<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifario_combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('tarifario_combos')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('tarifario_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifario_combo_items');
    }
};
