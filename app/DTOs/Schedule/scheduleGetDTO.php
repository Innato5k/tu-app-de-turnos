<?php

namespace App\DTOs\Schedule;

use App\Http\Requests\User\StoreScheduleRequest;

 readonly class ScheduleGetDTO
{
    public function __construct(
        public string $start_time,
        public string $end_time,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            start_time: $request['start_time'],
            end_time: $request['end_time'],            
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
