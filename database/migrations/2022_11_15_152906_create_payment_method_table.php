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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('code', 10);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->integer('country_id');
            $table->string('name', 255);
            $table->integer('amount');
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
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('shipping_methods');
    }
};
