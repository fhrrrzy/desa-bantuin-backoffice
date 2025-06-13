<?php

namespace App\Filament\Widgets;

use App\Models\UserRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UserRequestChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Permintaan';

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(function ($day) {
            $date = Carbon::now()->subDays($day);

            return [
                'date' => $date->format('d M'),
                'onprocess' => UserRequest::where('status', 'onprocess')
                    ->whereDate('created_at', $date)
                    ->count(),
                'accepted' => UserRequest::where('status', 'accepted')
                    ->whereDate('created_at', $date)
                    ->count(),
                'rejected' => UserRequest::where('status', 'rejected')
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Sedang Diproses',
                    'data' => $days->pluck('onprocess')->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#fef3c7',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Selesai',
                    'data' => $days->pluck('accepted')->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#d1fae5',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Ditolak',
                    'data' => $days->pluck('rejected')->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#fee2e2',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $days->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
