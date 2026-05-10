<?php

namespace App\Filament\Resources\RealisasiDanaResource\Pages;

use App\Filament\Resources\RealisasiDanaResource;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateRealisasiDana extends CreateRecord
{
    protected static string $resource =
        RealisasiDanaResource::class;

    protected ?string $maxContentWidth = 'full';

    protected function afterCreate(): void
    {
        $this->record->pengajuan->update([
            'status_pengajuan' => 'Direalisasi',
        ]);

        Notification::make()
            ->title('Realisasi dana berhasil dibuat')
            ->success()
            ->send();
    }
}