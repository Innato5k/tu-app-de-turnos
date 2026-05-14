<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Tomamos el primer usuario (seguramente vos) para asignarle la autoría
    $adminId = \App\Models\User::first()->id ?? 1;

    \App\Models\Announcement::create([
        'user_id'   => $adminId,
        'title'     => 'Mejoras en la estabilidad',
        'message'   => 'Se optimizó la conexión con la API de frases y el mapeo de autores.',
        'type'      => 'success',
        'is_active' => true,
    ]);

    \App\Models\Announcement::create([
        'user_id'   => $adminId,
        'title'     => 'Mantenimiento programado',
        'message'   => 'El domingo de 02:00 a 04:00 el sistema estará fuera de servicio por backups.',
        'type'      => 'warning',
        'is_active' => true,
    ]);
}
}
