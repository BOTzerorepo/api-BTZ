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
        Schema::table('aduanas', function (Blueprint $table) {
            // Verifica si la columna 'address' no existe antes de agregarla
            if (!Schema::hasColumn('aduanas', 'address')) {
                $table->string('address')->nullable()->after('description');
            }

            // Verifica si la columna 'user' no existe antes de agregarla
            if (!Schema::hasColumn('aduanas', 'user')) {
                $table->string('user')->nullable()->after('link_maps');
            }
            // Verifica si la columna 'user' no existe antes de agregarla
            if (!Schema::hasColumn('aduanas', 'company')) {
                $table->string('company')->nullable()->after('user');
            }

            // Verifica si la columna 'created_at' no existe antes de agregarla
            if (!Schema::hasColumn('aduanas', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            // Verifica si la columna 'updated_at' no existe antes de agregarla
            if (!Schema::hasColumn('aduanas', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aduanas', function (Blueprint $table) {
            if (Schema::hasColumn('aduanas', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('aduanas', 'user')) {
                $table->dropColumn('user');
            }
            if (Schema::hasColumn('aduanas', 'company')) {
                $table->dropColumn('company');
            }
            if (Schema::hasColumn('aduanas', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('aduanas', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
