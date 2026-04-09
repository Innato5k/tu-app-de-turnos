<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProfessionalScheduleService;
use App\Models\ProfessionalSchedule;
use Hamcrest\Arrays\IsArray;

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

        return response()->json($schedules);
    }

    public function create(array $data): ?ProfessionalSchedule
    {
        return null;
    }

    public function store(Request $request)
    {
         $request->validate([
            'days_of_week' => 'required|array',
        ]);
        $schedules = $this->professionalScheduleService->store( $request, 'start_time');

        return response()->json($schedules);
    }

    public function show(int $id)
    {
        $schedule = $this->professionalScheduleService->findScheduleById($id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        return response()->json($schedule);
    }

    public function showByUserId(int $id)
    {
        $schedule = $this->professionalScheduleService->findScheduleByUserId($id);

        if (!$schedule) {
            return response()->json(['message' => 'User Schedule not found'], 404);
        }

        return response()->json($schedule);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'day_of_week' => 'sometimes|integer|min:0|max:6',
            'effective_start_date' => 'nullable|date',
            'effective_end_date' => 'nullable|date|after_or_equal:effective_start_date',
        ]);

        $schedule = $this->professionalScheduleService->updateSchedule($id, $request);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        return response()->json($schedule);
    }

    public function destroy(int $id)
    {
        //TODO: pasar al service la logica de búsqueda y eliminación del horario
        
        $schedule = $this->professionalScheduleService->findScheduleById($id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully']);
    }
}
