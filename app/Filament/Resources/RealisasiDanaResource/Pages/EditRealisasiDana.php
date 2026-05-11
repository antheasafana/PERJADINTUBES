<?php

namespace App\Filament\Resources\RealisasiDanaResource\Pages;

use App\Filament\Resources\RealisasiDanaResource;
use App\Mail\RealisasiDanaMail;

use Filament\Resources\Pages\EditRecord;

use Illuminate\Support\Facades\Mail;

class EditRealisasiDana extends EditRecord
{
    protected static string $resource = RealisasiDanaResource::class;

    protected function afterSave(): void
    {
        $realisasi = $this->record;

        // kirim email hanya jika status selesai
        if ($realisasi->alisasi == 'selesai') {

            $pegawai = $realisasi
                ->pengajuan
                ->pegawai;

            if ($pegawai && $pegawai->user) {

                Mail::to($pegawai->user->email)
                    ->send(new RealisasiDanaMail($realisasi));
            }
        }
    }
}