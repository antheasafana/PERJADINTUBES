<h2>Realisasi Dana</h2>

<p>
    Dana perjalanan dinas anda telah cair.
</p>

<p>
    <b>Tujuan:</b>
    {{ $realisasi->pengajuan->tujuan }}
</p>

<p>
    <b>Total Realisasi:</b>
    Rp {{ number_format($realisasi->total_realisasi, 0, ',', '.') }}
</p>

<p>
    <b>Status:</b>
    {{ $realisasi->status_realisasi }}
</p>