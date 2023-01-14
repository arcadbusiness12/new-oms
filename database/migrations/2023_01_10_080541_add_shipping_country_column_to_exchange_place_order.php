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
        Schema::table('oms_place_exchanges', function (Blueprint $table) {
            $table->string('shipping_country')->after('shipping_country_id');
            $table->integer('payment_method_id')->after('shipping_country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oms_place_exchanges', function (Blueprint $table) {
            //
        });
    }
};
