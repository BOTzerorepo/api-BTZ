<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proximas_acciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes_comercial')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('tipo');
            $table->text('descripcion');
            $table->string('responsable');
            $table->boolean('completada')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proximas_acciones');
    }
};
