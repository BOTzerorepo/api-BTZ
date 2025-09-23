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
        Schema::create('cma_logs_coordinate', function (Blueprint $table) {
            $table->id();
            $table->string('patente');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable(); // si aún se llama 'long', dejalo y renombrás luego
            $table->string('transportOrder');
            $table->string('equipmentReference')->nullable();
            $table->string('carrierBookingReference')->nullable();
            $table->timestamp('hora')->nullable();
            $table->timestamps();
        
            // ÍNDICES con nombres cortos
            $table->index(['patente', 'hora'], 'clc_patente_hora_idx');
            $table->index(['equipmentReference', 'carrierBookingReference'], 'clc_eqref_cbr_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cma_logs_coordinate');
    }
};
