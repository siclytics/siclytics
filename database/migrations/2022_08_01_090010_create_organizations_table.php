<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_provider_id');
            // $table->foreign('service_provider_id')->references('service_provider_id')->on('service_providers');
            $table->unsignedBigInteger('organization_id');
            $table->boolean('status');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('login')->nullable();
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('template_id')->nullable();
            $table->string('identifier')->nullable();
            $table->string('scope')->nullable();
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
        Schema::dropIfExists('organizations');
    }
}
