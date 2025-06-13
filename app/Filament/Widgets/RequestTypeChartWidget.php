<?php

namespace App\Filament\Widgets;

use App\Models\UserRequest;
use App\Models\LaporanType;
use Filament\Widgets\ChartWidget;

class RequestTypeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Permintaan';

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
                'Permintaan (' . $typeData['permintaan'] . ')',
                'Pelaporan (' . $typeData['pelaporan'] . ')',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
