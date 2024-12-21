<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone', function (Blueprint $table) {
            $table->id();
            $table->string('zone_name')->nullable();
            $table->integer('primary_zone_id')->nullable();
            $table->string('country')->nullable();
            $table->string('admin_commission_type')->nullable();
            $table->string('admin_commission')->nullable();
            $table->multiPolygon('map_zone')->nullable();
            $table->string('payment_types')->nullable();
            $table->string('unit')->nullable();
            $table->string('non_service_zone')->nullable();
            $table->string('slug')->nullable();
            $table->string('types_id')->nullable();
            $table->text('map_cooder')->nullable();
            $table->enum('zone_level', array('SECONDARY', 'PRIMARY'));
            $table->enum('driver_assign_method', array('DISTANCE', 'FIFO'));
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
        Schema::dropIfExists('zone');
    }
}
