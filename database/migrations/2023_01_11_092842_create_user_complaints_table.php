<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('answer')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('complaint_id')->nullable();
            $table->integer('category')->nullable();
            $table->string('request_id')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('user_complaints');
    }
}
