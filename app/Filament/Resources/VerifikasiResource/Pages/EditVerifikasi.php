<?php

namespace App\Filament\Resources\VerifikasiResource\Pages;

use App\Filament\Resources\VerifikasiResource;
use App\Models\RealisasiDana;
use Filament\Resources\Pages\EditRecord;

class EditVerifikasi extends EditRecord
{
    protected static string $resource = VerifikasiResource::class;

    protected function afterSave(): void
    {
        $verifikasi = $this->record;

        $pengajuan = $verifikasi->pengajuan;

        /*
        |--------------------------------------------------------------------------
        | AUTO MASUK REALISASI DANA
        |--------------------------------------------------------------------------
        */

        if (
            $pengajuan->jenis_pengajuan === 'UANG_MUKA'
            &&
            $verifikasi->status === 'approve'
        ) {

            RealisasiDana::updateOrCreate(
                [
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                ],
                [
                    'tgl_realisasi' => now(),
                    'total_realisasi' => $pengajuan->estimasi_biaya,
                    'status' => 'PENDING',
                ]
            );

            $pengajuan->update([
                'status' => 'Direalisasikan'
            ]);
        }
    }
}