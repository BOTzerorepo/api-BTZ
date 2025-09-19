<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('geo_action_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('trip_id');            // cntr.id_cntr
            $table->string('cntr_number');                    // cntr.cntr_number
            $table->string('domain');                         // trucks.domain

            // ENTER / EXIT + punto
            $table->enum('action_type', [
                'ENTER','EXIT'
            ]);

            $table->enum('point_type', [
                'CARGA','ADUANA','DESCARGA'
            ]);

            // Distancias y umbrales (en metros)
            $table->decimal('distance_m', 10, 2)->nullable();
            $table->integer('threshold_m')->nullable();

            // Coordenada del evento (punto de referencia)
            $table->decimal('event_lat', 10, 7)->nullable();
            $table->decimal('event_lng', 10, 7)->nullable();

            // Coordenada de la posición del camión en ese momento
            $table->decimal('position_lat', 10, 7)->nullable();
            $table->decimal('position_lng', 10, 7)->nullable();

            // Status vigente al momento del trigger (opcional pero útil)
            $table->string('status_at_moment')->nullable();

            // Timestamp de AKER si lo tenés; si no, el del servidor
            $table->timestamp('aker_time')->nullable();

            // Datos adicionales sin esquema rígido
            $table->json('meta')->nullable();

            $table->timestamps();

            // Índices prácticos para listado y filtros
            $table->index(['cntr_number', 'created_at']);
            $table->index(['trip_id', 'point_type', 'action_type', 'created_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('geo_action_logs');
    }
};