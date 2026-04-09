<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Columna para el costo del turno
         
            $table->decimal('cost', 8, 2)->nullable();

            // Columna para la presencialidad
            $table->enum('modality', ['presencial', 'virtual'])->default('presencial');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('cost');
            $table->dropColumn('modality');
        });
    }
};