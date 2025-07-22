<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProfessionalScheduleService;
use App\Models\ProfessionalSchedule;


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
                // Llama al servicio para obtener los horarios profesionales filtrados y paginados
        $schedules = $this->professionalScheduleService->getAllSchedules($request, 'start_time');

        return response()->json($schedules);
    }

    public function create(array $data): ?ProfessionalSchedule
    {
        return null;
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'day_of_week' => 'required|integer|min:0|max:6', // 0 = Domingo, 6 = Sabado
            'effective_start_date' => 'nullable|date',
            'effective_end_date' => 'nullable|date|after_or_equal:effective_start_date',
        ]);


        $schedules = $this->professionalScheduleService->store($request, 'start_time');

        return response()->json($schedules);
    }
}
