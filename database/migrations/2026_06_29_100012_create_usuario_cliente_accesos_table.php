<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_cliente_accesos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_comercial_id');
            $table->integer('user_id');

            // Visibilidad
            $table->boolean('ver_precios')->default(false);
            $table->boolean('ver_documentos')->default(true);
            $table->boolean('ver_tracking')->default(true);
            $table->boolean('ver_cargas_internas')->default(false);

            // Notificaciones
            $table->boolean('notif_email')->default(false);
            $table->boolean('notif_nuevas_cargas')->default(false);
            $table->boolean('notif_cambio_estado')->default(true);

            // Vista personalizable (columnas, etc.)
            $table->json('columnas_visibles')->nullable();

            // Notas internas
            $table->text('notas')->nullable();

            $table->timestamps();

            $table->foreign('cliente_comercial_id')->references('id')->on('clientes_comercial')->onDelete('cascade');
            $table->index('user_id');
            $table->unique(['cliente_comercial_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_cliente_accesos');
    }
};
