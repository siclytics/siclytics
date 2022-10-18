<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToExtensions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extensions', function (Blueprint $table) {
           $table->string('parentName')->nullable();
           $table->string('parentID')->nullable();
           $table->string('parentIdentifier')->nullable();
           $table->string('extensionNo')->nullable();
           $table->string('crDate')->nullable();
           $table->string('templateID')->nullable();
           $table->datetime('last_sync')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('extensions', function (Blueprint $table) {
            //
        });
    }
}
