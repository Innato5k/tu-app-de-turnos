<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            
            // El string RRULE (ej: FREQ=WEEKLY;BYDAY=MO) que procesará la librería 'recurr'
            $table->string('rrule');             
            $table->date('start_date'); // Cuándo comienza la recurrencia
            $table->date('end_date')->nullable(); // Cuándo termina (null = para siempre)            
            // Datos por defecto para los turnos que genere esta regla
            $table->decimal('cost', 10, 2);
            $table->string('modality')->default('presencial');
            $table->text('notes')->nullable();            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_appointments');
    }
};
