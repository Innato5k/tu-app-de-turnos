<?php

namespace App\Services;

use App\Models\AvailableSlot;
use App\Models\Appointment;
use App\Services\PatientService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;
use Carbon\Exceptions\InvalidFormatException;
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
    public function __construct(PatientService $patientService )
    {
        $this->patientService = $patientService;
    }

  
    public function bookSlot(int $slotId, int $patientId, array $appointmentData): Appointment
    {
        DB::beginTransaction();

        try {

            $slot = AvailableSlot::lockForUpdate()->findOrFail($slotId);


            if ($slot->status !== 'available') {
                throw new Exception('Este horario ya no está disponible.');
            }

            $auxPaciente = $this->patientService->findPatientById($patientId);

            if (!$auxPaciente) {
                throw new Exception('Paciente no encontrado.');
            }

            $slot->update([
                'status' => 'booked',
            ]);

            //Crea un nuevo registro en la tabla de citas
            $appointment = Appointment::create([
                'user_id' => $slot->user_id,
                'patient_id' => $patientId,
                'available_slot_id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'title' => $auxPaciente->name . ' ' . $auxPaciente->last_name,
                'status' => 'booked',
                'notes' => $appointmentData['notes'] ?? null,
                'cost' => $appointmentData['cost'] ?? null,
                'modality' => $appointmentData['modality'] ?? null,

            ]);

            DB::commit();

            return $appointment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getCalendarData(Request $request)
    {
        // Obtener el ID del profesional autenticado
        $userId = auth()->user()->id;
        // Obtener las fechas del calendario
        $startDateInput = $request->query('start_date');
        $endDateInput = $request->query('end_date');
        try {
            $startDate = Carbon::createFromFormat('d/m/Y', $startDateInput)->startOfDay()->setTimezone('UTC');
            $endDate = Carbon::createFromFormat('d/m/Y', $endDateInput)->endOfDay()->setTimezone('UTC');
        } catch (InvalidFormatException $e) {
            return response()->json([
                'message' => 'El formato de fecha es incorrecto. Se espera DD/MM/YYYY.',
                'input_received' => $startDateInput,
            ], 422);
        }

        //TODO: Optimizar no usar las dos consultas.
        $availableSlots = AvailableSlot::where('user_id', $userId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->get();

        $Appointments = Appointment::where('user_id', $userId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->get()
            ->keyBy('available_slot_id');

        // Unificar los datos 
        $combinedData = [];
        foreach ($availableSlots as $slot) {
            $status = $slot->status; 
            $title = 'Disponible';

            if (isset($Appointments[$slot->id])) {
                $status = $Appointments[$slot->id]->status;
                $client = $this->patientService->findPatientById($Appointments[$slot->id]->patient_id);
                $title = $client->name . ' ' . $client->last_name;
                Log::info("Cita encontrada para el slot ID {$slot->id} con paciente {$title}");
            }

            $combinedData[] = [
                'id' => $slot->id,
                'title' => $title,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,     
                'extendedProps' => [
                    'status' => $status,
                ],
            ];
        }

        // Devolver la respuesta en formato JSON
        return response()->json($combinedData);
    }
}
