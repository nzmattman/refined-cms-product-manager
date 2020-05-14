<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;

use Carbon\Carbon;
use RefinedDigital\ProductManager\Module\Models\Order;
use RefinedDigital\ProductManager\Module\Models\OrderExtraFee;
use RefinedDigital\ProductManager\Module\Models\OrderProduct;
use RefinedDigital\ProductManager\Module\Models\OrderProductVariation;

class OrderRepository {

    public function create($fields, $cart, $paymentMethod)
    {
        $orderFields = [
            'order_status_id' => 1, // processing
            'paid_at' => Carbon::now(),
            'payment_method' => $paymentMethod
        ];
        foreach($fields as $key => $field) {
            $fieldKey = snake_case($key);
            if ($fieldKey === 'address2') {
                $fieldKey = 'address_2';
            }
            $orderFields[$fieldKey] = $field;
        }

        if ($cart->delivery && $cart->delivery->zone) {
            $orderFields['delivery_zone_id'] = $cart->delivery->zone->id;
        }

        $gst = config('products.orders.gst');
        if ($gst['active']) {
            $orderFields['gst_active'] = $gst['active'];
            $orderFields['gst_method'] = $gst['type'];
        }

        if ($cart->totals->discount) {
            $orderFields['discount'] = $cart->totals->discount;
        }

        if ($cart->totals->sub_total) {
            $orderFields['sub_total'] = $cart->totals->sub_total;
        }

        if ($cart->totals->delivery) {
            $orderFields['delivery'] = $cart->totals->delivery;
        }

        if ($cart->totals->gst) {
            $orderFields['gst'] = $cart->totals->gst;
        }

        if ($cart->totals->total) {
            $orderFields['total'] = $cart->totals->total;
        }

        $order = Order::create($orderFields);

        $this->addExtraFees($order->id, $cart->extra_fees);

        $this->addProducts($order->id, $cart->items);

        return $order;
    }

    public function addExtraFees($orderId, $extraFees = false)
    {
        if ($extraFees && sizeof($extraFees)) {
            foreach ($extraFees as $fee) {
                $fields = [
                    'order_id' => $orderId,
                    'name' => $fee['name'],
                    'value' => isset($fee['value']) ? $fee['value'] : null,
                    'percent' => isset($fee['percent']) ? $fee['percent'] : null,
                    'total' => $fee['total'],
                ];
                OrderExtraFee::create($fields);
            }
        }
    }

    public function addProducts($orderId, $products)
    {
        // add the product
        if ($products->count()) {
            foreach ($products as $item) {
                $orderProduct = OrderProduct::create([
                    'order_id' => $orderId,
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total
                ]);

                if (isset($item->variations) && is_array($item->variations) && sizeof($item->variations)) {
                    foreach ($item->variations as $variation) {
                        OrderProductVariation::create([
                            'order_product_id' => $orderProduct->id,
                            'variation_id' => $variation->id,
                            'variation_value_id' => $variation->value_id,
                        ]);
                    }
                }
            }
        }
    }
}
