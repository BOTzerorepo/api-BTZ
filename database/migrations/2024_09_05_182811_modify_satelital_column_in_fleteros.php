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
        /*Schema::table('fleteros', function (Blueprint $table) {
            // Cambia la columna 'satelital' de boolean a string
            $table->string('satelital')->change();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       /* Schema::table('fleteros', function (Blueprint $table) {
            $table->boolean('satelital')->default(false)->change();
        });*/
    }
};
