<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('dial_code')->nullable();
            $table->string('code')->nullable();
            $table->string('currency_name')->nullable();
            $table->string('currency_code')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->integer('status')->default(1);
            $table->string('capital')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('country_code')->nullable();
            $table->string('currency')->nullable();
            $table->string('currency_sub_unit')->nullable();
            $table->string('full_name')->nullable();
            $table->string('iso_3166_3')->nullable();
            $table->string('region_code')->nullable();
            $table->string('sub_region_code')->nullable();
            $table->string('eea')->nullable();
            $table->string('currency_decimals')->nullable();
            $table->string('flag')->nullable();
            $table->longText('flag_base_64')->nullable();
            $table->string('time_zone')->nullable();
            $table->string('gmt_offset')->nullable();
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
        Schema::dropIfExists('country');
    }
}
