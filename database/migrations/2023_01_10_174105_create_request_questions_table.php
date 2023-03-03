<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_questions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('request_id')->nullable();
            $table->integer('question_id')->nullable();
            $table->enum('answer', array('YES', 'NO'));
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('request_questions');
    }
}
