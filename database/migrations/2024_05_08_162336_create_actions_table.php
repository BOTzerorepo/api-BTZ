<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->boolean('send_email')->default(false); // Indica si se enviará correo
            $table->boolean('update_status')->default(false); // Indica si se actualizará el estado
            $table->boolean('send_notification')->default(false); // Indica si se enviarán notificaciones
            $table->json('parameters')->nullable(); // Parámetros adicionales para la acción
            $table->timestamps();
        });

        // Crear tabla de relación para acción y estado
        Schema::create('action_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_id')->constrained('actions');
            $table->foreignId('status_id');
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
        Schema::dropIfExists('action_status');
        Schema::dropIfExists('actions');
    }
}