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
            $table->decimal('total_amount', 15,4)->after('shipping_city_area')->nullable();
            $table->tinyInteger('online_approved')->after('total_amount');
            $table->integer('mobile_app_sale')->after('online_approved')->nullable();
            $table->tinyInteger('app_order_source')->after('mobile_app_sale')->nullable();
            $table->tinyInteger('otp_verify_login')->after('app_order_source')->nullable();
            $table->string('map_lat', 50)->after('otp_verify_login')->nullable();
            $table->string('map_long', 50)->after('map_lat')->nullable();
            $table->text('google_map_link', 100)->after('map_long')->nullable();
            $table->tinyInteger('reseller_approve')->after('google_map_link');
            $table->string('utm_source', 100)->after('reseller_approve')->nullable();
            $table->string('utm_medium', 100)->after('utm_source')->nullable();
            $table->string('utm_campaign', 100)->after('utm_medium')->nullable();
            $table->string('utm_content', 100)->after('utm_campaign')->nullable();
            $table->string('utm_term', 100)->after('utm_content')->nullable();
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
