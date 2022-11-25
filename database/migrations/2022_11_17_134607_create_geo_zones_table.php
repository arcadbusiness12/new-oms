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
        Schema::create('geo_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('geo_zone_to_zones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('geo_zone_id')->unsigned()->index();
            $table->integer('country_id')->unsigned()->index();
            $table->integer('city_id')->unsigned()->index();
            $table->foreign('geo_zone_id')->references('id')->on('geo_zones');
            // $table->foreign('country_id')->references('id')->on('countries');
            // $table->foreign('city_id')->references('id')->on('cities');
            $table->timestamps();
        });
        
        Schema::create('zone_areas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('zone_id')->unsigned()->index();
            $table->foreign('zone_id')->references('id')->on('geo_zone_to_zones');
            $table->integer('city_id')->unsigned()->index()->nullable();
            // $table->foreign('city_id')->references('id')->on('cities');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo_zones');
    }
};
