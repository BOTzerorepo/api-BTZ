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
        Schema::create('cntr_types', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->integer('teu');
            $table->decimal('weight', 11, 2);
            $table->decimal('height', 11, 2);
            $table->decimal('width', 11, 2);
            $table->decimal('longitud', 11, 2);
            $table->text('observation');
            $table->string('user', 20);
            $table->string('company', 255);
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
        Schema::dropIfExists('cntr_types');
    }
};
