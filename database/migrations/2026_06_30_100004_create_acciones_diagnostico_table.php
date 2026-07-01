<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acciones_diagnostico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostico_id')->constrained('diagnosticos')->cascadeOnDelete();
            $table->text('descripcion');
            $table->string('tipo');
            $table->string('responsable');
            $table->date('fecha_limite')->nullable();
            $table->enum('estado', ['Pendiente', 'En curso', 'Completada'])->default('Pendiente');
            $table->text('comentarios')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acciones_diagnostico');
    }
};
