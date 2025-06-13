<?php

namespace App\Filament\Resources\UserRequestResource\Pages;

use App\Filament\Resources\UserRequestResource;
use App\Models\LaporanType;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListUserRequests extends ListRecords
{
    protected static string $resource = UserRequestResource::class;

    protected function getHeaderActions(): array
    {
        // Only show create action for warga users
        if (Auth::user()->role === 'warga') {
            return [
                Actions\CreateAction::make(),
            ];
        }

        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Status')
                ->icon('heroicon-o-document-text')
                ->badge(UserRequestResource::getEloquentQuery()->count())
                ->badgeColor('gray'),

            'onprocess' => Tab::make('Sedang Diproses')
                ->icon('heroicon-o-clock')
                ->badge(UserRequestResource::getEloquentQuery()->where('status', 'onprocess')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'onprocess'))
                ->badgeColor('warning'),

            'accepted' => Tab::make('Selesai')
                ->icon('heroicon-o-check-circle')
                ->badge(UserRequestResource::getEloquentQuery()->where('status', 'accepted')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'accepted'))
                ->badgeColor('success'),

            'rejected' => Tab::make('Ditolak')
                ->icon('heroicon-o-x-circle')
                ->badge(UserRequestResource::getEloquentQuery()->where('status', 'rejected')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected'))
                ->badgeColor('danger'),
        ];
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 2;
    }

    // protected function getTableFilters(): array
    // {
    //     return [
    //         SelectFilter::make('laporan_type_id')
    //             ->label('Jenis Laporan')
    //             ->options(LaporanType::pluck('name', 'id'))
    //             ->multiple()
    //             ->badge()
    //             ->badgeColor('info')
    //             ->native(false),
    //         SelectFilter::make('type')
    //             ->label('Tipe')
    //             ->options([
    //                 'permintaan' => 'Permintaan',
    //                 'pelaporan' => 'Pelaporan',
    //             ])
    //             ->multiple()
    //             ->badge()
    //             ->badgeColor('success')
    //             ->native(false),
    //     ];
    // }
}
