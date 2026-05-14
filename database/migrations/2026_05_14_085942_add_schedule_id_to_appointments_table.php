<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // nullable() es importante si ya tenés turnos cargados sin ID de horario
            $table->foreignId('schedule_id')
                  ->nullable() 
                  ->after('user_id') // Ajustalo según tu tabla
                  ->constrained('professional_schedules') 
                  ->onDelete('restrict'); // Evita borrar el horario si hay turnos
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropColumn('schedule_id');
        });
    }
};