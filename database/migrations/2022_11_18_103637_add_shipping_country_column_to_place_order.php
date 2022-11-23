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
            $table->string('shipping_country',100)->nullable()->after('shipping_city_area');
            $table->string('payment_method_name',100)->nullable()->after('payment_method_id');
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
