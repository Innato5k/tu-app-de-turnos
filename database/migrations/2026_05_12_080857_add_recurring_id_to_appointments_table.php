<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Relación opcional con la regla de recurrencia
            $table->foreignId('recurring_appointment_id')
                  ->nullable()
                  ->after('patient_id')
                  ->constrained('recurring_appointments')
                  ->onDelete('set null'); // Si borramos la regla, el turno queda como historial
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recurring_appointment_id');
        });
    }
};
