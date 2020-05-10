<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;

use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;

class CartRepository extends CoreRepository {
    protected $sessionKey = 'cart';

    public function get()
    {
        $cart = session()->get('cart');
        if (!$cart) {
            $cart            = new \stdClass();
            $cart->items     = collect([]);
            $cart->discount  = null; // name of discount applied
            $cart->delivery  = null; // name of delivery applied
            $cart->totals    = new \stdClass();

            $cart->totals->discount  = 0;
            $cart->totals->sub_total = 0;
            $cart->totals->delivery  = 0;
            $cart->totals->gst       = 0;
            $cart->totals->total     = 0;

            session()->put($this->sessionKey, $cart);
        }

        return $cart;
    }

    public function clear()
    {
        session()->forget($this->sessionKey);
    }

    public function add($product, $request)
    {
        $cart = $this->get();

        $item = $this->findItem($product, $request->get('variation'));
        $item->quantity += $request->get('quantity') ?: 1;

        $itemCollectionKey = $cart->items->search(function($record) use($item) {
            return $record->key === $item->key;
        });

        if (!is_numeric($itemCollectionKey)) {
            $cart->items->push($item);
        } else {
            $cart->items->splice($itemCollectionKey, 1, [$item]);
        }

        session()->put($this->sessionKey, $cart);

        $this->updateTotals();
    }

    public function remove($product, $request)
    {
        $cart = $this->get();
        $item = $this->findItem($product, $request->get('variation'));

        $itemCollectionKey = $cart->items->search(function($record) use($item) {
            return $record->key === $item->key;
        });

        if (is_numeric($itemCollectionKey)) {
            $cart->items->splice($itemCollectionKey, 1);
        }

        session()->put($this->sessionKey, $cart);

        $this->updateTotals();
    }

    public function getItemPrice($product, $variationKey = false, $key = 'price')
    {
        if ($variationKey) {
            $variation = $this->findVariation($product, $variationKey);
            if (isset($variation->{$key})) {
                return $variation->{$key};
            }
        }

        return $product->{$key};
    }

    private function findItem($product, $variation)
    {
        $cart = $this->get();
        $key = $this->getItemKey($product, $variation);
        $item = $cart->items->first(function ($value) use($key) {
            if ($value->key === $key) {
                return $value;
            }
        });

        if (!$item) {
            return $this->createItem($product, $variation);
        }

        return $item;
    }

    private function createItem($product, $variation = false)
    {
        $var = $this->findVariation($product, $variation);
        $item = new \stdClass();
        $item->id = $product->id;
        $item->key = $this->getItemKey($product, $variation);
        $item->variationKey = $variation ?: null;
        $item->product = $product->name;
        $item->variation = $var ? $var->name : null;
        $item->quantity = 0;
        $item->price = (float) $this->getItemPrice($product, $variation);
        $item->sale_price = (float) $this->getItemPrice($product, $variation, 'sale_price');

        return $item;
    }

    private function getItemKey($product, $variation = false)
    {
        $itemKey = 'product:'.$product->id;
        if ($variation) {
            $itemKey .= '-variation:'.$variation;
        }

        return $itemKey;
    }

    private function findVariation($product, $variationKey)
    {
        if ($variationKey && isset($product->variations) && sizeof($product->variations)) {
            $variation = array_first($product->variations, function ($value, $key) use ($variationKey) {
                return (int)$variationKey === (int)$key;
            });

            if ($variation) {
                $var = new \stdClass();
                foreach($variation as $key => $data) {
                    $var->{$key} = $data->content;
                }
                return $var;
            }

            return null;
        }

        return null;
    }

    private function updateTotals()
    {
        $cart = $this->get();
        $totals = $cart->items->sum(function($item) {
            if ($item->sale_price) {
                $price = $item->sale_price;
            } else {
                $price = $item->price;
            }

            return $price * $item->quantity;
        });
        $cart->totals->sub_total = $totals;

        // add the discount
        if ($cart->discount) {
            $discount = $cart->discount->amount;
            $totals -= $discount;
            $cart->totals->discount = $discount;
        }

        // add the shipping
        if ($cart->delivery) {
            $delivery = $cart->delivery->amount;
            $totals += $delivery;
            $cart->totals->delivery = $delivery;
        }


        // add the gst
        $config = config('product-manager.orders');
        $gst = 0;
        if ($config['gst']['active']) {
            $rate = $config['gst']['percent'] / 100;
            $gst = $totals * $rate;
        }

        $cart->totals->gst = $gst;
        if ($gst > 0 && $config['gst']['type'] === 'ex') {
            $totals += $gst;
        }

        // final total
        $cart->totals->total = $totals;

        session()->put($this->sessionKey, $cart);
    }

}
