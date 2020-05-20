<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
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
            $table->boolean('hide_from_menu')->default(0);
            $table->boolean('for_sale')->default(1);
            $table->integer('product_status_id')->nullable();
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('products');
    }
}
