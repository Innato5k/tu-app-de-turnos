<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableSlot\AvailableSlotResource;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Services\ProfessionalAppointmentsService;
use App\Dtos\Appointment\AppointmentDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfessionalAppointmentsController extends Controller
{
    protected $professionalAppointmentsService;

    public function __construct(ProfessionalAppointmentsService $professionalAppointmentsService)
    {
        $this->professionalAppointmentsService = $professionalAppointmentsService;
    }

    public function book(StoreAppointmentRequest $request)
    {
        $dto = AppointmentDTO::fromRequest($request);
        $appointment = $this->professionalAppointmentsService->bookSlot($dto);

        return response()->json([
            'status'  => 'success',
            'message' => 'Turno reservado correctamente',
            'data'    => $appointment
        ], 201);
    }

    public function index(Request $request)
    {
        $appointments = $this->professionalAppointmentsService->getCalendarData($request);
        return AvailableSlotResource::collection($appointments);
    }
}