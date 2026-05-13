<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpieza de tablas (Bypass de Foreign Keys)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('appointments')->truncate();

        // Ponemos todos los slots en available antes de empezar
        DB::table('available_slots')->update(['status' => 'available']);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Datos de Pacientes (Basado en tu lista confirmada)
        $patients = [
            ['id' => 18, 'name' => 'prueba1', 'last_name' => 'prueba1', 'modality' => 'presencial', 'cost' => 5000],
            ['id' => 20, 'name' => 'Juan', 'last_name' => 'Pérez', 'modality' => 'presencial', 'cost' => 5000],
            ['id' => 21, 'name' => 'María', 'last_name' => 'García', 'modality' => 'virtual', 'cost' => 7000],
            ['id' => 23, 'name' => 'Lucía', 'last_name' => 'Fernández', 'modality' => 'virtual', 'cost' => 5000],
            ['id' => 25, 'name' => 'Elena', 'last_name' => 'Díaz', 'modality' => 'presencial', 'cost' => 5000],
            ['id' => 28, 'name' => 'Diego', 'last_name' => 'Romero', 'modality' => 'virtual', 'cost' => 5000],
            ['id' => 29, 'name' => 'Paula', 'last_name' => 'Suárez', 'modality' => 'presencial', 'cost' => 5000],
            ['id' => 30, 'name' => 'Andrés', 'last_name' => 'Ruiz', 'modality' => 'virtual', 'cost' => 7000],
            ['id' => 32, 'name' => 'Pedro', 'last_name' => 'López', 'modality' => 'virtual', 'cost' => 7000],
            ['id' => 36, 'name' => 'sara', 'last_name' => 'jimenez', 'modality' => 'virtual', 'cost' => 5000],
        ];

        // Definimos los estados posibles
        $possibleStatuses = ['attended', 'absent', 'cancelled'];

        // 3. Obtener slots (User 2) ordenados
        $slots = DB::table('available_slots')
            ->where('user_id', 2)
            ->orderBy('start_time')
            ->get();

        $skipUntil = null;

        foreach ($slots as $slot) {
            $startTime = Carbon::parse($slot->start_time);

            // Saltar si el slot ya está cubierto por un turno largo previo
            if ($skipUntil && $startTime->lt($skipUntil)) {
                continue;
            }

            // Probabilidad de ocupación (70%)
            if (rand(1, 100) > 70) continue;

            $patient = $patients[array_rand($patients)];

            // Definir duración (30, 60, 90 min)
            $duration = [30, 60, 90][array_rand([0, 1, 2])];
            $endTime = $startTime->copy()->addMinutes($duration);
            $skipUntil = $endTime;

            // Lógica de estados según tiempo (Hoy es 11 de Mayo)
            $isPast = $startTime->isPast();
            $status = $isPast ? $possibleStatuses[array_rand($possibleStatuses)] : 'booked';
            $payment = ($status == 'attended' || rand(0, 1)) ? 'paid' : 'pending';

            // 4. Insertar el Appointment
            DB::table('appointments')->insert([
                'patient_id' => $patient['id'],
                'user_id' => 2,
                'title' => $patient['name'] . ' ' . $patient['last_name'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $status,
                'payment_status' => $payment,
                'is_extra' => 0,
                'cost' => $patient['cost'] ?? 5000,
                'modality' => strtolower($patient['modality']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. Marcar el slot como 'booked'
            DB::table('available_slots')
                ->where('id', $slot->id)
                ->update(['status' => 'booked']);
        }
    }
}
