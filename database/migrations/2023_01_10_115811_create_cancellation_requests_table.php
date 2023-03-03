<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancellationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancellation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable();
            $table->biginteger('reason')->unsigned()->nullable();
            $table->double('cancellation_fee',10,2)->default(0.00);
            $table->tinyInteger('is_paid')->default(0);
            $table->text('custom_reason')->nullable();
            $table->enum('cancelled_by', array('Automatic', 'User', 'Driver', 'Dispatcher'));
            $table->enum('cancel_type',array('Before accept','Before arrive','After arrived'))->nullable();
            $table->double('user_lat',15,8)->nullable();
            $table->double('user_lng',15,8)->nullable();
            $table->double('driver_lat',15,8)->nullable();
            $table->double('driver_lng',15,8)->nullable();
            $table->string('user_location')->nullable();
            $table->string('driver_location')->nullable();
            $table->enum('status', array('Pending','Resolved'));
            $table->double('distance',10,2)->nullable();
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
        Schema::dropIfExists('cancellation_requests');
    }
}
