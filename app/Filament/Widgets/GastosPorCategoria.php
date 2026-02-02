<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GastosPorCategoria extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Gastos por categoría';
    }

    protected function getData(): array
    {
        $gastos = DB::table('gastos')
            ->join('categorias', 'gastos.categoria_id', '=', 'categorias.id')
            ->selectRaw('categorias.nombre as categoria, SUM(gastos.monto) as total')
            ->groupBy('categorias.nombre')
            ->orderBy('total', 'desc')
            ->pluck('total', 'categoria');

        return [
            'datasets' => [
                [
                    'label' => 'Total gastado ($)',
                    'data' => $gastos->values(),
                ],
            ],
            'labels' => $gastos->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // también puedes usar 'pie' o 'doughnut'
    }
}
