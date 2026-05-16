<?php

namespace App\Filament\Resources\RealisasiDanaResource\Pages;

use App\Filament\Resources\RealisasiDanaResource;
use App\Mail\RealisasiDanaMail;

use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Mail;

class EditRealisasiDana extends EditRecord
{
    protected static string $resource = RealisasiDanaResource::class;

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