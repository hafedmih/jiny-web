<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutstationUploadImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outstation_upload_images', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable();
            $table->biginteger('user_id')->unsigned()->nullable();
            $table->string('trip_start_km_image')->nullable();
            $table->string('trip_start_km')->nullable();
            $table->string('trip_end_km_image')->nullable();
            $table->string('trip_end_km')->nullable();
            $table->string('distance')->nullable();
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
        Schema::dropIfExists('outstation_upload_images');
    }
}
