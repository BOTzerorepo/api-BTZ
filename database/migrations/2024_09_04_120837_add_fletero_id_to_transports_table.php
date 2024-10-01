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
        Schema::table('transports', function (Blueprint $table) {
            $table->unsignedBigInteger('fletero_id')->nullable()->after('id'); // Agrega la columna
            $table->foreign('fletero_id')->references('id')->on('fleteros')->onDelete('set null'); // Define la relación
        });
    }

    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropForeign(['fletero_id']); // Elimina la clave foránea
            $table->dropColumn('fletero_id'); // Elimina la columna
        });
    }
};
