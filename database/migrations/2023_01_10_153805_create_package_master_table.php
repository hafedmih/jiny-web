<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_master', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('country')->nullable();
            $table->string('hours')->nullable();
            $table->string('km')->nullable();
            $table->integer('admin_commission_type')->nullable();
            $table->string('admin_commission')->nullable();
            $table->string('driver_price')->nullable();
            $table->string('time_cast')->nullable();
            $table->string('time_cast_type')->nullable();
            $table->string('distance_cast')->nullable();
            $table->string('slug')->nullable();
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
        Schema::dropIfExists('package_master');
    }
}
