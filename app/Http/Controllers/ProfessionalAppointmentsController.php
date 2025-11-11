<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProfessionalAppointmentsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfessionalAppointmentsController extends Controller
{
    protected $professionalAppointmentsService;

    public function __construct(ProfessionalAppointmentsService $professionalAppointmentsService)
    {
        $this->professionalAppointmentsService = $professionalAppointmentsService;
    }

    public function book(Request $request)
    {
        $request->validate([
            'available_slot_id' => 'required|exists:available_slots,id',
            'notes' => 'nullable|string|max:500',
            'patient_id' => 'required|exists:patients,id',
        ]);

        try {
            $appointment = $this->professionalAppointmentsService->bookSlot(
                $request->input('available_slot_id'),
                $request->input('patient_id'),                
                $request->all()
            );

            return response()->json([
                'message' => 'Cita reservada con éxito.',
                'appointment' => $appointment,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_CONFLICT);
        }
    }

    public function index(Request $request)
    {
        $appointments = $this->professionalAppointmentsService->getCalendarData($request);
        return response()->json($appointments);
    }
}