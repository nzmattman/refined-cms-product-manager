<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('active')->default(1);
            $table->boolean('featured_product')->default(1);
            $table->boolean('new')->default(0);
            $table->integer('position');
            $table->string('name');
            $table->string('code')->nullable();
            $table->integer('image')->nullable();
            $table->json('images')->nullable();
            $table->integer('file')->nullable();
            $table->json('files')->nullable();
            $table->longText('content')->nullable();
            $table->float('price')->nullable();
            $table->float('sale_price')->nullable();
            $table->json('variations')->nullable();
        });

        Schema::create('related_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('related_product_id');
        });

        Schema::create('product_variation_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->string('display_name')->nullable();
        });

        Schema::create('product_variation_type_values', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('position');
            $table->string('name');
            $table->integer('product_variation_type_id');
        });

        Schema::create('product_product_variation_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('product_variation_type_id');
        });

        Schema::create('product_product_variation_type_value', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->longText('product_variation_type_value_ids');
            $table->float('price')->nullable();
            $table->float('sale_price')->nullable();
        });

        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('active')->default(1);
            $table->integer('position');
            $table->string('name');
            $table->float('price')->nullable();
            $table->longText('postcodes')->nullable();
            $table->longText('notes')->nullable();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('paid_at');
            $table->string('payment_method')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();
            $table->string('address_2')->nullable();
            $table->string('suburb')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('additional_information')->nullable();
            $table->integer('delivery_zone_id')->nullable();
            $table->boolean('gst_active')->default(0);
            $table->string('gst_method')->nullable();
            $table->float('discount')->nullable();
            $table->float('sub_total')->nullable();
            $table->float('delivery')->nullable();
            $table->float('gst')->nullable();
            $table->float('total')->nullable();
        });

        Schema::create('order_extra_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->string('name');
            $table->float('amount')->nullable();
            $table->integer('percent')->nullable();
            $table->float('total');
        });

        Schema::create('order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->float('price');
            $table->float('total');
        });

        Schema::create('order_product_variations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_order_id');
            $table->integer('product_id');
            $table->integer('variation_id');
            $table->integer('variation_value_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('related_products');
        Schema::dropIfExists('product_variation_types');
        Schema::dropIfExists('product_variation_type_values');
        Schema::dropIfExists('product_product_variation_type');
        Schema::dropIfExists('product_product_variation_type_value');
        Schema::dropIfExists('delivery_zones');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_extra_fees');
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('order_product_variations');
    }
}
