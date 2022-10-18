<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dids', function (Blueprint $table) {
            $table->id();
            $table->string('did_type')->nullable();
            $table->string('assigned')->nullable();
            $table->unsignedBigInteger('voip_user_id')->nullable();
            $table->unsignedBigInteger('did_id')->nullable();
            $table->unsignedBigInteger('channelID')->nullable();
            $table->string('channel')->nullable();
            $table->string('externalNo')->nullable();
            $table->string('did')->nullable();
            $table->string('cost')->nullable();
            $table->string('callbackExt')->nullable();
            $table->unsignedBigInteger('callbackExtID')->nullable();
            $table->string('flow')->nullable();
            $table->string('crDate')->nullable();
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
        Schema::dropIfExists('dids');
    }
}
