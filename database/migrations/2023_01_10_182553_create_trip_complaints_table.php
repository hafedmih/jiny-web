<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('type')->nullable();
            $table->string('slug')->nullable();
            $table->string('complaint_type')->nullable();
            $table->string('language')->nullable();
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
        Schema::dropIfExists('trip_complaints');
    }
}
