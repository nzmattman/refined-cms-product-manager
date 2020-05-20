<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('order_status_id')->unsigned();
            $table->integer('user_id')->nullable();
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
            $table->json('data')->nullable();

            $table->foreign('order_status_id')->references('id')->on('order_statuses')->onDelete('cascade');
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
    }
}
