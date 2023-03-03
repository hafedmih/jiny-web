<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('driver_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('sub_title')->nullable();
            $table->text('message')->nullable();
            $table->string('has_redirect_url')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('slug')->nullable();
            $table->dateTime('date')->nullable();
            $table->enum('notification_type', array('GENERAL', 'TRIP'))->nullable();
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
        Schema::dropIfExists('notification');
    }
}
