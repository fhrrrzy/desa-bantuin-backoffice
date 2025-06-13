<?php

namespace App\Filament\Resources\LaporanTypeResource\Pages;

use App\Filament\Resources\LaporanTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanType extends EditRecord
{
    protected static string $resource = LaporanTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No delete action allowed - read-only
        ];
    }
}
