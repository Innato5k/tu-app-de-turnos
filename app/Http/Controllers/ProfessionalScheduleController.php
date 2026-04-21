<?php

namespace App\Http\Controllers;

use App\Http\Requests\Schedule\StoreScheduleRequest;
use Illuminate\Http\Request;
use App\Services\ProfessionalScheduleService;
use App\DTOs\Schedule\ScheduleRequestDTO;
use App\Http\Resources\Schedule\ScheduleResource;

class ProfessionalScheduleController extends Controller
{
    protected $professionalScheduleService;

    /**
     * Constructor que inyecta el servicio de horarios profesionales.
     *
     * @param ProfessionalScheduleService $professionalScheduleService
     */
    public function __construct(ProfessionalScheduleService $professionalScheduleService)
    {
        $this->professionalScheduleService = $professionalScheduleService;
        $this->middleware('jwt.auth');
    }

    public function index(Request $request)
    {  
        $schedules = $this->professionalScheduleService->getAllSchedules($request, 'day_of_week');
        return ScheduleResource::collection($schedules);
    }

    public function store(StoreScheduleRequest $request)
    {
        $dto = ScheduleRequestDTO::fromRequest($request->validated());
        $schedules = $this->professionalScheduleService->store( $dto);

        return response()->json($schedules);
    }

    public function destroy(int $id)
    {
        $this->professionalScheduleService->delete($id);

        return response()->json(['message' => 'Schedule deleted successfully']);
    }
}
