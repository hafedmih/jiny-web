<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestDriverLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_driver_logs', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable();
            $table->biginteger('user_id')->unsigned()->nullable();
            $table->string('driver_lat')->nullable();
            $table->string('driver_lng')->nullable();
            $table->string('date_time')->nullable();
            $table->enum('type', array('ACCEPT','REJECT','ARRIVED','STARTED','COMPLETED','CANCELLED'));
            $table->enum('user_type', array('USER', 'DRIVER', 'ADMIN'));
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
        Schema::dropIfExists('request_driver_logs');
    }
}
