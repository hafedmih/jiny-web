<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_meta', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable();
            $table->biginteger('user_id')->unsigned()->nullable();
            $table->biginteger('driver_id')->unsigned()->nullable();
            $table->tinyInteger('active')->default(0);
            $table->enum('assign_method', array('1', '2'));
            $table->tinyInteger('is_later')->default(0);
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
        Schema::dropIfExists('request_meta');
    }
}
