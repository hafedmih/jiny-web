<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_history', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable();
            $table->double('olat',15,8)->nullable();
            $table->double('olng',15,8)->nullable();
            $table->double('dlat',15,8)->nullable();
            $table->double('dlng',15,8)->nullable();
            $table->string('pick_address')->nullable();
            $table->string('drop_address')->nullable();
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
        Schema::dropIfExists('request_history');
    }
}
