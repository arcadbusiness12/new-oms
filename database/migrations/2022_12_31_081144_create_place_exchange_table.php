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
        Schema::create('oms_place_exchanges', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->unsigned()->index();
            $table->integer('customer_id')->unsigned()->index();
            // $table->foreign('customer_id')->references('id')->on('customers');
            $table->integer('payment_country_id')->unsigned()->index()->nullable();
            // $table->foreign('payment_country_id')->references('id')->on('countries');
            $table->integer('payment_city_id')->unsigned()->index()->nullable();
            // $table->foreign('payment_city_id')->references('id')->on('cities');
            $table->integer('payment_city_area_id')->unsigned()->index()->nullable();
            // $table->foreign('payment_city_area_id')->references('id')->on('city_areas');
            $table->integer('shipping_country_id')->unsigned()->index()->nullable();
            // $table->foreign('shipping_country_id')->references('id')->on('countries');
            $table->integer('shipping_city_id')->unsigned()->index()->nullable();
            // $table->foreign('shipping_city_id')->references('id')->on('cities');
            $table->integer('shipping_city_area_id')->unsigned()->index()->nullable();
            // $table->foreign('shipping_city_area_id')->references('id')->on('city_areas');
            $table->string('firstname', 32);
            $table->string('lastname', 32)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('mobile', 32);
            $table->string('payment_firstname')->nullable();
            $table->string('payment_lastname', 32)->nullable();
            $table->text('payment_address_1')->nullable();
            $table->text('payment_address_2')->nullable();
            $table->string('payment_city', 100)->nullable();
            $table->text('payment_city_area')->nullable();
            $table->string('payment_country', 100)->nullable();
            $table->string('shipping_firstname')->nullable();
            $table->string('shipping_lastname', 32)->nullable();
            $table->text('shipping_address_1')->nullable();
            $table->text('shipping_address_2')->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->text('shipping_city_area')->nullable();
            $table->text('payment_street_building');
            $table->text('payment_villa_flat');
            $table->text('shipping_street_building');
            $table->text('shipping_villa_flat');
            $table->text('comment')->nullable();
            $table->string('alternate_number',32)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // public function down()
    // {
    //     Schema::dropIfExists('oms_place_exchanges');
    // }
};
