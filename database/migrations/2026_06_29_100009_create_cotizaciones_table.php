<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('cliente_id')->constrained('clientes_comercial');
            $table->integer('comercial_id');
            $table->foreignId('combo_id')->nullable()->constrained('tarifario_combos')->nullOnDelete();
            $table->date('fecha_creacion');
            $table->date('fecha_vigencia');
            $table->enum('estado', ['Pendiente', 'Enviada', 'Aceptada', 'Rechazada', 'Expirada'])->default('Pendiente');
            $table->decimal('total_usd', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
