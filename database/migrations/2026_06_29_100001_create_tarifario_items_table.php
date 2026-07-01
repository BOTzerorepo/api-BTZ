<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifario_items', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->string('origen');
            $table->string('destino');
            $table->enum('tipo_servicio', ['EMAR', 'ETER', 'IMAR', 'ITER', 'FOB', 'NAC']);
            $table->string('tipo_cntr');
            $table->enum('moneda', ['USD', 'ARS']);
            $table->decimal('tarifa', 10, 2);
            $table->text('descripcion')->nullable();
            $table->date('vigencia_desde');
            $table->date('vigencia_hasta');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifario_items');
    }
};
