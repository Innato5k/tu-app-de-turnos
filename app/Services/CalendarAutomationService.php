<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class CalendarAutomationService
{
    /**
     * Llama al comando de generación de slots.
     * En el futuro, aquí podés filtrar por rango de fechas para que tu i7 no sufra.
     */
    
    /*Limpiar disponibles antes de generar nuevos (opcional, dependiendo de tu lógica de negocio)
     *AvailableSlot::where('user_id', $userId)
            ->where('status', 'available')
            ->whereBetween('start_time', [$startDate, $endDate])
            ->delete();
     *
     */
    public function syncSlots()
    {
        Artisan::call('slots:generate');
    }
}