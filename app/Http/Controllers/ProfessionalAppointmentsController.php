<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableSlot\AvailableSlotResource;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Requests\Appointment\StoreExtraAppointmentRequest;
use App\DTOs\Appointment\ExtraAppointmentDTO;
use App\Services\ProfessionalAppointmentsService;
use App\DTOs\Appointment\AppointmentDTO;
use App\DTOs\Appointment\AppointmentUpdateDTO;
use App\Http\Resources\Appointment\AppointmentResource;
use Illuminate\Http\Request;

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

    public function show(Request $request, $id)
    {
        $appointment = $this->professionalAppointmentsService->getAppointmentsById($request, $id);
        return new AppointmentResource($appointment);
    }

    public function update(UpdateAppointmentRequest $request,$id)
    {
        $dto = AppointmentUpdateDTO::fromRequest($request->validated());
        $appointment = $this->professionalAppointmentsService->updateAppointment($dto,$id);
        return new AppointmentResource($appointment);
    }

    public function bookExtra(StoreExtraAppointmentRequest $request){
        $dto = ExtraAppointmentDTO::fromRequest($request);
        $appointment = $this->professionalAppointmentsService->bookExtraSlot($dto);

        return response()->json([
            'status'  => 'success',
            'message' => 'Turno reservado correctamente',
            'data'    => $appointment
        ], 201);
    }
}
