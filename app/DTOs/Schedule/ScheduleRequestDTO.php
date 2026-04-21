<?php

namespace App\DTOs\Schedule;


use App\Http\Requests\User\StoreScheduleRequest;

 readonly class ScheduleRequestDTO
{
    public function __construct(
        public ?int $user_id,
        public array $days_of_week,
        public string $start_time,
        public string $end_time,
        public ?string $effective_start_date,
        public ?string $effective_end_date,
        public ?int $slot_duration,
        public ?string $observations = null,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            user_id: $request['user_id'] ?? null,
            days_of_week: $request['days_of_week'],
            start_time: $request['start_time'],
            end_time: $request['end_time'],
            effective_start_date: $request['effective_start_date'] ?? null,
            effective_end_date: $request['effective_end_date'] ?? null,
            slot_duration: $request['slot_duration'] ?? 30 ,   
            observations: $request['observations'] ?? null,                    
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
