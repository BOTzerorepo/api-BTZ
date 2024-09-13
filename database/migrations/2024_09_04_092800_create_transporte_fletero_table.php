<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransporteFleteroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_fletero', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('transport_id'); // Clave foránea hacia transportes
            $table->unsignedBigInteger('fletero_id'); // Clave foránea hacia fleteros
            $table->timestamps(); // Campos de timestamps

            // Definir las claves foráneas
            $table->foreign('transport_id')->references('id')->on('transports')->onDelete('cascade');
            $table->foreign('fletero_id')->references('id')->on('fleteros')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_fletero');
    }
}

