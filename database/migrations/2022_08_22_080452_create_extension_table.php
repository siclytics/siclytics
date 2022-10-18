<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtensionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voip_user_id');
             $table->string('first_name');
             $table->string('last_name');
             $table->string('label');
             $table->string('email');
             $table->string('extension_type');
             $table->string('extended_number');
             $table->string('identifier');
             $table->text('available_caller_ids')->nullable();
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
        Schema::dropIfExists('extensions');
    }
}
