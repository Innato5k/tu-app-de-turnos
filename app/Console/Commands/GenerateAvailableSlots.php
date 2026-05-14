<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProfessionalSchedule;
use App\Models\AvailableSlot;
use App\Models\BlackoutPeriod;
use App\Models\RecurringAppointment;
use App\Models\Appointment;
use Carbon\Carbon;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;

class GenerateAvailableSlots extends Command
{
    protected $signature = 'slots:generate';
    protected $description = 'Genera slots filtrando por bloqueos e inyectando recurrencias';

    public function handle()
    {
        $schedules = ProfessionalSchedule::with('user')->get();
        $this->info("Iniciando generación: " . $schedules->count() . " reglas.");

        foreach ($schedules as $schedule) {
            $startDate = now()->startOfDay();
            $endDate = now()->addMonths(2)->endOfDay();

            // --- PRE-CARGA DE CAPAS (Optimización i7-4790) ---
            $blackouts = BlackoutPeriod::where('user_id', $schedule->user_id)
                ->where('end_time', '>=', $startDate)
                ->get();

            $recurrences = RecurringAppointment::where('user_id', $schedule->user_id)->get();
            // --------------------------------------------------

            $currentDate = $startDate->clone();

            while ($currentDate->lte($endDate)) {
                if ($currentDate->dayOfWeek == $schedule->day_of_week) {
                    $isEffective = true;
                    if ($schedule->effective_start_date && $currentDate->lt($schedule->effective_start_date)) $isEffective = false;
                    if ($schedule->effective_end_date && $currentDate->gt($schedule->effective_end_date)) $isEffective = false;

                    if ($isEffective) {
                        $this->processSlotsForDay($currentDate, $schedule, $blackouts, $recurrences);
                    }
                }
                $currentDate->addDay();
            }
        }
        $this->info("¡Proceso completado!");
    }

    protected function processSlotsForDay($currentDate, $schedule, $blackouts, $recurrences)
    {
        $dateStr = $currentDate->format('Y-m-d');
        $startTime = Carbon::parse($dateStr . ' ' . $schedule->start_time);
        $endTime = Carbon::parse($dateStr . ' ' . $schedule->end_time);
        $duration = $schedule->slot_duration ?? 30;

        $auxStartTime = $startTime->clone();

        while ($auxStartTime->clone()->addMinutes($duration) <= $endTime) {
            $auxEndTime = $auxStartTime->clone()->addMinutes($duration);

            // --- CAPA 1: FILTRO DE BLOQUEOS ---
            $isBlocked = $blackouts->contains(function ($period) use ($auxStartTime, $auxEndTime) {
                return $auxStartTime->lt($period->end_time) && $auxEndTime->gt($period->start_time);
            });

            if (!$isBlocked) {
                // --- CAPA 2: GENERAR O RECUPERAR SLOT ---
                $slot = AvailableSlot::firstOrCreate(
                    ['user_id' => $schedule->user_id, 'start_time' => $auxStartTime],
                    ['end_time' => $auxEndTime, 'status' => 'available', 'capacity' => 1]
                );

                // --- CAPA 3: INYECCIÓN DE RECURRENCIA ---
                // Solo si el slot acaba de ser creado o está disponible
                if ($slot->status === 'available') {
                    $this->checkAndInjectRecurrence($slot, $recurrences);
                }
            }

            $auxStartTime->addMinutes($duration);
        }
    }

    protected function checkAndInjectRecurrence($slot, $recurrences)
    {
        foreach ($recurrences as $rec) {
            // Usamos la librería Recurr para ver si hoy toca
            $rule = new Rule($rec->rrule, $rec->start_date);
            $transformer = new ArrayTransformer();
            
            // Limitamos la búsqueda al día del slot para no procesar de más
            $constraint = new BetweenConstraint($slot->start_time->clone()->subMinute(), $slot->start_time->clone()->addMinute());
            $occurrence = $transformer->transform($rule, $constraint)->first();

            if ($occurrence && $occurrence->getStart()->format('H:i') === $slot->start_time->format('H:i')) {
                // ¡Coincidencia! Creamos el appointment
                Appointment::create([
                    'user_id' => $rec->user_id,
                    'patient_id' => $rec->patient_id,
                    'recurring_appointment_id' => $rec->id,
                    'available_slot_id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'status' => 'booked', // Nace ocupado
                    'payment_status' => 'pending',
                    'cost' => $rec->cost,
                    'modality' => $rec->modality,
                    'notes' => $rec->notes
                ]);

                // Actualizamos el slot
                $slot->update(['status' => 'booked']);
                break; // Un slot, una recurrencia
            }
        }
    }
}