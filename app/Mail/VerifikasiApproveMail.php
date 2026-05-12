<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifikasiApproveMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;

    public function __construct($pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function build()
    {
        return $this->subject('Pengajuan Disetujui')
            ->view('emails.approve');
    }
}