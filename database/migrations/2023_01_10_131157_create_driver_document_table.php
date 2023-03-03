<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_document', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('document_id')->unsigned()->nullable();
            $table->string('document_image')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('issue_date')->nullable();
            $table->integer('document_status')->default(0);
            $table->integer('status')->default(1);
            $table->integer('exprienc_status')->default(1);
            $table->string('exprience_reson')->nullable();
            $table->string('identifier')->nullable();
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
        Schema::dropIfExists('driver_document');
    }
}
