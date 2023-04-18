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
        Schema::create('customer_agents', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 255);
            $table->string('pais', 255);
            $table->string('provincia', 255);
            $table->string('mail', 255);
            $table->bigInteger('phone');
            $table->bigInteger('tax_id');
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
        Schema::dropIfExists('customer_agents');
    }
};
