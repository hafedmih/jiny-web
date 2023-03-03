<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutStationMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_station_master', function (Blueprint $table) {
            $table->id();
            $table->string('pick_up')->nullable();
            $table->double('pick_lat',15,8)->nullable();
            $table->double('pick_lng',15,8)->nullable();
            $table->string('country')->nullable();
            $table->string('drop')->nullable();
            $table->double('drop_lat',15,8)->nullable();
            $table->double('drop_lng',15,8)->nullable();
            $table->string('distance')->nullable();
            $table->string('price')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('out_station_master');
    }
}
