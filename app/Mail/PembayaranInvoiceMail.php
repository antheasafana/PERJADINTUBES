<?php

namespace App\Mail;

use App\Models\Pembayaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PembayaranInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pembayaran $pembayaran,
        public string $pdfContent
    ) {}

    public function build()
    {
        $jenisLabel = match ($this->pembayaran->jenis_pembayaran) {
            'uang_muka' => 'Uang Muka',
            'reimbursement' => 'Reimbursement',
            'pengembalian_dana' => 'Pengembalian Dana',
            default => 'Pembayaran',
        };

        return $this->subject("Invoice Pembayaran {$jenisLabel} — Dana Telah Dicairkan")
            ->view('emails.pembayaran-invoice', [
                'pembayaran' => $this->pembayaran,
                'jenisLabel' => $jenisLabel,
            ])
            ->attachData(
                $this->pdfContent,
                'invoice-pembayaran-' . $this->pembayaran->id_pembayaran . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
