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
        Schema::create('aker_trucks', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('transport');
            $table->string('satelital');
            $table->integer('estado')->default(0); // 0 - No Alta 1 - Alta 2 - Con Problema. 
            $table->text('observacion');
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
        Schema::dropIfExists('aker_trucks');
    }
};
