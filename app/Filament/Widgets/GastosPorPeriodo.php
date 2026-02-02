<?php

namespace App\Filament\Widgets;

use App\Models\Gasto;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GastosPorPeriodo extends ChartWidget
{
    protected ?string $heading = 'Gastos por mes';

    protected function getData(): array
    {
        $anio = now()->year;

        // SQLite usa strftime
        $gastos = Gasto::select(
                DB::raw("strftime('%m', fecha) as mes"),
                DB::raw('SUM(monto) as total')
            )
            ->where(DB::raw("strftime('%Y', fecha)"), (string) $anio)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // Asegurar los 12 meses
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $mes = str_pad($i, 2, '0', STR_PAD_LEFT); // '01', '02', etc
            $data[] = $gastos[$mes] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total gastado ($)',
                    'data' => $data,
                ],
            ],
            'labels' => [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
