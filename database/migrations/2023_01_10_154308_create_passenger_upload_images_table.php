<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassengerUploadImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passenger_upload_images', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable();
            $table->biginteger('user_id')->unsigned()->nullable();
            $table->biginteger('driver_id')->unsigned()->nullable();
            $table->string('image')->nullable();
            $table->enum('upload', array('USER','DRIVER'))->nullable();
            $table->timestamp('upload_time')->nullable();
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
        Schema::dropIfExists('passenger_upload_images');
    }
}
