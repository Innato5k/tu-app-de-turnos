<?php

namespace App\Services;

use App\Models\AvailableSlot;
use App\Models\Appointment;
use App\Services\PatientService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Dtos\Appointment\AppointmentDTO;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProfessionalAppointmentsService
{
    /**
     * Procesa la reserva de una cita.
     *
     * @param int $slotId El ID del slot disponible a reservar.
     * @param int $patientId El ID del cliente que reserva.
     * @param array $appointmentData Otros datos de la cita (ej. notas).
     * @return Appointment
     * @throws Exception
     */
    protected $patientService;
    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }


    public function bookSlot(AppointmentDTO $dto): Appointment
    {
        return DB::transaction(function () use ($dto) {
            $nextSlots = collect();
            $slot = AvailableSlot::lockForUpdate()->findOrFail($dto->slotId);
            if ($slot->status !== 'available') {
                throw new Exception('Este horario ya no está disponible.');
            }

            $slotCount = ceil($dto->duration / 30);
            if ($slotCount > 1.0) {
                $nextSlots = AvailableSlot::where('user_id', $slot->user_id)
                    ->where('start_time', '>=', Carbon::parse($slot->start_time)->addMinutes(30))
                    ->where('start_time', '<', Carbon::parse($slot->start_time)->addMinutes($dto->duration))
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->get();

                if ($nextSlots->count() < $slotCount - 1) {
                    throw new Exception('No hay suficientes slots consecutivos disponibles para la duración solicitada.');
                }
                $slot->update(['end_time' => $nextSlots->last()->end_time]);
            }

            $patient = $this->patientService->findPatientById($dto->patientId) ?? throw new Exception('Paciente no encontrado.');

            $slot->update(['status' => 'booked']);

            $appointment = Appointment::create([
                'user_id'           => $slot->user_id,
                'patient_id'        => $dto->patientId,
                'available_slot_id' => $slot->id,
                'start_time'        => $slot->start_time,
                'end_time'          => $slot->end_time,
                'title'             => $patient->name . ' ' . $patient->last_name,
                'status'            => 'booked',
                'notes'             => $dto->notes ?? null,
                'cost'              => $dto->cost ?? null,
                'modality'          => $dto->modality ?? null,
            ]);

            if (!$nextSlots->isEmpty()) {
                foreach ($nextSlots as $slot) {
                    $slot->update([
                        'status' => 'booked',
                        'appointment_id' => $appointment->id
                    ]);
                }
            }




            return $appointment;
        });
    }

    public function getCalendarData(Request $request): ?Collection
    {
        $userId = auth()->id();

        $startRaw = str_replace('/', '-', $request->query('start_date'));
        $endRaw = str_replace('/', '-', $request->query('end_date'));


        try {
            $startDate = \Carbon\Carbon::parse($startRaw)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endRaw)->endOfDay();

            // 1. Traemos solo los slots que NO están ocupados (Disponibles o Bloqueados)
            $freeSlots = AvailableSlot::where('user_id', $userId)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->where('status', '!=', 'booked')
                ->get();

            // 2. Traemos los Turnos reales (que ya traen su start y end de 1hs, 2hs, etc.)
            $appointments = Appointment::with('patient')
                ->where('user_id', $userId)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->get();

            // 3. Unimos ambas colecciones
            // Nota: El Resource deberá estar preparado para manejar ambos tipos de objeto
            return $freeSlots->concat($appointments);
        } catch (\Exception $e) {
            // Esto va a detener la ejecución y mostrarte el error exacto
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }
}
