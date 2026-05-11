<?php

namespace App\Http\Controllers;

use App\Models\Verifikasi;
use App\Models\RealisasiDana;

class VerifikasiController extends Controller
{
    public function approve($id)
    {
        // AMBIL DATA VERIFIKASI
        $verifikasi = Verifikasi::findOrFail($id);

        // UPDATE STATUS
        $verifikasi->status = 'approve';
        $verifikasi->tanggal_verifikasi = now();
        $verifikasi->save();

        // AMBIL DATA PENGAJUAN
        $pengajuan = $verifikasi->pengajuan;

        // KHUSUS UANG MUKA
        if ($pengajuan->jenis_pengajuan == 'UANG_MUKA') {

            // MASUKKAN KE REALISASI DANA
            RealisasiDana::create([
                'id_pengajuan'       => $pengajuan->id_pengajuan,
                'id_jenis_transaksi' => 1,
                'tgl_realisasi'      => now(),
                'total_realisasi'    => $pengajuan->estimasi_biaya,
            ]);

            // UPDATE STATUS PENGAJUAN
            $pengajuan->status = 'Disetujui';
            $pengajuan->save();
        }

        return redirect()->back()
            ->with('success', 'Pengajuan berhasil di-approve!');
    }
}