<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZonePriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_price', function (Blueprint $table) {
            $table->id();
            $table->integer('zone_id')->nullable();
            $table->integer('type_id')->nullable();
            $table->double('ridenow_base_price',10,2)->default(0.00);
            $table->string('ridenow_price_per_time')->nullable();
            $table->string('ridenow_base_distance')->nullble();
            $table->double('ridenow_price_per_distance',15,2)->default(0.00);
            $table->string('ridenow_free_waiting_time')->nullable();
            $table->double('ridenow_waiting_charge',15,2)->default(0.00);
            $table->double('ridenow_cancellation_fee',15,2)->default(0.00);
            $table->integer('ridenow_admin_commission_type')->nullable();
            $table->string('ridenow_admin_commission')->nullable();
            $table->string('ridenow_booking_base_fare')->nullable();
            $table->string('ridenow_booking_base_per_kilometer')->nullable();
            $table->string('ridenow_free_waiting_time_after_start')->nullable();
            $table->double('ridelater_base_price',15,2)->default(0.00);
            $table->string('ridelater_price_per_time')->nullable();
            $table->string('ridelater_base_distance')->nullable();
            $table->double('ridelater_price_per_distance',15,2)->default(0.00);
            $table->string('ridelater_free_waiting_time')->nullable();
            $table->double('ridelater_waiting_charge',15,2)->default(0.00);
            $table->double('ridelater_cancellation_fee',15,2)->default(0.00);
            $table->integer('ridelater_admin_commission_type')->nullable();
            $table->string('ridelater_admin_commission')->nullable();
            $table->string('ridelater_booking_base_fare')->nullable();
            $table->string('ridelater_booking_base_per_kilometer')->nullable();
            $table->string('ridelater_free_waiting_time_after_start')->nullable();
            $table->integer('status')->default(1);
            $table->string('slug')->nullable();
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
        Schema::dropIfExists('zone_price');
    }
}
