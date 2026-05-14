<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlackoutPeriodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'start'      => $this->start_time->toIso8601String(),
            'end'        => $this->end_time->toIso8601String(),
            'title'      => $this->reason,
            'is_all_day' => (bool)$this->is_all_day,
            'type'       => 'blackout', // Metadata para que el JS sepa qué es
        ];
    }
}