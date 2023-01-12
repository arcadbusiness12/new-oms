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
        Schema::table('oms_exchange_return_products', function (Blueprint $table) {
            $table->decimal('total',10,4)->after('quantity');
            $table->integer('product_option_id')->after('total');
            $table->string('option_name')->after('product_option_id');
            $table->string('option_value')->after('option_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oms_exchange_return_products', function (Blueprint $table) {
            //
        });
    }
};
