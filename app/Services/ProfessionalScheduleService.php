<?php

namespace App\Services;

use App\Models\ProfessionalSchedule;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ProfessionalScheduleService
{
    /**
     * Obtiene todos los horarios profesionales.
     *
     * @return \Illuminate\Database\Eloquent\Collection<ProfessionalSchedule>
     */
    public function getAllSchedules(Request $request, string $orderBy = 'effective_start_date'): Collection
    {

        // Aquí podrías agregar lógica para filtrar o paginar los horarios si es necesario
        return ProfessionalSchedule::orderBy($orderBy)->get();
    }

    public function findScheduleById(int $id): ?ProfessionalSchedule
    {
        return ProfessionalSchedule::find($id);
    }

    public function findScheduleByUserId(int $id, string $orderBy = 'effective_start_date'): ?Collection
    {
        return ProfessionalSchedule::where('user_id', $id)->orderBy($orderBy)->get();
    }

    public function updateSchedule(int $id, Request $request): ?ProfessionalSchedule
    {
        $schedule = $this->findScheduleById($id);

        if (!$schedule) {
            return null;
        }

        if ($request->input('start_time') >= $request->input('end_time')) {
            throw ValidationException::withMessages([
                'start_time' => ['The start time must be before the end date-time.'],
            ]);
        }


        if ($this->hasOverlap(
            $request->input('user_id'),
            $request->input('day_of_week'),
            $request->input('start_time'),
            $request->input('end_time'),
            $request->input('effective_start_date'),
            $request->input('effective_end_date')
        )) {
            throw ValidationException::withMessages([
                'schedule' => ['The schedule overlaps with an existing one.'],
            ]);
        }

        $data = $request->only([
            'user_id',
            'day_of_week',
            'start_time',
            'end_time',
            'effective_start_date',
            'effective_end_date',
        ]);

        // Validación de datos
        if (ProfessionalSchedule::where('user_id', $data['user_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('start_time', $data['start_time'])
            ->where('end_time', $data['end_time'])
            ->where('id', '!=', $id)
            ->exists()
        ) {
            throw ValidationException::withMessages([
                'schedule' => ['The schedule overlaps with an existing one.'],
            ]);
        }

        $schedule->update($data);
        return $schedule;
    }

    public function store(Request $request): ProfessionalSchedule
    {

        if ($request->input('start_time') >= $request->input('end_time')) {
            throw ValidationException::withMessages([
                'start_time' => ['The start time must be before the end time.'],
            ]);
        }

        if ($this->hasOverlap(
            $request->input('user_id'),
            $request->input('day_of_week'),
            $request->input('start_time'),
            $request->input('end_time'),
            $request->input('effective_start_date'),
            $request->input('effective_end_date')
        )) {
            throw ValidationException::withMessages([
                'schedule' => ['The schedule overlaps with an existing one.'],
            ]);
        }

        return ProfessionalSchedule::create([
            'user_id' => $request->input('user_id'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'day_of_week' => $request->input('day_of_week'),
            'effective_start_date' => $request->input('effective_start_date') ?? null,
            'effective_end_date' => $request->input('effective_end_date') ?? null,
        ]);
    }

    public function hasOverlap(
        int $userId,
        string $dayOfWeek,
        string $newStartTime,
        string $newEndTime,
        ?string $newEffectiveStartDate,
        ?string $newEffectiveEndDate,
        ?int $excludeScheduleId = null
    ): bool {
        // Convertir las horas de inicio y fin del nuevo horario a formato de comparación
        $newStartCarbonTime = Carbon::parse($newStartTime);
        $newEndCarbonTime = Carbon::parse($newEndTime);

        // Convertir las fechas de efectividad del nuevo horario a objetos Carbon
        // Si son null, usar valores mínimos/máximos para representar "sin límite"
        $newEffectiveStart = $newEffectiveStartDate ? Carbon::parse($newEffectiveStartDate)->startOfDay() : Carbon::minValue();
        $newEffectiveEnd = $newEffectiveEndDate ? Carbon::parse($newEffectiveEndDate)->endOfDay() : Carbon::maxValue();

        // Consulta para obtener horarios existentes para el mismo profesional y día de la semana
        $query = ProfessionalSchedule::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->where('deleted_at', null); // Excluir horarios eliminados

        // Si estamos actualizando un horario, excluimos el propio horario de la verificación
        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        $existingSchedules = $query->get();

        foreach ($existingSchedules as $existingSchedule) {
            // Convertir las horas de inicio y fin del horario existente
            $existingStartCarbonTime = Carbon::parse($existingSchedule->start_time);
            $existingEndCarbonTime = Carbon::parse($existingSchedule->end_time);

            // Convertir las fechas de efectividad del horario existente
            $existingEffectiveStart = $existingSchedule->effective_start_date ? Carbon::parse($existingSchedule->effective_start_date)->startOfDay() : Carbon::minValue();
            $existingEffectiveEnd = $existingSchedule->effective_end_date ? Carbon::parse($existingSchedule->effective_end_date)->endOfDay() : Carbon::maxValue();

            // 1. Verificar superposición de tiempo (horario del día)
            // Dos rangos de tiempo [A, B] y [C, D] se superponen si A < D Y C < B
            $timeOverlap = $newStartCarbonTime->lt($existingEndCarbonTime) && $existingStartCarbonTime->lt($newEndCarbonTime);

            // 2. Verificar superposición de fechas de efectividad
            // Dos rangos de fechas [DS1, DE1] y [DS2, DE2] se superponen si DS1 <= DE2 Y DS2 <= DE1
            $dateOverlap = $newEffectiveStart->lessThanOrEqualTo($existingEffectiveEnd) && $existingEffectiveStart->lessThanOrEqualTo($newEffectiveEnd);

            // Si hay superposición tanto en tiempo como en fechas de efectividad, entonces hay un conflicto
            if ($timeOverlap && $dateOverlap) {
                return true; // Se encontró una superposición
            }
        }

        return false; // No se encontraron superposiciones
    }
}
