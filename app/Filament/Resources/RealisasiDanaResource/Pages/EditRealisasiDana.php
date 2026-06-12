<?php

namespace App\Filament\Resources\RealisasiDanaResource\Pages;

use App\Filament\Resources\RealisasiDanaResource;
use Filament\Resources\Pages\EditRecord;

class EditRealisasiDana extends EditRecord
{
    protected static string $resource = RealisasiDanaResource::class;
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = 'TEREALISASI';

        return $data;
        }
    public function getTitle(): string
    {
        return 'Realisasi Pengajuan';
    }
    
    /*
    |--------------------------------------------------------------------------
    | AFTER SAVE
    |--------------------------------------------------------------------------
    */

    protected function afterSave(): void
    {
        $realisasi = $this->record;

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        $realisasi->status = 'TEREALISASI';
        $realisasi->save();

        /*
        |--------------------------------------------------------------------------
        | KIRIM EMAIL
        |--------------------------------------------------------------------------
        */

        $pegawai = $realisasi->pengajuan?->pegawai;

        if ($pegawai?->user?->email) {

             try {

        Mail::to($pegawai->user->email)
            ->send(new RealisasiDanaMail($realisasi));

    } catch (\Exception $e) {

        dd($e->getMessage());
    }
        }

        /*
        |--------------------------------------------------------------------------
        | NOTIFICATION
        |--------------------------------------------------------------------------
        */

        Notification::make()
        ->title('Pengajuan #' . $realisasi->id_pengajuan . ' direalisasi')
        ->success()
        ->sendToDatabase(auth()->user());
    }

    /*
    |--------------------------------------------------------------------------
    | UBAH TOMBOL SAVE
    |--------------------------------------------------------------------------
    */

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Realisasikan');
    }

    /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}