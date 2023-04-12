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
        Schema::create('deposito_retiros', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('address', 255);
            $table->string('country', 255);
            $table->string('city', 50);
            $table->integer('km_from_town');
            $table->string('latitud', 255);
            $table->string('longitud', 255);
            $table->text('link_maps');
            $table->string('user', 255);
            $table->string('empresa', 255);
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
        Schema::dropIfExists('deposito_retiros');
    }
};
