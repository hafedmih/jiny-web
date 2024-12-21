<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward', function (Blueprint $table) {
            $table->id();
            $table->string('reward_name')->nullable();
            $table->string('reward_icon')->nullable();
            $table->string('reward_to')->nullable();
            $table->string('reward_user_count')->nullable();
            $table->string('reward_user_from_date')->nullable();
            $table->string('reward_user_to_date')->nullable();
            $table->string('reward_user_type')->nullable();
            $table->string('reward_driver_count')->nullable();
            $table->string('reward_driver_from_date')->nullable();
            $table->string('reward_driver_to_date')->nullable();
            $table->string('reward_driver_type')->nullable();
            $table->string('no_of_trips')->nullable();
            $table->string('amount')->nullable();
            $table->string('slug')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('reward');
    }
}
