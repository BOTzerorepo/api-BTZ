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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 255);
            $table->bigInteger('CUIT')->default(NULL);
            $table->integer('IIBB');
            $table->string('mail_admin', 255);
            $table->string('mail_logistic', 255);
            $table->string('name_admin', 255);
            $table->string('name_logistic', 255);
            $table->bigInteger('cel_admin');
            $table->bigInteger('cel_logistic');
            $table->string('direccion', 255);
            $table->string('user', 255);
            $table->string('empresa', 255);
            $table->string('pais', 255);
            $table->string('provincia', 255);
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
        Schema::dropIfExists('companies');
    }
};
