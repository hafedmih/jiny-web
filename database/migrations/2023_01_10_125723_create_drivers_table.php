<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->nullable();
            $table->tinyInteger('is_available')->default(0);
            $table->tinyInteger('is_active')->default(0);
            $table->tinyInteger('is_approve')->default(0);
            $table->integer('total_accept')->default(1);
            $table->integer('total_reject')->default(1);
            $table->string('car_number')->nullable();
            $table->string('car_model')->nullable();
            $table->string('car_year')->nullable();
            $table->string('car_colour')->nullable();
            $table->string('slug')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('status')->default(1);
            $table->bigInteger('service_location')->unsigned()->nullable();
            $table->integer('reject_count')->default(0);
            $table->integer('document_upload_status')->default(2);
            $table->integer('refernce_count')->default(0);
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->integer('acceptance_ratio')->default(100);
            $table->enum('subscription_type', array('COMMISSION', 'SUBSCRIPTION', 'BOTH'))->nullable();
            $table->integer('company_id')->nullable();
            $table->string('service_category')->nullable();
            $table->string('brand_label')->nullable();
            $table->string('login_method')->nullable();
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
        Schema::dropIfExists('drivers');
    }
}
