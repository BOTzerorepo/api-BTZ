<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cntr_interest_point', function (Blueprint $table) {
            $table->id();
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('cntr_id_cntr');
            $table->unsignedBigInteger('interest_point_id');
            
            
            $table->foreign('cntr_id_cntr')->references('cntr_id')->on('cntr')->onDelete('cascade');
            $table->foreign('interest_point_id')->references('id')->on('interest_points')->onDelete('cascade');
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
        Schema::dropIfExists('cntr_interest_point');
    }
};
