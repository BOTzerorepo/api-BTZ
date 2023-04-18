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
        Schema::create('customer_ntfies', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 255);
            $table->bigInteger('tax_id');
            $table->string('address', 255);
            $table->string('city', 255);
            $table->string('country', 255);
            $table->string('postal_code', 11);
            $table->string('create_user', 22);
            $table->string('company', 255);
            $table->string('remarks', 255);
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
        Schema::dropIfExists('customer_ntfies');
    }
};
