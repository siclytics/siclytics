<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('phone')->nullable();
           $table->string('fax')->nullable();
           $table->string('address')->nullable();
           $table->string('city')->nullable();
           $table->string('pcode')->nullable();
           $table->string('country')->nullable();
           $table->string('region')->nullable();
           $table->string('timezone')->nullable();
           $table->string('interfaceLang')->nullable();
           $table->string('notes')->nullable();
           $table->string('serverID')->nullable();
           $table->string('chargingIdentifier')->nullable();
           $table->string('phoneStatus')->nullable();
           $table->string('cpAccess')->nullable();
           $table->string('parentName')->nullable();
           $table->string('parentID')->nullable();
           $table->string('parentIdentifier')->nullable();
           $table->string('chargingPlanID')->nullable();
           $table->string('chargingPlanIdentifier')->nullable();
           $table->string('chargingPlan')->nullable();
           $table->string('crDate')->nullable();
           $table->string('link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            //
        });
    }
}
