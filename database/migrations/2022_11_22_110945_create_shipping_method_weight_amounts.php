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
        Schema::create('shipping_method_weight_amounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shipping_method_id')->unsigned()->index();
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods');
            $table->string('weight');
            $table->decimal('amount_weight');
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
        Schema::dropIfExists('shipping_method_weight_amounts');
    }
};
