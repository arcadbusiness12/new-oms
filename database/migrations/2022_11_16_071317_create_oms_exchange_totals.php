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
        Schema::create('oms_exchange_totals', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->index();
            $table->string('code', 32);
            $table->string('title', 255);
            $table->decimal('value', 15,4)->default(0.0000);
            $table->integer('sort_order');
            $table->string('text', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // public function down()
    // {
    //     Schema::dropIfExists('oms_order_totals');
    // }
};
