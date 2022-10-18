<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoipUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voip_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voip_user_id');
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
        Schema::dropIfExists('voip_users');
    }
}
