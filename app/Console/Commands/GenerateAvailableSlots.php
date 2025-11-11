<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProfessionalSchedule;
use App\Models\AvailableSlot;
use Carbon\Carbon;

class GenerateAvailableSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slots:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera slots de turnos disponibles para los profesionales según sus horarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $schedules = ProfessionalSchedule::all();

        foreach ($schedules as $schedule) {
            $startDate = now()->startOfDay();
            $endDate = now()->addMonths(2)->endOfDay();            
            
            $currentDate = $startDate->clone();
            while ($currentDate->lte($endDate)) {
                if ($currentDate->dayOfWeek == $schedule->day_of_week) {
                    // horarios con fechas de inicio/fin
                    if ($schedule->effective_start_date && $schedule->effective_end_date) {
                        if ($currentDate->between($schedule->effective_start_date, $schedule->effective_end_date)) {
                            $this->createAvailableSlots($currentDate->format('Y-m-d'), $schedule);
                        }
                    } else {
                        // Si no tiene rango de fechas, es "siempre"
                        $this->createAvailableSlots($currentDate->format('Y-m-d'), $schedule);
                    }
                }
                $currentDate->addDay();                
            }
        }
    }

    // crear el slot disponible
    protected function createAvailableSlots($date, $schedule)
    {  
        $startAvailableSlotsTime = Carbon::parse( $date . ' ' . $schedule->start_time->format('H:i:s'));
        $endAvailableSlotsTime = Carbon::parse( $date . ' ' . $schedule->end_time->format('H:i:s'));
        
        $existingAvailableSlots = AvailableSlot::where([
            'user_id' => $schedule->user_id,
            'start_time' => $startAvailableSlotsTime
        ])->first();

        $auxStartTime = clone $startAvailableSlotsTime;
        //fin de espacio disponible cada 30 minutos
        $auxEndTime = clone $auxStartTime;
        $auxEndTime = $auxEndTime->addMinutes(30);
        
        if (!$existingAvailableSlots) {
            while ($auxEndTime <= $endAvailableSlotsTime) {                
                AvailableSlot::create([
                    'user_id' => $schedule->user_id,
                    'start_time' => $auxStartTime,
                    'end_time' => $auxEndTime,
                    'status' => 'available',
                    'capacity' => 1,
                ]);
                $auxStartTime = $auxStartTime->addMinutes(30);
                $auxEndTime = $auxEndTime->addMinutes(30);
            }
        }
    }
}
