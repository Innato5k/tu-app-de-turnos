<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProfessionalSchedule;
use App\Models\AvailableSlot;
use Carbon\Carbon;

class GenerateAvailableSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slots:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera slots de turnos disponibles para los profesionales según sus horarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Traemos los horarios con su relación de usuario para evitar queries de más
        $schedules = ProfessionalSchedule::with('user')->get();
        $this->info("Iniciando generación de slots para " . $schedules->count() . " reglas de horario.");

        foreach ($schedules as $schedule) {
            // Rango de generación: Desde hoy hasta 2 meses adelante (ajustable)
            $startDate = now()->startOfDay();
            $endDate = now()->addMonths(2)->endOfDay();

            $currentDate = $startDate->clone();

            while ($currentDate->lte($endDate)) {
                // 1. Validar día de la semana
                if ($currentDate->dayOfWeek == $schedule->day_of_week) {

                    // 2. Validar fechas de efectividad
                    $isEffective = true;
                    if ($schedule->effective_start_date && $currentDate->lt($schedule->effective_start_date)) $isEffective = false;
                    if ($schedule->effective_end_date && $currentDate->gt($schedule->effective_end_date)) $isEffective = false;

                    if ($isEffective) {
                        $this->createAvailableSlots($currentDate, $schedule);
                    }
                }
                $currentDate->addDay();
            }
        }
        $this->info("¡Slots generados exitosamente!");
    }

    protected function createAvailableSlots($currentDate, $schedule)
    {
        $dateStr = $currentDate->format('Y-m-d');
        $startTime = Carbon::parse($dateStr . ' ' . $schedule->start_time);
        $endTime = Carbon::parse($dateStr . ' ' . $schedule->end_time);

        // Usamos el slot_duration del profesional, o 30 por defecto si no está definido
        $duration = $schedule->slot_duration ?? 30;

        $auxStartTime = $startTime->clone();

        while ($auxStartTime->clone()->addMinutes($duration) <= $endTime) {
            $auxEndTime = $auxStartTime->clone()->addMinutes($duration);

            // EVITAR MUGRE: Solo creamos si no existe un slot para ese profesional a esa hora
            // Usamos firstOrCreate para mayor seguridad
            AvailableSlot::firstOrCreate(
                [
                    'user_id'    => $schedule->user_id,
                    'start_time' => $auxStartTime,
                ],
                [
                    'end_time'   => $auxEndTime,
                    'status'     => 'available',
                    'capacity'   => 1
                ]
            );

            $auxStartTime->addMinutes($duration);
        }
    }
}
