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
        Schema::table('oms_promotion_product_post_socials', function (Blueprint $table) {
            $table->renameColumn('product_post_id', 'organic_post_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oms_promotion_product_post_socials', function (Blueprint $table) {
            //
        });
    }
};
