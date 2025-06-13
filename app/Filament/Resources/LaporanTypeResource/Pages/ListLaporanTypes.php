<?php

namespace App\Filament\Resources\LaporanTypeResource\Pages;

use App\Filament\Resources\LaporanTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanTypes extends ListRecords
{
    protected static string $resource = LaporanTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action allowed - read-only
        ];
    }
}
