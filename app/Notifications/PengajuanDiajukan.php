<?php

namespace App\Notifications;

use App\Models\Pengajuan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengajuanDiajukan extends Notification
{
    use Queueable;

    protected Pengajuan $pengajuan;
    protected string $role; // 'pegawai' atau 'admin'

    public function __construct(Pengajuan $pengajuan, string $role = 'pegawai')
    {
        $this->pengajuan = $pengajuan;
        $this->role      = $role;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jenis = $this->pengajuan->jenis_pengajuan === 'UANG_MUKA'
            ? 'Uang Muka'
            : 'Reimbursement';

        $tglBerangkat = $this->pengajuan->tgl_berangkat
            ? date('d/m/Y', strtotime($this->pengajuan->tgl_berangkat))
            : '-';

        $tglKembali = $this->pengajuan->tgl_kembali
            ? date('d/m/Y', strtotime($this->pengajuan->tgl_kembali))
            : '-';

        $estimasi = $this->pengajuan->estimasi_biaya
            ? 'Rp ' . number_format($this->pengajuan->estimasi_biaya, 0, ',', '.')
            : '-';

        if ($this->role === 'admin') {
            return (new MailMessage)
                ->subject('📋 Pengajuan Perjalanan Dinas Baru - ' . $this->pengajuan->tujuan)
                ->greeting('Halo Admin,')
                ->line('Ada pengajuan perjalanan dinas baru yang perlu diproses.')
                ->line('**Detail Pengajuan:**')
                ->line('• **No. Pengajuan:** #' . $this->pengajuan->id_pengajuan)
                ->line('• **Pegawai:** ' . ($notifiable->name ?? 'Unknown'))
                ->line('• **Jenis:** ' . $jenis)
                ->line('• **Tujuan:** ' . $this->pengajuan->tujuan)
                ->line('• **Tanggal Berangkat:** ' . $tglBerangkat)
                ->line('• **Tanggal Kembali:** ' . $tglKembali)
                ->line('• **Estimasi Biaya:** ' . $estimasi)
                ->line('• **Status:** ' . $this->pengajuan->status)
                ->action('Lihat di Panel Admin', url('/admin/pengajuans/' . $this->pengajuan->id_pengajuan))
                ->line('Silakan login ke panel admin untuk menyetujui atau menolak pengajuan ini.')
                ->salutation('Sistem PERJADINTUBES');
        }

        // Untuk pegawai
        return (new MailMessage)
            ->subject('✅ Pengajuan Perjalanan Dinas Berhasil Disubmit')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Pengajuan perjalanan dinas Anda telah berhasil disubmit dan sedang menunggu persetujuan admin.')
            ->line('**Detail Pengajuan Anda:**')
            ->line('• **No. Pengajuan:** #' . $this->pengajuan->id_pengajuan)
            ->line('• **Jenis:** ' . $jenis)
            ->line('• **Tujuan:** ' . $this->pengajuan->tujuan)
            ->line('• **Tanggal Berangkat:** ' . $tglBerangkat)
            ->line('• **Tanggal Kembali:** ' . $tglKembali)
            ->line('• **Estimasi Biaya:** ' . $estimasi)
            ->line('• **Status:** ' . $this->pengajuan->status)
            ->action('Lihat Status Pengajuan', url('/pengajuan'))
            ->line('Anda akan mendapatkan notifikasi lagi ketika admin telah memproses pengajuan Anda.')
            ->salutation('Sistem PERJADINTUBES');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'id_pengajuan'    => $this->pengajuan->id_pengajuan,
            'jenis_pengajuan' => $this->pengajuan->jenis_pengajuan,
            'tujuan'          => $this->pengajuan->tujuan,
            'status'          => $this->pengajuan->status,
        ];
    }
}
