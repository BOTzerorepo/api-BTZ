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
        Schema::create('cma_logs_coordinate', function (Blueprint $table) {
            $table->id();
            $table->string('patente');
            // ojo: mejor precisión típica
            $table->decimal('lat', 10, 8)->nullable();
            // renombrá "long" a "longitude" si podés (ver nota más abajo)
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('transportOrder');
            $table->string('equipmentReference')->nullable();
            $table->string('carrierBookingReference')->nullable();
            // "hora" parece fecha/hora -> timestamp
            $table->timestamp('hora')->nullable();

            $table->timestamps();

            // Índices sugeridos
            $table->index(['patente', 'hora']);
            $table->index(['equipmentReference', 'carrierBookingReference']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cma_logs_coordinate');
    }
};
