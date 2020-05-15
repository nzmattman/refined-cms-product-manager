<?php

namespace RefinedDigital\ProductManager\Database\Seeds;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class ProductStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $statuses = [
            'Available',
            'Limited Stock',
            'Sold Out',
        ];

        foreach($statuses as $pos => $u) {
            $args = [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'active' => true,
                'name' => $u,
                'position' => $pos
            ];
            DB::table('product_statuses')->insert($args);
        }
    }
}
