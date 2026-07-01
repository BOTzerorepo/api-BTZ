<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes_comercial')->nullOnDelete();
            $table->enum('tipo', ['Dolor', 'Oportunidad', 'Feedback', 'Funcionalidad']);
            $table->text('descripcion');
            $table->enum('impacto', ['Alto', 'Medio', 'Bajo'])->default('Medio');
            $table->unsignedInteger('repetido_por')->default(1);
            $table->string('relacionado_con')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};
