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
        Schema::create('particular_soft_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->default('avatar.png');
            $table->string('imgLogin')->default('avatarLog.png');
            $table->string('to_mail_trafico_Team')->default('pablorio@botzero.tech');
            $table->string('cc_mail_trafico_Team')->default('pablorio@botzero.tech');
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
        Schema::dropIfExists('particular_soft_configurations');
    }
};
