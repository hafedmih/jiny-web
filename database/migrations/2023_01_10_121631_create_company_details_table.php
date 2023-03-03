<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_details', function (Blueprint $table) {
            $table->id();
            $table->biginteger('user_id')->unsigned()->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_code')->nullable();
            $table->string('alternative_number')->nullable();
            $table->integer('commission')->nullable();
            $table->string('no_of_vehicle')->nullable();
            $table->integer('status')->default(1);
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('company_details');
    }
}
