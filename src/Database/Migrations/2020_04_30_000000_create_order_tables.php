<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('order_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('active')->boolean(1);
            $table->integer('position');
            $table->string('name');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('order_status_id')->unsigned();
            $table->timestamp('paid_at')->nullable();
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

            $table->foreign('order_status_id')->references('id')->on('order_statuses')->onDelete('cascade');
        });

        Schema::create('order_extra_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('order_id');
            $table->string('name');
            $table->float('value')->nullable();
            $table->integer('percent')->nullable();
            $table->float('total');
        });

        Schema::create('order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('order_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->float('price');
            $table->float('total');
        });

        Schema::create('order_product_variations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('order_product_id');
            $table->integer('variation_id');
            $table->integer('variation_value_id');
        });

        Schema::create('order_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('active')->default(1);
            $table->string('name');
            $table->longText('content');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_extra_fees');
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('order_product_variations');
        Schema::dropIfExists('order_statuses');
        Schema::dropIfExists('order_emails');
    }
}
