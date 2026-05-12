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

            /*
            |--------------------------------------------------------------------------
            | TENTUKAN JENIS PEMBAYARAN
            |--------------------------------------------------------------------------
            */

            $jenisPengajuan =
                $transaksi->pengajuan->jenis_pengajuan;

            $jenisPembayaran = match ($jenisPengajuan) {

                'UANG_MUKA' =>
                    'uang_muka',

                'PENGEMBALIAN' =>
                    'pengembalian_dana',

                default =>
                    'reimbursement',
            };

            /*
            |--------------------------------------------------------------------------
            | ARAH TRANSAKSI
            |--------------------------------------------------------------------------
            */

            $arahTransaksi =
                $jenisPengajuan === 'PENGEMBALIAN'
                ? 'pegawai_ke_admin'
                : 'admin_ke_pegawai';

            /*
            |--------------------------------------------------------------------------
            | SIMPAN PEMBAYARAN
            |--------------------------------------------------------------------------
            */

            Pembayaran::create([

                'id_pengajuan' =>
                    $transaksi->id_pengajuan,

                'id_transaksi_pengeluaran' =>
                    $transaksi->id_transaksi_pengeluaran,

                'jenis_pembayaran' =>
                    $jenisPembayaran,

                'arah_transaksi' =>
                    $arahTransaksi,

                'nominal' =>
                    $transaksi->nominal,

                'status' =>
                    'dibayar',

                'tanggal_pembayaran' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE TRANSAKSI FINAL
            |--------------------------------------------------------------------------
            */

            $transaksi->update([

                'status' =>
                    'transaksi_tercatat',

                'tanggal_pembayaran' =>
                    now(),

                'tanggal_tercatat' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS PENGAJUAN
            |--------------------------------------------------------------------------
            */

            $transaksi->pengajuan->update([

                'status' =>
                    'Transaksi Tercatat'
            ]);

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
                    'Pengeluaran berhasil diverifikasi dan pembayaran selesai.'
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

        $pengajuan->update([

            'status' =>
                'Pembayaran',
        ]);

        /*
        |--------------------------------------------------------------------------
        | KHUSUS UANG MUKA
        |--------------------------------------------------------------------------
        */

        if (
            $pengajuan->jenis_pengajuan ==
            'UANG_MUKA'
        ) {

            Pembayaran::create([

                'id_pengajuan' =>
                    $pengajuan->id_pengajuan,

                'jenis_pembayaran' =>
                    'uang_muka',

                'arah_transaksi' =>
                    'admin_ke_pegawai',

                'nominal' =>
                    $pengajuan->estimasi_biaya,

                'status' =>
                    'dibayar',

                'tanggal_pembayaran' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS FINAL
            |--------------------------------------------------------------------------
            */

            $pengajuan->update([

                'status' =>
                    'Transaksi Tercatat'
            ]);
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