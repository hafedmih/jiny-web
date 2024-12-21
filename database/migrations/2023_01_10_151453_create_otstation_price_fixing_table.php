<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtstationPriceFixingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outstation_price_fixing', function (Blueprint $table) {
            $table->id();
            $table->biginteger('type_id')->unsigned()->nullable();
            $table->double('distance_price',10,2)->nullable();
            $table->double('admin_commission',10,2)->nullable();
            $table->integer('admin_commission_type')->default(1);
            $table->double('driver_price',10,2)->nullable();
            $table->string('grace_time')->nullable();
            $table->double('hill_station_price',10,2)->nullable();
            $table->double('waiting_charge',10,2)->nullable();
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
        Schema::dropIfExists('outstation_price_fixing');
    }
}
