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
            $table->bigIncrements('id');
            $table->string('slug',255);            
            $table->string('firstname',255);
            $table->string('lastname',255);
            $table->string('email')->unique();
            $table->string('phone_number',20);
            $table->string('gender',15);
            $table->string('time_zone');
            $table->string('user_type')->nullable();
            $table->string('device_info_hash');
            $table->biginteger('application_id')->unsigned()->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamp('last_seen')->nullable();
            $table->string('social_unique_id')->nullable();
            $table->enum('mobile_application_type', array('ANDROID','IOS'))->default('ANDROID');
            $table->text('token')->nullable();
            $table->string('country_code')->nullable();
            $table->string('profile_pic')->nullable();
            $table->string('referral_code')->nullable();
            $table->integer('online_by')->default(1);
            $table->string('block_reson')->nullable();
            $table->string('language')->nullable();
            $table->string('address')->nullable();
            $table->string('emergency_number')->nullable();
            $table->string('user_referral_code')->nullable();
            $table->string('otp')->nullable();
            $table->string('country')->nullable();
            $table->integer('trips_count')->default(0);
            $table->integer('created_by')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
