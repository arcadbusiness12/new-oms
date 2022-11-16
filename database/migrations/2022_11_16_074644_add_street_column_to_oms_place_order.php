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
            $table->text('payment_street_building')->after('payment_address_2');
            $table->text('payment_villa_flat')->after('payment_street_building');
            $table->text('shipping_street_building')->after('shipping_address_2');
            $table->text('shipping_villa_flat')->after('shipping_street_building');
            $table->text('comment')->nullable()->after('total_amount');
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
