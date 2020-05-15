<?php

namespace RefinedDigital\ProductManager\Database\Seeds;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;
use RefinedDigital\FormBuilder\Module\Fields\FormField;
use RefinedDigital\FormBuilder\Module\Models\Form;
use RefinedDigital\FormBuilder\Module\Models\FormFieldOption;

class ProductFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $formPosition = Form::count();
        $formData = [
            'active' => true,
            'position' => $formPosition + 1,
            'form_action' => 2,
            'recaptcha' => 0,
            'name' => 'Checkout',
            'subject' => 'New Order #[[order_number]]',
            'email_to' => 'matthias@refineddigital.co.nz',
            'reply_to' => null,
            'cc' => null,
            'bcc' => null,
            'callback' => 'RefinedDigital\ProductManager\Module\Classes\Process',
            'model' => null,
            'message' => '<h1>You have received a new order.</h1><p>[[first_name]] has just placed an order.</p><p>Order Number <strong>#[[order_number]]</strong>, and is now ready for processing.</p><p>[[order_details]]</p><p>[[billing_details]]</p>',
            'confirmation' => '<p>Not Used</p>',
            'redirect_page' => '/cart/checkout/thank-you',
            'receipt' => 0,
            'receipt_subject' => null,
            'receipt_message' => null,

        ];

        $form = Form::create($formData);

        $fields = [
            [
                'form_field_type_id' => 1,
                'name' => 'First Name',
                'required' => 1,
                'note' => null,
                'custom_class' => 'cart__field--first-name',
            ],
            [
                'form_field_type_id' => 1,
                'name' => 'Last Name',
                'required' => 1,
                'note' => null,
                'custom_class' => 'cart__field--last-name',
            ],
            [
                'form_field_type_id' => 1,
                'name' => 'Company Name',
                'required' => 0,
                'note' => null,
                'custom_class' => 'cart__field--company-name',
            ],
            [
                'form_field_type_id' => 1,
                'name' => 'Address',
                'required' => 1,
                'note' => null,
                'custom_class' => 'cart__field--address',
            ],
            [
                'form_field_type_id' => 1,
                'name' => 'Address 2',
                'required' => 0,
                'note' => null,
                'custom_class' => 'cart__field--address-2',
            ],
            [
                'form_field_type_id' => 1,
                'name' => 'Suburb',
                'required' => 1,
                'note' => null,
                'custom_class' => 'cart__field--suburb',
            ],
            [
                'form_field_type_id' => 3,
                'name' => 'State',
                'required' => 1,
                'note' => null,
                'custom_class' => 'cart__field--state',
                'options' => [
                    [ 'value' => 'ACT', 'label' => 'Australian Capital Territory'],
                    [ 'value' => 'NSW', 'label' => 'New South Wales'],
                    [ 'value' => 'NT', 'label' => 'Northern Territory'],
                    [ 'value' => 'QLD', 'label' => 'Queensland'],
                    [ 'value' => 'SA', 'label' => 'South Australia'],
                    [ 'value' => 'TAS', 'label' => 'Tasmania'],
                    [ 'value' => 'VIC', 'label' => 'Victoria'],
                    [ 'value' => 'WA', 'label' => 'Western Australia'],
                ]
            ],
            [
                'form_field_type_id' => 7,
                'name' => 'Postcode',
                'required' => 1,
                'note' => null,
                'custom_class' => 'cart__field--postcode',
            ],
            [
                'form_field_type_id' => 9,
                'name' => 'Phone',
                'required' => 1,
                'note' => null,
                'custom_class' => 'cart__field--phone',
            ],
            [
                'form_field_type_id' => 8,
                'name' => 'Email',
                'required' => 1,
                'note' => null,
                'custom_class' => 'cart__field--email',
            ],
            [
                'form_field_type_id' => 2,
                'name' => 'Additional Information',
                'required' => 0,
                'note' => 'Order notes',
                'custom_class' => 'cart__field--additional-information',
            ],
        ];

        foreach ($fields as $pos => $field) {
            $data = [
                'form_id' => $form->id,
                'active' => true,
                'show_label' => true,
                'position' => $pos,
                'required' => null,
                'placeholder' => null,
                'data' => null,
                'custom_field_class' => null,
                'store_in' => null,
                'label_position' => 1,
                'autocomplete' => 1,
            ];

            $insert = array_merge($field, $data);
            $options = null;
            if ($insert['options']) {
                $options = $insert['options'];
                unset($insert['options']);
            }

            $field = FormField::create($insert);
            if ($options) {
                $optionCount = FormFieldOption::count();
                foreach ($options as $option) {
                    $option['form_field_id'] = $field->id;
                    $option['position'] = $optionCount;
                    FormFieldOption::create($option);
                    $optionCount ++;
                }
            }

            if ($field->name === 'Email') {
                $form->reply_to = $field->id;
                $form->save();
            }
        }
    }
}
