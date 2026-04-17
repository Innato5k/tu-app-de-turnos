<?php

namespace App\Http\Resources\Schedule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'professional_id' => $this->professional_id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'effective_start_date' => $this->effective_start_date?->format('Y-m-d'),
            'effective_end_date' => $this->effective_end_date?->format('Y-m-d'),
            'slot_duration' => $this->slot_duration,
            'observations'  => $this->observations,
        ];
    }
}