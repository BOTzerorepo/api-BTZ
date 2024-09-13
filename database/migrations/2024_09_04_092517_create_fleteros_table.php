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
        Schema::create('fleteros', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('logo')->nullable();
            $table->boolean('satelital')->default(false);
            $table->boolean('alta_aker')->default(false);
            $table->string('CUIT', 11);
            $table->string('direccion');
            $table->string('provincia');
            $table->string('pais');
            $table->string('paut')->nullable();
            $table->string('permiso')->nullable();
            $table->date('vto_permiso')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fleteros');
    }
};
