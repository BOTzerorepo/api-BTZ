<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItinerariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itinerarios', function (Blueprint $table) {
            $table->id();
            $table->string('unidad_asignada');
            $table->unsignedBigInteger('carga_id');
            $table->unsignedBigInteger('descarga_id');
            $table->unsignedBigInteger('trip_id');
            $table->string('user');
            $table->integer('estado')->default(1); // 0 eliminado - 1 activo - 2 completado
            $table->timestamps();

            // Claves foráneas
            $table->foreign('carga_id')->references('id')->on('customer_load_places')->onDelete('cascade');
            $table->foreign('descarga_id')->references('id')->on('customer_unload_places')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itinerarios');
    }
}

