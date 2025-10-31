<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geofencing_events', function (Blueprint $table) {
            $table->id();
            $table->string('cntr_number');             // Número de contenedor
            $table->string('truck_plate')->nullable(); // Patente
            $table->decimal('lat', 10, 6);
            $table->decimal('lon', 10, 6);

            $table->enum('zone_type', ['CARGA','DESCARGA','ADUANA','INTERES']);
            $table->enum('event_type', ['ENTER','EXIT']); // Entrada/Salida

            $table->timestamp('entered_at')->nullable(); // Cuando entra
            $table->timestamp('exited_at')->nullable();  // Cuando sale
            $table->integer('duration_minutes')->nullable(); // Tiempo dentro de zona

            $table->json('metadata')->nullable(); // opcional para info adicional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geofencing_events');
    }
};
