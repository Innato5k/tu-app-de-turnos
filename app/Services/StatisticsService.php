<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Obtiene las métricas del mes cerrado (mes anterior).
     */
    public function getClosedMonthStats()
    {
        $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfMonth = Carbon::now()->subMonth()->endOfMonth();

        return [
            'attendance' => $this->getAttendanceData($startOfMonth, $endOfMonth),
            'financial'  => $this->getFinancialData($startOfMonth, $endOfMonth),
            'debtors'    => $this->getTopDebtors(),
        ];
    }

    /**
     * Lógica para el gráfico de Dona (Asistencia + Extras)
     */
    private function getAttendanceData($start, $end)
    {
        return Appointment::withTrashed()
            ->whereBetween('start_time', [$start, $end])
            ->select('status', DB::raw('count(*) as total'), 'is_extra')
            ->groupBy('status', 'is_extra')
            ->get();
    }

    /**
     * Lógica financiera con regla de 24hs para cancelados
     */
    private function getFinancialData($start, $end)
    {
        $appointments = Appointment::withTrashed()
            ->whereBetween('start_time', [$start, $end])
            ->get();

        $facturado = 0;
        $adeudado = 0;

        foreach ($appointments as $appo) {
            $esCobrable = false;

            // 1. Si asistió o se ausentó, es cobrable por defecto
            if (in_array($appo->status, [Appointment::STATUS_ATTENDED, Appointment::STATUS_ABSENT])) {
                $esCobrable = true;
            }

            // 2. Si se canceló (Soft Delete), aplicamos la regla de las 24hs
            /*
            if ($appo->trashed()) {
                $horasAnticipacion = Carbon::parse($appo->deleted_at)
                    ->diffInHours($appo->start_time, false);

                if ($horasAnticipacion < 24) {
                    $esCobrable = true;
                }
            }*/

            // 3. Clasificamos según el estado de pago del turno cobrable
            if ($esCobrable) {
                if ($appo->payment_status === 'paid') {
                    $facturado += $appo->cost;
                } else {
                    $adeudado += $appo->cost;
                }
            }
        }

        return [
            'total_facturado' => $facturado,
            'total_adeudado' => $adeudado,
            'ratio_deuda' => ($facturado + $adeudado) > 0
                ? round(($adeudado / ($facturado + $adeudado)) * 100, 2)
                : 0
        ];
    }

    private function getTopDebtors()
    {
        // Retornamos los pacientes con turnos 'present' o 'absent' sin pagar
        return Appointment::whereIn('status', ['present', 'absent'])
            ->where('payment_status', 'pending')
            ->with('patient:id,name,last_name')
            ->orderBy('start_time', 'desc')
            ->limit(5)
            ->get();
    }
}
