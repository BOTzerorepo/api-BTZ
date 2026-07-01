<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->cascadeOnDelete();
            $table->foreignId('tarifario_item_id')->constrained('tarifario_items');
            $table->string('descripcion')->nullable();
            $table->string('origen');
            $table->string('destino');
            $table->string('tipo_servicio');
            $table->string('tipo_cntr');
            $table->string('moneda');
            $table->decimal('tarifa', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_items');
    }
};
