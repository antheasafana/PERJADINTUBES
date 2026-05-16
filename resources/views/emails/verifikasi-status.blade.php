<h2>Notifikasi {{ $jenis }}</h2>

<p>Halo,</p>

<p>
    Permintaan verifikasi Anda telah diproses dengan status:
    <strong>{{ $label }}</strong>.
</p>

<ul>
    <li>ID Verifikasi: #{{ $verifikasi->id }}</li>
    <li>Pengajuan: #{{ $verifikasi->id_pengajuan }}</li>
    <li>Tujuan: {{ $verifikasi->pengajuan->tujuan ?? '-' }}</li>
    @if($verifikasi->transaksiPengeluaran)
        <li>Uraian pengeluaran: {{ $verifikasi->transaksiPengeluaran->uraian }}</li>
        <li>Nominal: Rp {{ number_format($verifikasi->transaksiPengeluaran->nominal, 0, ',', '.') }}</li>
    @endif
    @if($hasil === 'reject' && $verifikasi->alasan_reject)
        <li>Alasan penolakan: {{ $verifikasi->alasan_reject }}</li>
    @endif
    @if($hasil === 'approve' && $verifikasi->catatan)
        <li>Catatan admin: {{ $verifikasi->catatan }}</li>
    @endif
</ul>

<p>Laporan PDF terlampir pada email ini.</p>

<p>Terima kasih.</p>
