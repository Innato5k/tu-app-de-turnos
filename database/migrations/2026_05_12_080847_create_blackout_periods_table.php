<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blackout_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_time'); // Inicio del bloqueo
            $table->dateTime('end_time');   // Fin del bloqueo
            $table->string('reason')->nullable(); // Ej: "Vacaciones", "Día del Analista"
            $table->boolean('is_all_day')->default(false); // Para bloqueos de día completo
            $table->timestamps();
            $table->index(['user_id', 'start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blackout_periods');
    }
};