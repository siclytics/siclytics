<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_provider_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact');
            $table->string('login');
            $table->string('company');
            $table->timestamp('last_sync');
            $table->string('template_id');
            $table->string('identifier');
            $table->string('scope');
            $table->boolean('status');
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
        Schema::dropIfExists('service_providers');
    }
}
