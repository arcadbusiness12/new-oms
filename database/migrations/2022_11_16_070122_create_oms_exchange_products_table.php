<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oms_exchange_products', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 50)->index();
            $table->integer('product_id')->unsigned()->index();
            // $table->foreign('product_id')->references('product_id')->on('oms_inventory_product');
            $table->integer('store_id')->unsigned()->index();
            // $table->foreign('store_id')->references('id')->on('store');
            $table->string('sku', 50)->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10,4);
            $table->decimal('total', 10,4);
            $table->decimal('tax', 10,4);
            $table->integer('product_option_id')->unsigned()->index();
            $table->string('option_name')->nullable();
            $table->string('option_value', 50);
            $table->integer('reward');
            $table->tinyInteger('is_return')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // public function down()
    // {
    //     Schema::dropIfExists('oms_order_products');
    // }
};
