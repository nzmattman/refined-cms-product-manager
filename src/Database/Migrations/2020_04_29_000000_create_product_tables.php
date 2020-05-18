<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTables extends Migration
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
            $table->integer('product_variation_type_id');
            $table->integer('position');
            $table->string('name');
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
            $table->integer('product_status_id');
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
            $table->json('available_days')->nullable();
            $table->json('conditions')->nullable();
        });

        Schema::create('product_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('active')->default(1);
            $table->integer('position');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('products');
        Schema::dropIfExists('related_products');
        Schema::dropIfExists('product_variation_types');
        Schema::dropIfExists('product_variation_type_values');
        Schema::dropIfExists('product_product_variation_type');
        Schema::dropIfExists('product_product_variation_type_value');
        Schema::dropIfExists('delivery_zones');
        Schema::dropIfExists('product_statuses');
    }
}
