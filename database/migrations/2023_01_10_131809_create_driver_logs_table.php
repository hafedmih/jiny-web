<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('driver_id')->unsigned()->nullable();
            $table->date('date')->nullable();
            $table->timestamp('online_time')->nullable();
            $table->timestamp('offline_time')->nullable();
            $table->string('working_time')->nullable();
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
        Schema::dropIfExists('driver_logs');
    }
}
