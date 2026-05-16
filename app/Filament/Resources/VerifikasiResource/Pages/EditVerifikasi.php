<?php

namespace App\Filament\Resources\VerifikasiResource\Pages;

use App\Filament\Resources\VerifikasiResource;
use App\Models\Pembayaran;
use Filament\Resources\Pages\EditRecord;

class EditVerifikasi extends EditRecord
{
    protected static string $resource = VerifikasiResource::class;

    protected function afterSave(): void
    {
        $verifikasi = $this->record->fresh(['pengajuan', 'transaksiPengeluaran.pengajuan']);

        if ($verifikasi->status !== 'approve') {
            return;
        }

        if ($verifikasi->transaksiPengeluaran) {
            $transaksi = $verifikasi->transaksiPengeluaran;

            $transaksi->update([
                'status' => 'pembayaran',
                'tanggal_verifikasi' => now(),
            ]);

            $transaksi->pengajuan?->update([
                'status' => 'Pembayaran',
            ]);

            Pembayaran::createPendingForPengajuan(
                $transaksi->pengajuan,
                $transaksi
            );

            return;
        }

        if (
            $verifikasi->pengajuan
            && $verifikasi->pengajuan->jenis_pengajuan === 'UANG_MUKA'
        ) {
            Pembayaran::createPendingForPengajuan($verifikasi->pengajuan);

            $verifikasi->pengajuan->update([
                'status' => 'Pembayaran',
            ]);
        }
    }
}
