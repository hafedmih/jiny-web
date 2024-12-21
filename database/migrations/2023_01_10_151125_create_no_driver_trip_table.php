<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoDriverTripTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('no_driver_trip', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('pick_up')->nullable();
            $table->string('drop')->nullable();
            $table->timestamp('datetime')->nullable();
            $table->enum('trip_type', array('LOCAL', 'RENTAL', 'OUTSTATION'))->nullable();
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
        Schema::dropIfExists('no_driver_trip');
    }
}
