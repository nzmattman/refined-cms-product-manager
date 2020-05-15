<?php

namespace RefinedDigital\ProductManager\Database\Seeds;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $statuses = [
            [
                'name' => 'Processing',
                'email_subject' => 'Your order has been received',
                'email_content' => '<h1>Thank you for your order</h1><p>Hi [[first_name]],</p><p>Just to let you know - we\'ve received your order <strong>#[[order_number]]</strong>, and it is now being processed</p><p>[[order_details]]</p><p>[[billing_details]]</p><p>You will receive an email once your order has been completed and is ready for collection.</p><p>Thanks for shopping with us.</p>',
            ],
            [
                'name' => 'On Hold',
                'email_subject' => null,
                'email_content' => null,
            ],
            [
                'name' => 'Cancelled',
                'email_subject' => 'Your order #[[order_number]] has been cancelled',
                'email_content' => '<h1>Your order has been cancelled</h1><p>Hi [[first_name]],</p><p>Your order <strong>#[[order_number]]</strong> has been cancelled.</p><p>If this is a mistake, please contact us.</p><p>[[order_details]]</p><p>[[billing_details]]</p><p>Thanks for shopping with us.</p>',
            ],
            [
                'name' => 'Failed',
                'email_subject' => null,
                'email_content' => null,
            ],
            [
                'name' => 'Completed',
                'email_subject' => 'Your order #[[order_number]] is now complete',
                'email_content' => '<h1>Thank you for shopping with us</h1><p>Hi [[first_name]],</p><p>We have now finished processing your order <strong>#[[order_number]]</strong>.</p><p>[[order_details]]</p><p>[[billing_details]]</p><p>Thanks for shopping with us.</p>',
            ],
            [
                'name' => 'Shipped',
                'email_subject' => 'Your order #[[order_number]] has been shipped',
                'email_content' => '<h1>Your order has been shipped!</h1><p>Hi [[first_name]],</p><p>Your order <strong>#[[order_number]]</strong> is on its way.</p><p>[[order_details]]</p><p>[[billing_details]]</p><p>Thanks for shopping with us.</p>',
            ],
            [
                'name' => 'Ready for Pickup',
                'email_subject' => 'Your order #[[order_number]] is ready for collection',
                'email_content' => '<h1>Thank you for shopping with us</h1><p>Hi [[first_name]],</p><p>We have now finished processing your order <strong>#[[order_number]]</strong> and is ready for collection.</p><p>[[order_details]]</p><p>[[billing_details]]</p><p>Thanks for shopping with us.</p>',
            ],
            [
                'name' => 'Refunded',
                'email_subject' => 'Your order #[[order_number]] has been refunded',
                'email_content' => '<h1>Your order has been refunded</h1><p>Hi [[first_name]],</p><p>Your order <strong>#[[order_number]]</strong> has been refunded.</p><p>[[order_details]]</p><p>[[billing_details]]</p><p>We hope to see you again soon.</p>',
            ],
            [
                'name' => 'Pending Payment',
                'email_subject' => null,
                'email_content' => null,
            ],
        ];

        foreach($statuses as $pos => $u) {
            $args = [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'active' => true,
                'send_email' => $u['email_subject'] ? true : false,
                'send_sms' => 0,
                'position' => $pos,
                'name' => $u['name'],
                'email_subject' => $u['email_subject'],
                'email_content' => $u['email_content'],
            ];
            DB::table('order_statuses')->insert($args);
        }
    }
}
