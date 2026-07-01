<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hallazgos_diagnostico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostico_id')->constrained('diagnosticos')->cascadeOnDelete();
            $table->text('descripcion');
            $table->enum('impacto', ['Alto', 'Medio', 'Bajo'])->default('Medio');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hallazgos_diagnostico');
    }
};
