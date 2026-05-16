<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Pengajuan;
use App\Models\TransaksiPengeluaran;
use App\Models\Verifikasi;
use App\Models\Pembayaran;
use App\Models\RealisasiDana;

use App\Mail\VerifikasiApproveMail;
use App\Mail\VerifikasiRejectMail;

class VerifikasiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $verifikasis = Verifikasi::with([

                'pengajuan.pegawai',

                'transaksiPengeluaran.pengajuan.pegawai',

                'transaksiPengeluaran.kategoriBiaya',

                'transaksiPengeluaran.akun'
            ])
            ->where('status', 'pending')
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | VERIFIKASI PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengajuan = $verifikasis->whereNull(
            'id_transaksi_pengeluaran'
        );

        /*
        |--------------------------------------------------------------------------
        | VERIFIKASI PENGELUARAN
        |--------------------------------------------------------------------------
        */

        $pengeluaran = $verifikasis->whereNotNull(
            'id_transaksi_pengeluaran'
        );

        return view(
            'admin.verifikasi.index',
            compact(
                'pengajuan',
                'pengeluaran'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $verifikasi = Verifikasi::with([

                'pengajuan.pegawai',

                'transaksiPengeluaran.pengajuan.pegawai',

                'transaksiPengeluaran.kategoriBiaya',

                'transaksiPengeluaran.akun'
            ])
            ->findOrFail($id);

        return view(
            'admin.verifikasi.show',
            compact('verifikasi')
        );
    }

    public function exportPdf()
    {
        $verifikasis = Verifikasi::with([

                'pengajuan.pegawai',

                'transaksiPengeluaran.pengajuan.pegawai',

                'transaksiPengeluaran.kategoriBiaya',

                'transaksiPengeluaran.akun'
            ])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $pengajuan = $verifikasis->whereNull('id_transaksi_pengeluaran');
        $pengeluaran = $verifikasis->whereNotNull('id_transaksi_pengeluaran');

        $pdf = Pdf::loadView(
            'pdf.verifikasi',
            compact('pengajuan', 'pengeluaran')
        )
        ->setPaper('a4', 'landscape');

        return $pdf->download('verifikasi-transaksi.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve($id)
    {
        $verifikasi = Verifikasi::with([

                'pengajuan.pegawai',

                'transaksiPengeluaran.pengajuan.pegawai'
            ])
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        if ($verifikasi->status !== 'pending') {

            return back()->with(
                'error',
                'Verifikasi sudah diproses.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE VERIFIKASI
        |--------------------------------------------------------------------------
        */

        $verifikasi->update([

            'status' =>
                'approve',

            'tanggal_verifikasi' =>
                now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | JIKA VERIFIKASI PENGELUARAN
        |--------------------------------------------------------------------------
        */

        if ($verifikasi->transaksiPengeluaran) {

            $transaksi =
                $verifikasi->transaksiPengeluaran;

            /*
            |--------------------------------------------------------------------------
            | UPDATE TRANSAKSI
            |--------------------------------------------------------------------------
            */

            $transaksi->update([

                'status' =>
                    'pembayaran',

                'tanggal_verifikasi' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS PENGAJUAN
            |--------------------------------------------------------------------------
            */

            $transaksi->pengajuan->update([

                'status' =>
                    'Pembayaran',
            ]);

            Pembayaran::createPendingForPengajuan(
                $transaksi->pengajuan,
                $transaksi
            );

            /*
            |--------------------------------------------------------------------------
            | KIRIM EMAIL
            |--------------------------------------------------------------------------
            */

            if (
                $transaksi->pengajuan->pegawai &&
                $transaksi->pengajuan->pegawai->email
            ) {

                Mail::to(
                    $transaksi->pengajuan->pegawai->email
                )->send(
                    new VerifikasiApproveMail(
                        $transaksi->pengajuan
                    )
                );
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE PDF
            |--------------------------------------------------------------------------
            */

            $pdf = Pdf::loadView(
                'pdf.pengajuan',
                [
                    'pengajuan' =>
                        $transaksi->pengajuan
                ]
            );

            $pdf->save(
                public_path(
                    'pdf/pengajuan_' .
                    $transaksi->pengajuan->id_pengajuan .
                    '.pdf'
                )
            );

            return redirect()
                ->route('verifikasi.index')
                ->with(
                    'success',
                    'Pengeluaran berhasil diverifikasi. Silakan proses pembayaran di menu Pembayaran.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFIKASI PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengajuan =
            $verifikasi->pengajuan;

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        if ($pengajuan->jenis_pengajuan === 'UANG_MUKA') {
            $pengajuan->update([
                'status' => 'Pembayaran',
            ]);

            Pembayaran::createPendingForPengajuan($pengajuan);
        }

        /*
        |--------------------------------------------------------------------------
        | KIRIM EMAIL
        |--------------------------------------------------------------------------
        */

        if (
            $pengajuan->pegawai &&
            $pengajuan->pegawai->email
        ) {

            Mail::to(
                $pengajuan->pegawai->email
            )->send(
                new VerifikasiApproveMail(
                    $pengajuan
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'pdf.pengajuan',
            compact('pengajuan')
        );

        $pdf->save(
            public_path(
                'pdf/pengajuan_' .
                $pengajuan->id_pengajuan .
                '.pdf'
            )
        );

        return redirect()
            ->route('verifikasi.index')
            ->with(
                'success',
                'Pengajuan berhasil disetujui.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        $id
    ) {

        $request->validate([

            'catatan' =>
                'required|string|max:500',
        ]);

        $verifikasi = Verifikasi::with([

                'pengajuan.pegawai',

                'transaksiPengeluaran.pengajuan.pegawai'
            ])
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        if ($verifikasi->status !== 'pending') {

            return back()->with(
                'error',
                'Verifikasi sudah diproses.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE VERIFIKASI
        |--------------------------------------------------------------------------
        */

        $verifikasi->update([

            'status' =>
                'reject',

            'alasan_reject' =>
                $request->catatan,

            'tanggal_verifikasi' =>
                now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | REJECT PENGELUARAN
        |--------------------------------------------------------------------------
        */

        if ($verifikasi->transaksiPengeluaran) {

            $transaksi =
                $verifikasi->transaksiPengeluaran;

            $transaksi->update([

                'status' =>
                    'ditolak',

                'catatan_verifikasi' =>
                    $request->catatan,

                'tanggal_verifikasi' =>
                    now(),
            ]);

            $transaksi->pengajuan->update([

                'status' =>
                    'Pengeluaran Ditolak',
            ]);

            /*
            |--------------------------------------------------------------------------
            | EMAIL REJECT
            |--------------------------------------------------------------------------
            */

            if (
                $transaksi->pengajuan->pegawai &&
                $transaksi->pengajuan->pegawai->email
            ) {

                Mail::to(
                    $transaksi->pengajuan->pegawai->email
                )->send(
                    new VerifikasiRejectMail(
                        $transaksi->pengajuan
                    )
                );
            }

            return redirect()
                ->route('verifikasi.index')
                ->with(
                    'success',
                    'Pengeluaran berhasil ditolak.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | REJECT PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $verifikasi->pengajuan->update([

            'status' =>
                'Pengajuan Ditolak',
        ]);

        /*
        |--------------------------------------------------------------------------
        | EMAIL REJECT
        |--------------------------------------------------------------------------
        */

        if (
            $verifikasi->pengajuan->pegawai &&
            $verifikasi->pengajuan->pegawai->email
        ) {

            Mail::to(
                $verifikasi->pengajuan->pegawai->email
            )->send(
                new VerifikasiRejectMail(
                    $verifikasi->pengajuan
                )
            );
        }

        return redirect()
            ->route('verifikasi.index')
            ->with(
                'success',
                'Pengajuan berhasil ditolak.'
            );
    }
}