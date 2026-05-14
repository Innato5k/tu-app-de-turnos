<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\BlackoutPeriod;
use App\Models\RecurringAppointment;
use Carbon\Carbon;

class TestAppointmentLogicSeeder extends Seeder
{
    public function run()
    {
        // 1. Buscamos a Matías (User ID 2 según tus logs anteriores)
        $user = User::find(2);
        if (!$user) {
            $this->command->error("No se encontró el usuario con ID 2");
            return;
        }

        // 2. Buscamos o creamos un paciente de prueba
        $patient = Patient::first() ?? Patient::create([
            'name' => 'Paciente',
            'last_name' => 'De Prueba',
            'cuil' => '20304050607',
            'email' => 'test@example.com',
            'phone' => '12345678',
            'is_active' => true
        ]);

        // --- ESCENARIO CAPA 1: EL BLOQUEO (Vacaciones) ---
        // Bloqueamos la semana que viene completa (Lunes a Viernes)
        BlackoutPeriod::updateOrCreate(
            ['user_id' => $user->id, 'reason' => 'Vacaciones de Invierno'],
            [
                'start_time' => Carbon::now()->addWeek()->startOfWeek()->setHour(0)->setMinute(0),
                'end_time'   => Carbon::now()->addWeek()->startOfWeek()->addDays(5)->setHour(23)->setMinute(59),
                'is_all_day' => true
            ]
        );

        // --- ESCENARIO CAPA 3: LA RECURRENCIA ---
        // Creamos una regla: "Todos los lunes a las 10:00 AM" (asumiendo que tenés slot de 10am)
        // El formato RRULE: Frecuencia Semanal, por día Lunes (MO)
        RecurringAppointment::updateOrCreate(
            ['user_id' => $user->id, 'patient_id' => $patient->id],
            [
                'rrule' => 'FREQ=WEEKLY;BYDAY=MO;INTERVAL=1', 
                'start_date' => Carbon::now()->startOfYear(), // Retroactivo para que aplique hoy
                'cost' => 15000.00,
                'modality' => 'presencial',
                'notes' => 'Tratamiento recurrente de prueba'
            ]
        );

        $this->command->info("Seeder de prueba ejecutado: Vacaciones cargadas y regla recurrente activa.");
    }
}