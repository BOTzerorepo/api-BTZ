<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales_cliente')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('email');
            $table->string('rol')->nullable();
            $table->boolean('notif_email')->default(false);
            $table->boolean('notif_sistema')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_cliente');
    }
};
