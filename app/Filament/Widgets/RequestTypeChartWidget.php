<?php

namespace App\Filament\Widgets;

use App\Models\UserRequest;
use App\Models\LaporanType;
use Filament\Widgets\ChartWidget;

class RequestTypeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Permintaan';

    protected static ?string $description = 'Distribusi berdasarkan jenis permintaan';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $laporanTypes = LaporanType::withCount('requests')->get();

        $typeData = [
            'permintaan' => UserRequest::where('type', 'permintaan')->count(),
            'pelaporan' => UserRequest::where('type', 'pelaporan')->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permintaan',
                    'data' => array_values($typeData),
                    'backgroundColor' => [
                        '#3b82f6', // Blue for permintaan
                        '#10b981', // Green for pelaporan
                    ],
                    'borderColor' => [
                        '#2563eb',
                        '#059669',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                'Permintaan',
                'Pelaporan',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
