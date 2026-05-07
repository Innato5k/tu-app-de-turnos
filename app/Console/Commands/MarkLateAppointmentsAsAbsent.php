<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment; // Asegúrate de que este sea tu modelo de turnos
use Carbon\Carbon;

class MarkLateAppointmentsAsAbsent extends Command
{
    protected $signature = 'appointments:mark-absent';
    protected $description = 'Marca como ausentes los turnos pendientes que tienen más de 24hs de antigüedad';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = Carbon::now()->subHours(24);
        $affectedRows = Appointment::where('status', Appointment::STATUS_BOOKED)
            ->where('start_time', '<', $yesterday)
            ->update(['status' => Appointment::STATUS_ABSENT]);

        $this->info("Proceso completado. Se marcaron {$affectedRows} turnos como ausentes.");
        
        return Command::SUCCESS;
    }
}
