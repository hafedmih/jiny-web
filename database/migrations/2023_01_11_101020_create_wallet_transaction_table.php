<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_transaction', function (Blueprint $table) {
            $table->id();
            $table->biginteger('wallet_id')->unsigned()->nullable();
            $table->biginteger('request_id')->unsigned()->nullable();
            $table->double('amount',10,2)->nullable();
            $table->string('purpose')->nullable();
            $table->enum('type', array('EARNED', 'SPENT'));
            $table->biginteger('user_id')->unsigned()->nullable();
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
        Schema::dropIfExists('wallet_transaction');
    }
}
