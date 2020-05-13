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

        $formBuilderRepository = new FormBuilderRepository();
        $formRepo = new FormsRepository($formBuilderRepository);
        $fieldsByName = $formRepo->formatFieldsByName($request, $form);

        $orderRepo = new OrderRepository();
        $order = $orderRepo->create($fieldsByName, $cart, $request->get('payment_gateway'));

        $emailRepo = new EmailRepository();
        $billingDetails = '<h3>Billing Details</h3>'.$emailRepo->generateHtml($request, $form, 'margin: 0 auto');

        $orderDetails = '<h3>Order Details</h3>'.view()->make('products::emails.elements.cart')->with(compact('cart'))->render();

        $search = [
            '[[first_name]]',
            '[[last_name]]',
            '[[order_number]]',
            '[[order_details]]',
            '[[billing_details]]'
        ];
        $replace = [
            isset($fieldsByName->{'First Name'}) ? $fieldsByName->{'First Name'} : '',
            isset($fieldsByName->{'Last Name'}) ? $fieldsByName->{'Last Name'} : '',
            '',
            $orderDetails,
            $billingDetails
        ];

        $html = str_replace($search, $replace, $form->message);

        help()->trace($gateway);

        if ($gateway) {
            help()->trace('process the payment');
        }

        help()->trace($html);;

        help()->trace($request->all());
        help()->trace($form->toArray());

        exit();
    }
}
