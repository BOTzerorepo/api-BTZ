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
        Schema::create('punto_de_interes', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->foreignId('itinerario_id')->constrained()->onDelete('cascade');
            $table->integer('orden')->default(1);
            $table->integer('estado')->default(1);
            $table->decimal('latitud', 10, 5); // M >= D
            $table->decimal('longitud', 10, 5); // M >= D
            $table->integer('rango')->default(100);
            $table->string('accion_mail')->default('0');
            $table->string('accion_notificacion')->default('0');
            $table->string('accion_status')->default('0');
            $table->string('user');
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
        Schema::dropIfExists('punto_de_interes');
    }
};
