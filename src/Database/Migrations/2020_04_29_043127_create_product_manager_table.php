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
    }
}
