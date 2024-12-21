<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndividualPromoMarketingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individual_promo_marketing', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('promo_name')->nullable();
            $table->string('target_amount')->nullable();
            $table->string('promo_percentage')->nullable();
            $table->string('promo_amount')->nullable();
            $table->enum('promo_amount_type',array('1', '0'))->nullable();
            $table->string('trip_type')->nullable();
            $table->string('no_of_times_use')->nullable();
            $table->integer('status')->default(1);
            $table->integer('marker_id')->nullable();
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
        Schema::dropIfExists('individual_promo_marketing');
    }
}
