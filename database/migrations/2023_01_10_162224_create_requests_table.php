<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->nullable();
            $table->integer('request_otp')->default(1234);
            $table->tinyInteger('is_later')->default(0);
            $table->tinyInteger('is_instant_trip')->default(0);
            $table->tinyInteger('if_dispatch')->default(0);
            $table->biginteger('zone_type_id')->unsigned()->nullable();
            $table->biginteger('user_id')->unsigned()->nullable();
            $table->biginteger('driver_id')->unsigned()->nullable();
            $table->timestamp('trip_start_time')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->tinyInteger('is_driver_started')->default(0);
            $table->tinyInteger('is_driver_arrived')->default(0);
            $table->tinyInteger('is_trip_start')->default(0);
            $table->tinyInteger('is_completed')->default(0);
            $table->tinyInteger('is_cancelled')->default(0);
            $table->string('custom_reason')->nullable();
            $table->enum('cancel_method', array('Automatic', 'User', 'Driver', 'Dispatcher'));
            $table->double('total_distance',15,8)->nullable();
            $table->double('total_time',15,2)->nullable();
            $table->tinyInteger('is_paid')->default(0);
            $table->tinyInteger('user_rated')->default(0);
            $table->tinyInteger('driver_rated')->default(0);
            $table->string('timezone')->nullable();
            $table->integer('attempt_for_schedule')->default(0);
            $table->biginteger('dispatcher_id')->nullable();
            $table->text('driver_notes')->nullable();
            $table->integer('created_by')->nullable();
            $table->enum('payment_opt', array('Card', 'Cash', 'Wallet'));
            $table->enum('ride_type', array('Ride Now', 'Ride Later'));
            $table->string('unit')->nullable();
            $table->string('requested_currency_code')->nullable();
            $table->string('requested_currency_symbol')->nullable();
            $table->integer('promo_id')->nullable();
            $table->tinyInteger('location_approve')->default(0);
            $table->integer('hold_status')->default(0);
            $table->integer('availables_status')->default(0);
            $table->enum('trip_type', array('LOCAL', 'RENTAL', 'OUTSTATION'));
            $table->integer('rental_package')->nullable();
            $table->enum('manual_trip', array('AUTOMATIC', 'MANUAL'));
            $table->integer('outstation_id')->nullable();
            $table->integer('outstation_type_id')->nullable();
            $table->integer('package_id')->nullable();
            $table->integer('package_item_id')->nullable();
            $table->timestamp('trip_end_time')->nullable();
            $table->string('outstation_trip_type')->nullable();
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
        Schema::dropIfExists('requests');
    }
}
