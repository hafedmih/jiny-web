<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_bills', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable();
            $table->double('base_price',10,2)->default(0.00);
            $table->integer('base_distance')->nullable();
            $table->double('total_distance',15,8)->default(0.00);
            $table->double('total_time',15,2)->default(0.00);
            $table->double('price_per_distance',10,2)->default(0.00);
            $table->double('distance_price',10,2)->default(0.00);
            $table->double('price_per_time',10,2)->default(0.00);
            $table->double('time_price',10,2)->default(0.00);
            $table->double('waiting_charge',10,2)->default(0.00);
            $table->double('cancellation_fee',10,2)->default(0.00);
            $table->double('service_tax',10,2)->default(0.00);
            $table->integer('service_tax_percentage')->default(0);
            $table->double('promo_discount',10,2)->default(0.00);
            $table->double('admin_commision',10,2)->default(0.00);
            $table->double('admin_commision_with_tax',10,2)->default(0.00);
            $table->double('driver_commision',10,2)->default(0.00);
            $table->double('total_amount',10,2)->default(0.00);
            $table->string('requested_currency_code')->nullable();
            $table->string('requested_currency_symbol')->nullable();
            $table->double('sub_total',10,2)->default(0.00);
            $table->double('out_of_zone_price',10,2)->default(0.00);
            $table->double('hill_station_price',10,2)->default(0.00);
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
        Schema::dropIfExists('request_bills');
    }
}
