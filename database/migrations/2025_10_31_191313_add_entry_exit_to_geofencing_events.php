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
        Schema::table('geofencing_events', function (Blueprint $table) {
            $table->timestamp('entered_at')->nullable();
            $table->timestamp('exited_at')->nullable()->after('entered_at');
            $table->integer('duration_minutes')->nullable()->after('exited_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geofencing_events', function (Blueprint $table) {
            $table->dropColumn(['entered_at', 'exited_at', 'duration_minutes']);
        });
    }
};
