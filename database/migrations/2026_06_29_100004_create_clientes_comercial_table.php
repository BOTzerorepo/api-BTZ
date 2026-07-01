<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_comercial', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->string('razon_social');
            $table->string('cuit')->nullable();
            $table->string('industria')->nullable();
            $table->enum('segmento', ['A', 'B', 'C'])->default('C');
            $table->enum('estado', ['Activo', 'Inactivo', 'Prospecto'])->default('Prospecto');
            $table->date('fecha_alta')->nullable();
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_email')->nullable();
            $table->string('contacto_telefono')->nullable();
            $table->string('contacto_cargo')->nullable();
            $table->string('direccion')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_comercial');
    }
};
