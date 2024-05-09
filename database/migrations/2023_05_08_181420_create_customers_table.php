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
       /*  Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('registered_name', 255);
            $table->bigInteger('tax_id');
            $table->string('contact_name', 50);
            $table->string('contact_mail', 50);
            $table->bigInteger('contact_phone');
            $table->timestamps();
        }); */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {/* 
       */  Schema::dropIfExists('customers');
    }
};
