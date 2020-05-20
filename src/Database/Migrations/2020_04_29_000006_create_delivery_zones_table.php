<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
            $table->json('available_days')->nullable();
            $table->json('conditions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_zones');
    }
}
