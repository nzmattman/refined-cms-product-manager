<?php

namespace RefinedDigital\ProductManager\Module\Classes;

use RefinedDigital\FormBuilder\Module\Contracts\FormBuilderCallbackInterface;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\EmailRepository;
use RefinedDigital\FormBuilder\Module\Http\Repositories\FormsRepository;
use RefinedDigital\FormBuilder\Module\Http\Repositories\FormBuilderRepository;
use RefinedDigital\ProductManager\Module\Http\Repositories\OrderRepository;


class Process implements FormBuilderCallbackInterface {

    public function run($request, $form) {
        $cart = cart()->get();
        $gateway = paymentGateways()->get($request->get('payment_gateway'));

        $emailRepo = new EmailRepository();
        $billingDetails = '<h3>Billing Details</h3>'.$emailRepo->generateHtml($request, $form, 'margin: 0 auto');

        $orderDetails = '<h3>Order Details</h3>'.view()->make('products::emails.elements.cart')->with(compact('cart'))->render();

        $formBuilderRepository = new FormBuilderRepository();
        $formRepo = new FormsRepository($formBuilderRepository);
        $fieldsByName = $formRepo->formatFieldsByName($request, $form);

        $orderRepo = new OrderRepository();
        $order = $orderRepo->create($fieldsByName, $cart, $request->get('payment_gateway'));

        $emailData = new \stdClass();
        $emailData->request = $emailRepo->setDataForDB($request);
        $emailData->cart = $cart;
        // remove any cc details
        if (isset($emailData->request['c'])) {
            unset($emailData->request['c']);
        }

        // todo: off site varification stuff
        if ($gateway) {

            $response = $gateway
                ->setTotal($cart->totals->total)
                ->setDescription('Order #'.str_pad($order->id, 4, 0, STR_PAD_LEFT))
                ->setMetaData([
                    'name' => $fieldsByName->{'First Name'}.' '.$fieldsByName->{'Last Name'},
                    'address' => implode(', ', $gateway->formatAddress($fieldsByName)),
                    'phone' => $fieldsByName->Phone,
                    'email' => $fieldsByName->Email,
                ])
                ->setCurrency(config('products.orders.currency'))
                ->setTypeId($order->id)
                ->setTypeDetails(get_class($order))
                ->process($request, $form, $emailData);

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

        exit();

        // send the receipt email
        $this->sendEmail($orderDetails, $billingDetails, $order, $fieldsByName, $request, $form, $emailData, 'receipt_');

        // send the admin email
        $this->sendEmail($orderDetails, $billingDetails, $order, $fieldsByName, $request, $form, $emailData);

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

    private function sendEmail($orderDetails, $billingDetails, $order, $fields, $request, $form, $emailData, $key = '')
    {
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

        $html = str_replace($search, $replace, $form->{$key.'message'});
        $subject = str_replace($search, $replace, $form->{$key.'subject'});

        $settings = $emailRepo->settingsFromForm($form, $request);
        $settings->body = $html;
        $settings->form_id = $form->id;
        $settings->data = $emailData;
        $settings->subject = $subject;
        $emailRepo->send($settings);
    }
}
