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
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 255);
            $table->string('logo', 255)->nullable();
            $table->bigInteger('CUIT');
            $table->string('direccion', 255);
            $table->string('provincia', 255);
            $table->string('pais', 255);
            $table->string('paut', 255)->nullable();
            $table->string('permiso', 255)->nullable();
            $table->date('vto_permiso')->nullable();
            $table->string('contacto_logistica_nombre', 255);
            $table->bigInteger('contacto_logistica_celular');
            $table->string('contacto_logistica_mail', 50);
            $table->string('contacto_admin_nombre', 255);
            $table->bigInteger('contacto_admin_celular');
            $table->string('contacto_admin_mail', 50);
            $table->string('empresa', 250);
            $table->string('user', 255);
            $table->integer('customer_id')->nullable();
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
        Schema::dropIfExists('transports');
    }
};
