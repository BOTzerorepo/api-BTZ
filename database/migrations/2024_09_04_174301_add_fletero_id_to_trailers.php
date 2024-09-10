<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('trailers', function (Blueprint $table) {
            // Temporalmente elimina el valor por defecto de created_at (ajusta según tu base de datos)
            DB::statement('ALTER TABLE trailers ALTER COLUMN created_at DROP DEFAULT');

            $table->integer('fletero_id')->nullable()->after('customer_id');

            // Restaura el valor por defecto de created_at (ajusta según tu base de datos)
            DB::statement('ALTER TABLE trailers ALTER COLUMN created_at SET DEFAULT CURRENT_TIMESTAMP');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trailers', function (Blueprint $table) {

            $table->dropColumn('fletero_id'); // Elimina la columna
            //
        });
    }
};
