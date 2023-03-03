<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetForeignKetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->foreign('country_code')->references('id')->on('country')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('cancellation_requests', function (Blueprint $table) {
        //     $table->foreign('request_id')->references('id')->on('requests')
        //     ->onUpdate('cascade')->onDelete('cascade');

        //     $table->foreign('reason')->references('id')->on('cancellation_reasons')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('company_details', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('customer_details', function (Blueprint $table) {
        //     $table->foreign('request_id')->references('id')->on('requests')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('documents', function (Blueprint $table) {
        //     $table->foreign('group_by')->references('id')->on('document_group')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('drivers', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('driver_document', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')
        //     ->onUpdate('cascade')->onDelete('cascade');

        //     $table->foreign('document_id')->references('id')->on('documents')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('driver_logs', function (Blueprint $table) {
        //     $table->foreign('driver_id')->references('id')->on('users')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('driver_subscriptions', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')
        //     ->onUpdate('cascade')->onDelete('cascade');

        //     $table->foreign('subscription_id')->references('id')->on('submaster')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('favourite_place', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });

        // Schema::table('fine', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')
        //     ->onUpdate('cascade')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('set_foreign_ket');
    }
}
