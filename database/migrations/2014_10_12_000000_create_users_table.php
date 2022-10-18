<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('username')->unique();
            $table->string('self_extension')->nullable();
            $table->integer('default_organization_id')->nullable();
            $table->string('other_organizations_ids')->nullable();
            $table->integer('otp')->nullable();
            $table->datetime('otp_expiry')->nullable();
                 $table->string('api_token', 80)
                        ->unique()
                        ->nullable()
                        ->default(null);
            $table->string('sip_domain')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('api_key')->nullable();
            $table->string('voipnow_auth_app_key')->nullable();
            $table->string('voipnow_auth_app_secret')->nullable();
            $table->string('unified_secret')->nullable();
            $table->string('unified_key')->nullable();


            $table->unsignedBigInteger('service_provider_id')->nullable();

            // Voip Now Token information
            $table->text('voipnow_access_token')->nullable();
            $table->integer('voipnow_expires_in')->nullable();
            $table->timestamp('voipnow_expired_at')->nullable();
            $table->timestamp('last_login')->nullable();
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
        Schema::dropIfExists('users');
    }
}
