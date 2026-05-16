<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public $pdf;

    public function __construct($data, $pdf)
    {
        $this->data = $data;

        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->subject('Invoice Realisasi Dana')
                    ->view('emails.invoice')
                    ->attachData(
                        $this->pdf,
                        'invoice-realisasi.pdf',
                        [
                            'mime' => 'application/pdf',
                        ]
                    );
    }
}