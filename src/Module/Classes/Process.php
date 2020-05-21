<?php

namespace RefinedDigital\ProductManager\Module\Classes;

use RefinedDigital\FormBuilder\Module\Contracts\FormBuilderCallbackInterface;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\EmailRepository;
use RefinedDigital\FormBuilder\Module\Http\Repositories\FormsRepository;
use RefinedDigital\FormBuilder\Module\Http\Repositories\FormBuilderRepository;
use RefinedDigital\ProductManager\Module\Events\OrderStatusUpdatedEvent;
use RefinedDigital\ProductManager\Module\Http\Repositories\OrderRepository;
use Carbon\Carbon;


class Process implements FormBuilderCallbackInterface {

    public function run($request, $form) {
        $cart = cart()->get();
        $gateway = paymentGateways()->get($request->get('payment_gateway'));
        $emailRepo = new EmailRepository();

        $formBuilderRepository = new FormBuilderRepository();
        $formRepo = new FormsRepository($formBuilderRepository);
        $fields = $formRepo->formatFieldsByName($request, $form);

        $data = new \stdClass();
        $data->cart = $cart;
        $data->request = $emailRepo->setDataForDB($request);
        $data->form = $form;
        $data->fields = $fields;
        // remove any cc details
        if (isset($data->request['c'])) {
            unset($data->request['c']);
        }

        $orderRepo = new OrderRepository();
        $order = $orderRepo->create($fields, $cart, $request->get('payment_gateway'), $data);
        $orderDetails = $orderRepo->generateOrderDetailsHtml($order);
        $billingDetails = $orderRepo->generateBillingDetailsHtml($order);

        help()->trace($fields);
        help()->trace($request->all());
        help()->trace($billingDetails);

        exit();

        // todo: off site varification stuff
        if ($gateway) {

            $response = $gateway
                ->setTotal($cart->totals->total)
                ->setDescription('Order #'.str_pad($order->id, 4, 0, STR_PAD_LEFT))
                ->setMetaData([
                    'name' => $fields->{'First Name'}.' '.$fields->{'Last Name'},
                    'address' => implode(', ', $gateway->formatAddress($fields)),
                    'phone' => $fields->Phone,
                    'email' => $fields->Email,
                ])
                ->setCurrency(config('products.orders.currency'))
                ->setTypeId($order->id)
                ->setTypeDetails(get_class($order))
                ->process($request, $form, $data);

            if (!$response->success) {
                $validator = \Validator::make(
                    ['check' => ''],
                    ['check' => 'required'],
                    ['check.required' => $response->message]
                );
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', true);
            }
        }

        $order->paid_at = Carbon::now();
        $order->save();

        // send the notifications
        event(new OrderStatusUpdatedEvent($order, 1));

        session()->flash('content', [
            'search' => [
                '[[order_details]]',
                '[[billing_details]]'
            ],
            'replace' => [
                $orderDetails,
                $billingDetails
            ]
        ]);

        cart()->clear();
    }
}
