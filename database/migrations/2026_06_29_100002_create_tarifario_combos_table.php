<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifario_combos', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio_combo', 10, 2);
            $table->enum('moneda', ['USD', 'ARS']);
            $table->date('vigencia_desde');
            $table->date('vigencia_hasta');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifario_combos');
    }
};
