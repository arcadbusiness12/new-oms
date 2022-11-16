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
        Schema::table('oms_place_order', function (Blueprint $table) {
            $table->integer('customer_id')->unsigned()->index()->after('store');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->integer('payment_country_id')->unsigned()->index()->after('customer_id')->nullable();
            $table->foreign('payment_country_id')->references('id')->on('countries');
            $table->integer('payment_city_id')->unsigned()->index()->after('payment_country_id')->nullable();
            $table->foreign('payment_city_id')->references('id')->on('cities');
            $table->integer('payment_city_area_id')->unsigned()->index()->after('payment_city_id')->nullable();
            $table->foreign('payment_city_area_id')->references('id')->on('city_areas');
            $table->integer('shipping_country_id')->unsigned()->index()->after('payment_city_area_id')->nullable();
            $table->foreign('shipping_country_id')->references('id')->on('countries');
            $table->integer('shipping_city_id')->unsigned()->index()->after('shipping_country_id')->nullable();
            $table->foreign('shipping_city_id')->references('id')->on('cities');
            $table->integer('shipping_city_area_id')->unsigned()->index()->after('shipping_city_id')->nullable();
            $table->foreign('shipping_city_area_id')->references('id')->on('city_areas');
            $table->string('firstname', 32)->after('shipping_city_area_id');
            $table->string('lastname', 32)->after('firstname');
            $table->string('email', 100)->after('lastname');
            $table->string('mobile', 32)->after('email');
            $table->string('payment_firstname')->after('mobile')->nullable();
            $table->string('payment_lastname', 32)->after('payment_firstname')->nullable();
            $table->text('payment_address_1')->after('payment_lastname')->nullable();
            $table->text('payment_address_2')->after('payment_address_1')->nullable();
            $table->string('payment_city', 100)->after('payment_address_2')->nullable();
            $table->text('payment_city_area')->after('payment_city')->nullable();
            $table->string('payment_country', 100)->after('payment_city_area')->nullable();
            $table->string('shipping_firstname')->after('payment_country')->nullable();
            $table->string('shipping_lastname', 32)->after('shipping_firstname')->nullable();
            $table->text('shipping_address_1')->after('shipping_lastname')->nullable();
            $table->text('shipping_address_2')->after('shipping_address_1')->nullable();
            $table->string('shipping_city', 100)->after('shipping_address_2')->nullable();
            $table->text('shipping_city_area')->after('shipping_city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oms_place_order', function (Blueprint $table) {
            //
        });
    }
};
