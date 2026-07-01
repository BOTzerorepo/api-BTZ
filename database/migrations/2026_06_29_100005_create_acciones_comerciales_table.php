<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acciones_comerciales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes_comercial')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('tipo', ['Llamada', 'Reunión', 'Email', 'Propuesta', 'Seguimiento']);
            $table->text('descripcion');
            $table->text('resultado')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acciones_comerciales');
    }
};
