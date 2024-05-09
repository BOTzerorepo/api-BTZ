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
       /*  Schema::table('customer_unload_places', function (Blueprint $table) {
            $table->integer('rango')->default(100)->after('longitud');
        }); */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
     /*    Schema::table('customer_unload_places', function (Blueprint $table) {
            $table->dropColumn('rango');
        }); */
    }
};
