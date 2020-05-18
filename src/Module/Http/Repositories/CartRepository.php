<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;


class CartRepository {
    protected $sessionKey = 'cart';

    public function get()
    {
        $cart = session()->get('cart');
        if (!$cart) {
            $cart               = new \stdClass();
            $cart->items        = collect([]);
            $cart->discount     = null; // name of discount applied
            $cart->delivery     = null; // name of delivery applied
            $cart->extra_fees   = [];
            $cart->totals       = new \stdClass();

            $cart->totals->discount     = 0;
            $cart->totals->sub_total    = 0;
            $cart->totals->delivery     = 0;
            $cart->totals->extra_fees   = 0;
            $cart->totals->gst          = 0;
            $cart->totals->total        = 0;
            $cart->totals->quantity     = 0;

            session()->put($this->sessionKey, $cart);
        }

        // adjust the urls for the products
        if ($cart->items->count()) {
            $cart->totals->quantity = 0;
            foreach ($cart->items as $item) {
                if (isset($item->product->uri)) {
                    if (config('products.cart.product_link.active')) {
                        $uri = [
                            ltrim(rtrim(config('app.url'), '/'), '/'),
                            ltrim(rtrim(config('products.cart.product_link.base_url'), '/'), '/'),
                            ltrim(rtrim($item->product->uri, '/'), '/'),
                        ];
                        $uri = array_filter($uri);
                        $item->product->url = implode('/', $uri);
                    }
                }
                $cart->totals->quantity += $item->quantity;
                $item->total = $item->price * $item->quantity;
            }
        }

        return $cart;
    }

    public function clear()
    {
        session()->forget($this->sessionKey);
    }

    public function add($product, $request)
    {
        $item = $this->findItem($product, $this->getVariationsFromRequest($request));
        $item->quantity += $request->get('quantity') ?: 1;

        $this->update($item);
    }

    public function remove($itemKey)
    {
        $cart = $this->get();
        $item = $this->getItemByKey($itemKey);
        $itemCollectionKey = $this->findItemCollectionKey($cart, $item);

        if (is_numeric($itemCollectionKey)) {
            $cart->items->splice($itemCollectionKey, 1);
        }

        session()->put($this->sessionKey, $cart);

        $this->updateTotals();
    }

    public function updateQuantity($request)
    {
        $itemKey = $request->get('key');

        if ($request->get('quantity') < 1) {
            $this->remove($itemKey);
        } else {
            $item = $this->getItemByKey($itemKey);
            $item->quantity = $request->get('quantity') ?: 1;
            $this->update($item);
        }
    }

    public function setDeliveryZone($zone, $postcode)
    {
        $cart = $this->get();
        $delivery = new \stdClass();
        $delivery->zone = $zone;
        $delivery->postcode = $postcode;
        $cart->delivery = $delivery;

        session()->put($this->sessionKey, $cart);

        $this->updateTotals();
    }

    private function update($item)
    {

        $cart = $this->get();

        $itemCollectionKey = $this->findItemCollectionKey($cart, $item);

        if (!is_numeric($itemCollectionKey)) {
            $cart->items->push($item);
        } else {
            $cart->items->splice($itemCollectionKey, 1, [$item]);
        }

        session()->put($this->sessionKey, $cart);

        $this->updateTotals();
    }

    private function getItemPrice($product, $variationKey = false, $key = 'price')
    {
        if ($variationKey) {
            $variation = $product->variation_type_values[$variationKey];
            if (isset($variation->{$key})) {
                return $variation->{$key};
            }
        }

        return $product->{$key};
    }

    private function findItem($product, $variations)
    {
        $cart = $this->get();
        $itemKey = $this->getItemKey($product, $variations);
        $item = $cart->items->first(function ($value) use($itemKey) {
            if ($value->key === $itemKey) {
                return $value;
            }
        });

        if (!$item) {
            return $this->createItem($product, $variations);
        }

        return $item;
    }

    private function findItemCollectionKey($cart, $item)
    {
        return $cart->items->search(function($record) use($item) {
            return $record->key === $item->key;
        });
    }

    private function createItem($product, $variations = false)
    {
        $pro = new \stdClass();
        $pro->id = $product->id;
        $pro->name = $product->name;
        $pro->code = $product->code;
        $pro->image = asset(image()
            ->load($product->image)
            ->width(config('products.cart.image.width'))
            ->height(config('products.cart.image.height'))
            ->string())
        ;
        $pro->uri = $product->meta->uri;

        $variationKey = $this->getVariationKeys($variations);
        $item = new \stdClass();
        $item->key = $this->getItemKey($product, $variations);
        $item->variationKey = $variationKey;
        $item->product = $pro;
        $item->variations = $this->findVariation($product, $variationKey);
        $item->quantity = 0;

        $prices = new \stdClass();
        $prices->price = (float) $this->getItemPrice($product, $variationKey);
        $prices->sale_price = (float) $this->getItemPrice($product, $variationKey, 'sale_price');
        $item->prices = $prices;
        $item->price = $prices->sale_price ?: $prices->price;

        return $item;
    }

    private function getVariationKeys($variations)
    {
        if (!$variations) {
            return null;
        }

        return implode(',', array_pluck($variations, 'id'));
    }

    private function getItemKey($product, $variations = false)
    {
        $itemKey = 'product:'.$product->id;
        if ($variations && is_array($variations)) {
            foreach($variations as $variation) {
                $itemKey .= '-variation:'.$variation->id;
            }
        }

        return $itemKey;
    }

    private function findVariation($product, $variationKey)
    {
        $variationRepo = new VariationRepository();
        return $variationRepo->findVariationsByKeys($product, $variationKey);
    }

    private function updateTotals()
    {
        $cart = $this->get();
        $totals = $cart->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
        $cart->totals->sub_total = $totals;

        // add the discount
        if ($cart->discount) {
            $discount = $cart->discount->amount;
            $totals -= $discount;
            $cart->totals->discount = $discount;
        }

        // add the shipping
        if (config('products.orders.active') && $cart->delivery) {
            $delivery = $cart->delivery->zone->price;
            if (isset($cart->delivery->zone->delivery_conditions) && sizeof($cart->delivery->zone->delivery_conditions)) {
                foreach ($cart->delivery->zone->delivery_conditions as $condition) {
                    $field = $cart->totals->{$condition->option};
                    if ($condition->is === '>') {
                        if ($field > $condition->value) {
                            $delivery = $condition->price;
                        }
                    }
                    if ($condition->is === '<') {
                        if ($field < $condition->value) {
                            $delivery = $condition->price;
                        }
                    }
                    if ($condition->is === '==') {
                        if ($field == $condition->value) {
                            $delivery = $condition->price;
                        }
                    }
                }
            }
            $totals += $delivery;
            $cart->totals->delivery = $delivery;
        }

        // add any additional fees.. if set
        $cartConfig = config('products.cart');
        if (isset($cartConfig['extra_fees']) && is_array($cartConfig['extra_fees'])) {
            $cart->extra_fees = [];
            $cart->totals->extra_fees = 0;
            foreach ($cartConfig['extra_fees'] as $fee) {
                if (isset($fee['percent'])) {
                    $rate = $fee['percent'] / 100;
                    $rateTotal = $totals * $rate;
                }

                if (isset($fee['value'])) {
                    $rateTotal = $fee['value'];
                }

                $fee['total'] = $rateTotal;
                $cart->extra_fees[] = $fee;

                $cart->totals->extra_fees += $rateTotal;
            }

            $totals += $cart->totals->extra_fees;
        }

        // add the gst
        $orderConfig = config('products.orders');
        $gst = 0;
        if ($orderConfig['gst']['active']) {
            $rate = $orderConfig['gst']['percent'] / 100;
            $gst = $totals * $rate;
        }

        $cart->totals->gst = $gst;
        if ($gst > 0 && $orderConfig['gst']['type'] === 'ex') {
            $totals += $gst;
        }

        // final total
        $cart->totals->total = $totals;

        session()->put($this->sessionKey, $cart);
    }

    private function getItemByKey($itemKey)
    {
        $cart = $this->get();
        return $cart->items->first(function ($value) use($itemKey) {
            if ($value->key === $itemKey) {
                return $value;
            }
        });
    }


    private function getVariationsFromRequest($request)
    {
        $variations = $request->get('variations');
        if (!is_array($variations)) {
            return null;
        }

        return array_map(function ($variation) {
            return json_decode($variation);
        }, $variations);
    }

    public function getResponse($request, $message = false)
    {
        $with = [
            'success' => true
        ];
        if ($message) {
            $with['message'] = $message;
        }
        if ($request->ajax()) {
            return response()
                ->json($with);
        } else {

            if ($request->has('redirectToCheckout') && $request->get('redirectToCheckout')) {
                // todo: auto grab the checkout page based on product template
                return redirect()
                    ->to('/checkout')
                    ->with($with);
            }

            if ($request->has('redirectToCart') && $request->get('redirectToCart')) {
                // todo: auto grab the cart page based on product template
                return redirect()
                    ->to('/cart')
                    ->with($with);
            }

            if ($request->has('redirectTo') && $request->get('redirectTo')) {
                return redirect()
                    ->to($request->get('redirectTo'))
                    ->with($with);
            }

            return redirect()
                ->back()
                ->with($with);
        }
    }
}
