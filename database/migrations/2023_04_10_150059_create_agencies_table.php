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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('description', 255);
            $table->string('razon_social', 255);
            $table->string('tax_id', 255);
            $table->string('puerto', 255);
            $table->string('contact_name', 255);
            $table->string('contact_phone', 255);
            $table->string('contact_mail', 255);
            $table->string('user', 255);
            $table->string('empresa', 255);
            $table->text('observation_gral');
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
        Schema::dropIfExists('agencies');
    }
};
