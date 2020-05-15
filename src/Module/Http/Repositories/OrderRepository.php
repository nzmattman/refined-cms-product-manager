<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;

use Carbon\Carbon;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\EmailRepository;
use RefinedDigital\ProductManager\Module\Models\DeliveryZone;
use RefinedDigital\ProductManager\Module\Models\Order;
use RefinedDigital\ProductManager\Module\Models\OrderExtraFee;
use RefinedDigital\ProductManager\Module\Models\OrderProduct;
use RefinedDigital\ProductManager\Module\Models\OrderProductVariation;
use RefinedDigital\ProductManager\Module\Models\OrderStatus;

class OrderRepository {

    public function create($fields, $cart, $paymentMethod, $data)
    {
        $orderFields = [
            'order_status_id' => 1, // processing
            'paid_at' => Carbon::now(),
            'payment_method' => $paymentMethod,
            'data' => $data
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

    public function generateBillingDetailsHtml($order)
    {
        if (isset($order->id)) {
            $request = (array) $order->data->request;
            $form = $this->resetForm($order->data->form);

            $emailRepo = new EmailRepository();
            $html = '<h3>Billing Details</h3>';
            $html .= $emailRepo->generateHtml($request, $form, 'margin: 0 auto');
            return $html;
        }

        return null;
    }

    public function generateOrderDetailsHtml($order)
    {
        if (isset($order->id)) {
            $cart = $order->data->cart;
            $cart->items = collect($cart->items);

            $html = '<h3>Order Details</h3>';
            $html .= view()->make('products::emails.elements.cart')->with(compact('cart'))->render();

            return $html;
        }

        return null;
    }

    public function sendNotification($order, $notificationId)
    {
        $notification = OrderStatus::find($notificationId);
        $emailSubject = isset($notification->id) ? $notification->email_subject : null;
        $emailContent = isset($notification->id) ? $notification->email_content : null;
        $smsContent = isset($notification->id) ? $notification->sms_content : null;

        if ($notification->send_email) {
            $this->emailNotification($order, $emailSubject, $emailContent);
        }

        if ($notification->send_sms) {
            $this->smsNotification($order, $smsContent);
        }

        // send the admin notification
        if ($notificationId === 1) {
            $this->emailNotification($order, $order->data->form->subject, $order->data->form->message);
        }
    }

    private function emailNotification($order, $emailSubject, $emailContent)
    {
        $fields = $order->data->fields;
        $orderDetails = $this->generateOrderDetailsHtml($order);
        $billingDetails = $this->generateBillingDetailsHtml($order);
        $form = $this->resetForm($order->data->form);
        $request = (array) $order->data->request;

        $emailRepo = new EmailRepository();
        $search = [
            '[[first_name]]',
            '[[last_name]]',
            '[[order_number]]',
            '[[order_details]]',
            '[[billing_details]]'
        ];
        $replace = [
            isset($fields->{'First Name'}) ? $fields->{'First Name'} : '',
            isset($fields->{'Last Name'}) ? $fields->{'Last Name'} : '',
            isset($order->id) ? str_pad($order->id, 4, 0, STR_PAD_LEFT) : null,
            $orderDetails,
            $billingDetails.'<p>&nbsp;</p>'
        ];

        $subject = str_replace($search, $replace, $emailSubject);
        $html = str_replace($search, $replace, $emailContent);

        $settings = $emailRepo->settingsFromForm($form, $request);
        $settings->body = $html;
        $settings->form_id = $form->id;
        $settings->data = $order->data;
        $settings->subject = $subject;
        $emailRepo->send($settings);
    }

    // todo: complete this
    private function smsNotification($order, $smsContent)
    {
        $fields = $order->data->fields;
        $search = [
            '[[first_name]]',
            '[[last_name]]',
            '[[order_number]]',
        ];
        $replace = [
            isset($fields->{'First Name'}) ? $fields->{'First Name'} : '',
            isset($fields->{'Last Name'}) ? $fields->{'Last Name'} : '',
            isset($order->id) ? str_pad($order->id, 4, 0, STR_PAD_LEFT) : null,
        ];

        $message = str_replace($search, $replace, $smsContent);

        // todo: update this to use packages to be able to send the notifications via sms
        // Configure HTTP basic authorization: BasicAuth
        try {
            $config = \ClickSend\Configuration::getDefaultConfiguration()
                ->setUsername(env('CLICKSEND_USERNAME'))
                ->setPassword(env('CLICKSEND_PASSWORD'));

            $apiInstance = new \ClickSend\Api\SMSApi(new \GuzzleHttp\Client(),$config);
            $msg = new \ClickSend\Model\SmsMessage();
            $msg->setBody($message);
            $msg->setTo($order->phone);
            $msg->setSource('sdk');

            $smsMessages = new \ClickSend\Model\SmsMessageCollection();
            $smsMessages->setMessages([$msg]);
            $apiInstance->smsSendPost($smsMessages);
        } catch (\Exception $e) {
        }

    }


    private function resetForm($form)
    {
        if (is_array($form->fields) && sizeof($form->fields)) {
            foreach ($form->fields as $key => $field) {
                if ($field->select_options) {
                    $field->select_options = (array) $field->select_options;
                }
                if ($field->options) {
                    $field->options = (array) $field->options;
                }
                if ($field->attributes) {
                    $field->attributes = (array) $field->attributes;
                }

                $form->fields[$key] = $field;
            }
        }
        $form->fields = collect($form->fields);

        return $form;
    }

    public function getStatuses()
    {
        $statuses = [];
        $data = OrderStatus::orderBy('id', 'asc')->get();
        if ($data->count()) {
            foreach ($data as $d) {
                $statuses[$d->id] = $d->name;
            }
        }

        return $statuses;

    }

    public function getStatus($statusId)
    {
        $statuses = [];
        if (session()->has('order_statuses')) {
            $statuses = session()->get('order_statuses');
        } else {
            $data = OrderStatus::orderBy('id', 'asc')->get();
            if ($data->count()) {
                foreach ($data as $d) {
                    $statuses[$d->id] = $d;
                }
            }

            session()->flash('order_statuses', $statuses);
        }

        if (isset($statuses[$statusId])) {
            return $statuses[$statusId]->name;
        }

        return null;
    }

    public function getDeliveryZone($deliveryZoneId)
    {
        $zones = [];
        if (session()->has('delivery_zones')) {
            $zones = session()->get('delivery_zones');
        } else {
            $data = DeliveryZone::orderBy('id', 'asc')->get();
            if ($data->count()) {
                foreach ($data as $d) {
                    $zones[$d->id] = $d;
                }
            }

            session()->flash('delivery_zones', $zones);
        }

        if (isset($zones[$deliveryZoneId])) {
            return $zones[$deliveryZoneId]->name;
        }

        return null;
    }
}
