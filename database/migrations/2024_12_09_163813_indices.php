<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->index('domain'); // Índice para mejorar las uniones y filtrados en la tabla trucks
            $table->index('alta_aker'); // Índice para agilizar los filtros en la columna alta_aker
        });

        Schema::table('cntr', function (Blueprint $table) {
            $table->index('cntr_number'); // Índice para mejorar las uniones con la tabla asign
            $table->index('main_status'); // Índice para agilizar los filtros por estado
        });

        Schema::table('carga', function (Blueprint $table) {
            $table->index('booking'); // Índice para mejorar las uniones con la tabla cntr
            $table->index('deleted_at'); // Índice para agilizar los filtros de registros eliminados
            
        });
        Schema::table('aduanas', function (Blueprint $table) {
            $table->index('description'); // Índice para mejorar las uniones con la tabla carga
        });
    
        Schema::table('customer_load_places', function (Blueprint $table) {
            $table->index('description'); // Índice para mejorar las uniones con la tabla carga
        });
    
        Schema::table('customer_unload_places', function (Blueprint $table) {
            $table->index('description'); // Índice para mejorar las uniones con la tabla carga
        });


        // Índice compuesto opcional (considerar si se realizan consultas frecuentes combinando booking y deleted_at)
        // Schema::table('carga', function (Blueprint $table) {
        //     $table->index(['booking', 'deleted_at']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->dropIndex('trucks_domain_index');
            $table->dropIndex('trucks_alta_aker_index');
        });

        Schema::table('cntr', function (Blueprint $table) {
            $table->dropIndex('cntr_cntr_number_index');
            $table->dropIndex('cntr_main_status_index'); // Eliminar índice si se agregó
        });

        Schema::table('carga', function (Blueprint $table) {
            $table->dropIndex('carga_booking_index');
            $table->dropIndex('carga_deleted_at_index');
           
        });
        Schema::table('aduanas', function (Blueprint $table) {
            $table->dropIndex('aduanas_description_index');
        });
    
        Schema::table('customer_load_places', function (Blueprint $table) {
            $table->dropIndex('customer_load_places_description_index');
        });
    
        Schema::table('customer_unload_places', function (Blueprint $table) {
            $table->dropIndex('customer_unload_places_description_index');
        });
    

        // Eliminar índice compuesto si se agregó
        // Schema::table('carga', function (Blueprint $table) {
        //     $table->dropIndex('carga_booking_deleted_at_index');
        // });
    }
};
