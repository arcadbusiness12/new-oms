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
            $table->integer('payment_method_id')->unsigned()->index()->after('shipping_city_area_id');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->integer('shipping_method_id')->unsigned()->index()->after('payment_method_id');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods');
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
