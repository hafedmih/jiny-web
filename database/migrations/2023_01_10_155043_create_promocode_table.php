<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocode', function (Blueprint $table) {
            $table->id();
            $table->biginteger('zone_id')->unsigned()->nullable();
            $table->string('promo_code')->nullable();
            $table->string('select_offer_option')->nullable();
            $table->string('promo_icon')->nullable();
            $table->text('description')->nullable();
            $table->string('promo_offer_no_of_ride')->nullable();
            $table->double('target_amount',15,2)->nullable();
            $table->integer('promo_type')->nullable();
            $table->double('amount',15,2)->nullable();
            $table->double('percentage',15,2)->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('distance_km')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('types_id')->nullable();
            $table->string('slug')->nullable();
            $table->integer('status')->default(1);
            $table->integer('promo_use_count')->default(0);
            $table->integer('promo_user_reuse_count')->default(0);
            $table->tinyInteger('new_user_count')->nullable();
            $table->string('trip_type')->nullable();
            $table->string('total_promo_limit_count')->nullable();
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
        Schema::dropIfExists('promocode');
    }
}
