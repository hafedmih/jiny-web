<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoneSurgePriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_surge_price', function (Blueprint $table) {
            $table->id();
            $table->integer('zone_id')->nullable();
            $table->double('surge_price',15,2)->default(0.00);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('available_days')->nullable();
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
        Schema::dropIfExists('zone_surge_price');
    }
}
