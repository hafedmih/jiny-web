<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projectversionings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug');
            $table->string('version_number');
            $table->string('version_code');
            $table->text('description');
            $table->string('application_type');
            $table->enum('status', array('OPEN','CLOSE'))->default('OPEN');
            $table->integer('created_by');
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
        Schema::dropIfExists('projectversionings');
    }
}
