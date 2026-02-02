<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GastosPorSemana extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Gastos de la semana (Lunes a Domingo)';
    }

    protected function getData(): array
    {
        // Lunes a domingo de la semana actual
        $inicioSemana = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $finSemana    = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        // Crear los 7 días (lunes → domingo)
        $dias = collect(range(0, 6))->map(fn ($i) =>
            $inicioSemana->copy()->addDays($i)
        );

        // Obtener gastos agrupados por día
        $gastos = DB::table('gastos')
            ->selectRaw('DATE(fecha) as dia, SUM(monto) as total')
            ->whereBetween('fecha', [
                $inicioSemana->toDateString(),
                $finSemana->toDateString(),
            ])
            ->groupBy('dia')
            ->pluck('total', 'dia');

        return [
            'datasets' => [
                [
                    'label' => 'Gastos ($)',
                    'data' => $dias->map(fn ($dia) =>
                        $gastos[$dia->toDateString()] ?? 0
                    ),
                ],
            ],
            'labels' => $dias->map(fn ($dia) =>
                $dia->translatedFormat('l') // lunes, martes, etc.
            ),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
