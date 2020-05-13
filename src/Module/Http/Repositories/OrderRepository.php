<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;

use Carbon\Carbon;
use RefinedDigital\ProductManager\Module\Models\Order;
use RefinedDigital\ProductManager\Module\Models\OrderExtraFee;
use RefinedDigital\ProductManager\Module\Models\OrderProduct;
use RefinedDigital\ProductManager\Module\Models\OrderProductValue;

class OrderRepository {

    public function create($fields, $cart, $paymentMethod)
    {
        help()->trace($fields);

        $orderFields = [
            'paid_at' => Carbon::now(),
            'payment_method' => $paymentMethod
        ];
        foreach($fields as $key => $field) {
            $orderFields[snake_case($key)] = $field;
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

        help()->trace($orderFields);
        help()->trace($cart);

        exit();

        return $order;
    }

    public function addExtraFees($orderId, $extraFees = false)
    {
        if ($extraFees && sizeof($extraFees)) {
            foreach ($extraFees as $fee) {
                $fields = [
                    'order_id' => $orderId,
                    'name' => $fee['name'],
                    'amount' => isset($fee['amount']) ? $fee['name'] : null,
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
         // add the variation
          // add the values
    }
}
