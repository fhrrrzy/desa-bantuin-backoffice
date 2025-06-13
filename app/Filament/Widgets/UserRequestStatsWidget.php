<?php

namespace App\Filament\Widgets;

use App\Models\UserRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserRequestStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Permintaan', UserRequest::count())
                ->description('Semua permintaan dan pelaporan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Sedang Diproses', UserRequest::where('status', 'onprocess')->count())
                ->description('Menunggu tindakan admin')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Selesai', UserRequest::where('status', 'accepted')->count())
                ->description('Permintaan yang telah selesai diproses')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Ditolak', UserRequest::where('status', 'rejected')->count())
                ->description('Permintaan yang ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
