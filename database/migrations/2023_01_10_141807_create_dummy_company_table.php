<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDummyCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dummy_company', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('slug')->nullable();
            $table->string('company_phone_number')->nullable();
            $table->string('total_no_of_vehicle')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
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
        Schema::dropIfExists('dummy_company');
    }
}
