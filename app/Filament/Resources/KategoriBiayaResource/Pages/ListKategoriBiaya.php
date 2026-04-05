<?php

namespace App\Filament\Resources\KategoriBiayaResource\Pages;

use App\Filament\Resources\KategoriBiayaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriBiaya extends ListRecords
{
    protected static string $resource = KategoriBiayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
