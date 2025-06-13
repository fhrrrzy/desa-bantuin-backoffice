<?php

namespace App\Filament\Widgets;

use App\Models\LaporanType;
use App\Models\UserRequest;
use Filament\Widgets\ChartWidget;

class LaporanTypeDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Berdasarkan Jenis Laporan';

    protected function getData(): array
    {
        $laporanTypes = LaporanType::withCount('requests')->get();

        $colors = [
            '#3b82f6', // Blue
            '#10b981', // Green
            '#f59e0b', // Yellow
            '#ef4444', // Red
            '#8b5cf6', // Purple
            '#06b6d4', // Cyan
            '#84cc16', // Lime
            '#f97316', // Orange
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permintaan',
                    'data' => $laporanTypes->pluck('requests_count')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $laporanTypes->count()),
                    'borderColor' => array_slice($colors, 0, $laporanTypes->count()),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $laporanTypes->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
