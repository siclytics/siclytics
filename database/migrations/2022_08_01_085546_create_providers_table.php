<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_provider_id');
            $table->foreign('service_provider_id')->references('service_provider_id')->on('service_providers');
            $table->string('c_name');
            $table->string('whmcs_client_id');
            $table->string('payment_method');
            $table->string('email');
            $table->string('api_key')->unique();
            $table->string('api_secret')->unique();
            $table->string('invoice_due_date');
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
        Schema::dropIfExists('providers_details');
    }
}
