<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_name')->nullable();
            $table->string('image')->nullable();
            $table->string('capacity')->nullable();
            $table->biginteger('category_id')->unsigned()->nullable();
            $table->string('service_type')->nullable();
            $table->string('slug')->nullable();
            $table->string('sorting_order')->nullable();
            $table->string('highlight_image')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('vehicle');
    }
}
