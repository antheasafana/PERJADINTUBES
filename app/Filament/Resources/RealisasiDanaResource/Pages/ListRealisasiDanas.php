<?php

namespace App\Filament\Resources\RealisasiDanaResource\Pages;

use App\Filament\Resources\RealisasiDanaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRealisasiDana extends ListRecords
{
    protected static string $resource = RealisasiDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}