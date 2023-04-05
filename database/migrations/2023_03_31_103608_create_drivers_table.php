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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('foto', 255)->nullable();
            $table->string('documento', 11);
            $table->date('vto_carnet');
            $table->bigInteger('WhatsApp');
            $table->string('mail', 255);
            $table->string('user', 255);
            $table->string('empresa', 255);
            $table->string('transporte', 255);
            $table->string('status_chofer', 255)->nullable();
            $table->string('place', 255)->nullable();
            $table->text('Observaciones');
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
        Schema::dropIfExists('drivers');
    }
};
