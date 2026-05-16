<?php

namespace App\Mail;

use App\Models\Verifikasi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifikasiStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Verifikasi $verifikasi,
        public string $hasil,
        public string $pdfContent
    ) {}

    public function build()
    {
        $label = $this->hasil === 'approve' ? 'Disetujui' : 'Ditolak';
        $jenis = $this->verifikasi->transaksiPengeluaran
            ? 'Verifikasi Pengeluaran'
            : 'Verifikasi Pengajuan';

        return $this->subject("Notifikasi {$jenis} — {$label}")
            ->view('emails.verifikasi-status', [
                'verifikasi' => $this->verifikasi,
                'hasil' => $this->hasil,
                'label' => $label,
                'jenis' => $jenis,
            ])
            ->attachData(
                $this->pdfContent,
                'laporan-verifikasi-' . $this->verifikasi->id . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
