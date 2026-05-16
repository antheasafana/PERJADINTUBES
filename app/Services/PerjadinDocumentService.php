<?php

namespace App\Services;

use App\Mail\PembayaranInvoiceMail;
use App\Mail\PengajuanBerhasilMail;
use App\Mail\VerifikasiStatusMail;
use App\Models\RealisasiDana;
use App\Models\Pembayaran;
use App\Models\Pengajuan;
use App\Models\Verifikasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PerjadinDocumentService
{
    public static function pegawaiEmail(?Pengajuan $pengajuan): ?string
    {
        $email = $pengajuan?->pegawai?->email;

        if ($email) {
            return $email;
        }

        return config('mail.test_recipient');
    }

    public static function pengajuanRingkasPdf(Pengajuan $pengajuan)
    {
        $pengajuan->load(['pegawai']);

        return Pdf::loadView('pdf.pengajuan', compact('pengajuan'));
    }

    public static function pengajuanPdf(Pengajuan $pengajuan)
    {
        $pengajuan->load([
            'pegawai',
            'realisasiDana',
            'transaksiPengeluaran.kategoriBiaya',
            'transaksiPengeluaran.akun',
        ]);

        return Pdf::loadView('pdf.pengajuan-lengkap', compact('pengajuan'));
    }

    public static function realisasiDanaPdf(RealisasiDana $realisasi)
    {
        $realisasi->load(['pengajuan.pegawai']);

        return Pdf::loadView('pdf.realisasidana', compact('realisasi'));
    }

    public static function verifikasiPdf(Verifikasi $verifikasi)
    {
        $verifikasi->load([
            'pengajuan.pegawai',
            'pengajuan.realisasiDana',
            'pengajuan.transaksiPengeluaran.kategoriBiaya',
            'pengajuan.transaksiPengeluaran.akun',
            'transaksiPengeluaran.kategoriBiaya',
            'transaksiPengeluaran.akun',
        ]);

        return Pdf::loadView('pdf.verifikasi-detail', compact('verifikasi'));
    }

    public static function pembayaranInvoicePdf(Pembayaran $pembayaran)
    {
        $pembayaran->load([
            'pengajuan.pegawai',
            'pengajuan.realisasiDana',
            'pengajuan.transaksiPengeluaran',
            'transaksiPengeluaran',
        ]);

        return Pdf::loadView('pdf.invoice-pembayaran', compact('pembayaran'));
    }

    public static function sendPengajuanBerhasilEmail(Pengajuan $pengajuan): void
    {
        $pengajuan->loadMissing('pegawai');

        $email = static::pegawaiEmail($pengajuan);

        if (! $email) {
            return;
        }

        try {
            $pdf = static::pengajuanRingkasPdf($pengajuan);

            Mail::to($email)->send(
                new PengajuanBerhasilMail($pengajuan, $pdf->output())
            );
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email pengajuan: ' . $e->getMessage());
        }
    }

    public static function sendVerifikasiEmail(Verifikasi $verifikasi, string $hasil): void
    {
        $email = static::pegawaiEmail($verifikasi->pengajuan);

        if (! $email) {
            return;
        }

        try {
            $pdf = static::verifikasiPdf($verifikasi);

            Mail::to($email)->send(
                new VerifikasiStatusMail($verifikasi, $hasil, $pdf->output())
            );
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email verifikasi: ' . $e->getMessage());
        }
    }

    public static function sendPembayaranInvoiceEmail(Pembayaran $pembayaran): void
    {
        $email = static::pegawaiEmail($pembayaran->pengajuan);

        if (! $email) {
            return;
        }

        try {
            $pdf = static::pembayaranInvoicePdf($pembayaran);

            Mail::to($email)->send(
                new PembayaranInvoiceMail($pembayaran, $pdf->output())
            );
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email pembayaran: ' . $e->getMessage());
        }
    }
}
