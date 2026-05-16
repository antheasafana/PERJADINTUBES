<?php

namespace App\Mail;

use App\Models\Pengajuan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PengajuanBerhasilMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pengajuan $pengajuan,
        public string $pdfContent
    ) {}

    public function build()
    {
        return $this->subject('Pengajuan Perjalanan Dinas Berhasil Diajukan')
            ->view('emails.pengajuan')
            ->attachData(
                $this->pdfContent,
                'pengajuan-' . $this->pengajuan->id_pengajuan . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
