<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderStatusesTable extends Migration
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
            $table->boolean('active')->default(1);
            $table->boolean('send_email')->default(1);
            $table->boolean('send_sms')->default(0);
            $table->integer('position');
            $table->string('name');
            $table->string('email_subject')->nullable();
            $table->longText('email_content')->nullable();
            $table->string('sms_content')->nullable();
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
