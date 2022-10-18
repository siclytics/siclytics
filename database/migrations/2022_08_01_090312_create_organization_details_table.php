<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->string('c_name');
            $table->string('email');
            $table->string('api_key')->unique();
            $table->string('api_secret')->unique();
            $table->string('phone_call_events_api_key');
            $table->string('dafeeder_company');
            $table->unsignedBigInteger('whmcs_client_id');
            $table->string('payment_method');
            $table->unsignedBigInteger('invoice_due_days');
            $table->string('timezone_shift');

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
        Schema::dropIfExists('organization_details');
    }
}
