<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes_comercial')->cascadeOnDelete();
            $table->string('nombre');
            $table->date('fecha');
            $table->enum('estado', ['Preparación', 'Reunión', 'Análisis', 'Implementación'])->default('Preparación');

            // Preparación
            $table->string('prep_comercial')->nullable();
            $table->integer('prep_cant_operaciones')->nullable();
            $table->json('prep_tipo_carga')->nullable();
            $table->json('prep_modulos_habilitados')->nullable();
            $table->text('prep_estadisticas_uso')->nullable();
            $table->text('prep_documentos_disponibles')->nullable();
            $table->text('prep_problemas_conocidos')->nullable();

            // Reunión
            $table->text('reunion_quien_usa')->nullable();
            $table->text('reunion_que_info_necesita')->nullable();
            $table->text('reunion_que_valor_encuentra')->nullable();
            $table->text('reunion_que_no_usa')->nullable();
            $table->text('reunion_que_le_falta')->nullable();

            // Datos estructurados (capa 4 — usados por /api/intel/stats)
            $table->unsignedTinyInteger('uso_frecuencia')->nullable();
            $table->json('valores_encontrados')->nullable();
            $table->json('barreras')->nullable();
            $table->json('funcionalidades_pedidas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};
