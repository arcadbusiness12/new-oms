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
        Schema::table('oms_inventory_product_specials', function (Blueprint $table) {
            $table->date('date_start')->default('0000-00-00')->change();
            $table->date('date_end')->default('0000-00-00')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oms_inventory_product_specials', function (Blueprint $table) {
            //
        });
    }
};
