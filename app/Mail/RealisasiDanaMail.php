<?php

namespace App\Mail;

use App\Models\RealisasiDana;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RealisasiDanaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $realisasi;

    public function __construct(RealisasiDana $realisasi)
    {
        $this->realisasi = $realisasi;
    }

    public function build()
    {
        return $this
            ->subject('Dana Perjalanan Dinas Sudah Cair')
            ->view('emails.realisasi-dana');
    }
}