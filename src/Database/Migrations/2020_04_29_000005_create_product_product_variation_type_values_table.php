<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createProductProductVariationTypeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_product_variation_type_value', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->longText('product_variation_type_value_ids');
            $table->integer('product_status_id');
            $table->float('price')->nullable();
            $table->float('sale_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('product_product_variation_type_value');
    }
}
