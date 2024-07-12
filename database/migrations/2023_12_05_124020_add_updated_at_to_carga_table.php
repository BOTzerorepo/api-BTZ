<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedAtToCargaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    /*     Schema::table('carga', function (Blueprint $table) {
            $table->timestamps(); // Si ya tienes 'created_at', esto también agrega 'updated_at'
        }); */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       /*  Schema::table('carga', function (Blueprint $table) {
            $table->dropTimestamps(); // Esto eliminará 'created_at' y 'updated_at'
        }); */
    }
}
