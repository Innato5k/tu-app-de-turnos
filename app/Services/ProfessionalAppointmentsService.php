<?php

namespace App\Services;

use App\Models\AvailableSlot;
use App\Models\Appointment;
use App\Services\PatientService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Dtos\Appointment\AppointmentDTO;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;

use function PHPUnit\Framework\isEmpty;

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
        // Bloqueamos el registro para que nadie más lo toque hasta el commit
        $slot = AvailableSlot::lockForUpdate()->findOrFail($dto->slotId);

        if ($slot->status !== 'available') {
            throw new Exception('Este horario ya no está disponible.');
        }

        $patient = $this->patientService->findPatientById($dto->patientId);
        if (!$patient) {
            throw new Exception('Paciente no encontrado.');
        }

        $slot->update(['status' => 'booked']);

        return Appointment::create([
            'user_id'           => $slot->user_id,
            'patient_id'        => $dto->patientId,
            'available_slot_id' => $slot->id,
            'start_time'        => $slot->start_time,
            'end_time'          => $slot->end_time,
            'title'             => $patient->name . ' ' . $patient->last_name,
            'status'            => 'confirmed', // O 'pending' según tu flujo
            'notes'             => $dto->notes ?? null,
            // Agregamos lo que tenías
            'cost'              => $dto->cost ?? null,
            'modality'          => $dto->modality ?? null,
        ]);
    });
}

    public function getCalendarData(Request $request) :?Collection
    {
        $userId = auth()->id();
        
        $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->query('start_date'))->startOfDay();
        $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->query('end_date'))->endOfDay();

        return AvailableSlot::with(['appointment.patient'])
            ->where('user_id', $userId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->get();
    }
}
