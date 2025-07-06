<?php

namespace App\Filament\Resources\LaporanTypeResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\LaporanTypeResource;
use Filament\Actions\CreateAction;

class ListLaporanTypes extends ListRecords
{
    protected static string $resource = LaporanTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
