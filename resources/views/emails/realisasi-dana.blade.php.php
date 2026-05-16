<h2>Realisasi Dana Perjalanan Dinas</h2>

<p>
    Halo,
</p>

<p>
    Pengajuan dana perjalanan dinas Anda telah berhasil direalisasikan.
</p>

<p>
    Mohon menunggu proses pencairan dana dan pembayaran sesuai alur yang berlaku pada sistem perjalanan dinas.
</p>

<br>

<p>
    <b>Tujuan Perjalanan:</b><br>
    {{ $realisasi->pengajuan->tujuan }}
</p>

<p>
    <b>Total Realisasi Dana:</b><br>
    Rp {{ number_format($realisasi->total_realisasi, 0, ',', '.') }}
</p>

<p>
    <b>Status Realisasi:</b><br>
    {{ $realisasi->status_realisasi }}
</p>

<br>

<p>
    Terima kasih.
</p>