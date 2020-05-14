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
            'Processing',
            'Completed',
            'On Hold',
            'Cancelled',
            'Failed',
            'Refunded',
            'Pending Payment',
        ];

        foreach($statuses as $pos => $u) {
            $args = [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'active' => 1,
                'position' => $pos,
                'name' => $u
            ];
            DB::table('order_statuses')->insert($args);
        }
    }
}
