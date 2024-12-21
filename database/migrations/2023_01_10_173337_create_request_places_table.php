<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_places', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable();
            $table->double('pick_lat',15,8)->nullable();
            $table->double('pick_lng',15,8)->nullable();
            $table->double('drop_lat',15,8)->nullable();
            $table->double('drop_lng',15,8)->nullable();
            $table->string('pick_address')->nullable();
            $table->string('drop_address')->nullable();
            $table->string('pick_up_id')->nullable();
            $table->string('drop_id')->nullable();
            $table->double('stop_lat',15,8)->nullable();
            $table->double('stop_lng',15,8)->nullable();
            $table->string('stop_address')->nullable();
            $table->string('stop_id')->nullable();
            $table->longText('request_path')->nullable();
            $table->longText('poly_string')->nullable();
            $table->tinyInteger('stops')->default(0);
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
        Schema::dropIfExists('request_places');
    }
}
