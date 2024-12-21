<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_management', function (Blueprint $table) {
            $table->id();
            $table->string('target_name')->nullable();
            $table->string('target_icon')->nullable();
            $table->string('target_to')->nullable();
            $table->string('target_driver_count')->nullable();
            $table->string('target_driver_from_date')->nullable();
            $table->string('target_driver_to_date')->nullable();
            $table->string('target_driver_type')->nullable();
            $table->string('no_of_trips')->nullable();
            $table->string('amount')->nullable();
            $table->string('slug')->nullable();
            $table->string('target_select_package')->nullable();
            $table->string('target_duration')->nullable();
            $table->biginteger('driver_id')->unsigned()->nullable();
            $table->biginteger('service_location')->unsigned()->nullable();
            $table->integer('achieve_amount')->nullable();
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
        Schema::dropIfExists('target_management');
    }
}
