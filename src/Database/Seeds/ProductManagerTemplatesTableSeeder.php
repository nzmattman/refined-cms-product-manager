<?php

namespace RefinedDigital\ProductManager\Database\Seeds;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class ProductManagerTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templates = [
            [
                'name'      => 'Products',
                'source'    => 'products',
                'active'    => 1,
            ],
            [
                'name'      => 'Product Category Details',
                'source'    => 'product-category-details',
                'active'    => 0,
            ],
            [
                'name'      => 'Product Details',
                'source'    => 'product-details',
                'active'    => 0,
            ],
            [
                'name'      => 'Order Thank You',
                'source'    => 'order-thank-you',
                'active'    => 1,
            ],
            [
                'name'      => 'Cart',
                'source'    => 'cart',
                'active'    => 1,
            ],
            [
                'name'      => 'Checkout',
                'source'    => 'checkout',
                'active'    => 1,
            ],
        ];

        foreach($templates as $pos => $u) {
            $args = [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'position' => $pos,
            ];
            $data = array_merge($args, $u);
            DB::table('templates')->insert($data);
        }
    }
}
