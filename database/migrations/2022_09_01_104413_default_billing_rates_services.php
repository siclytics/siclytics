<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DefaultBillingRatesServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_billing_rates_services', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->text('description');
            $table->unsignedBigInteger('discount_item');
            $table->unsignedBigInteger('discount_value');
            $table->unsignedBigInteger('rate');
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
        Schema::dropIfExists('default_billing_rates_services');
    }
}
