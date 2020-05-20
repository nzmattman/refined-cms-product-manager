<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderExtraFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_extra_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('order_id');
            $table->string('name');
            $table->float('value')->nullable();
            $table->integer('percent')->nullable();
            $table->float('total');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_extra_fees');
    }
}
