<h2>Invoice Pembayaran — {{ $jenisLabel }}</h2>

<p>Halo {{ $pembayaran->pengajuan->pegawai->nama ?? 'Pegawai' }},</p>

<p>
    @if($pembayaran->jenis_pembayaran === 'uang_muka')
        Uang muka perjalanan dinas Anda telah dicairkan.
    @else
        Pembayaran perjalanan dinas Anda telah diproses.
    @endif
</p>

<ul>
    <li>ID Pembayaran: #{{ $pembayaran->id_pembayaran }}</li>
    <li>Pengajuan: #{{ $pembayaran->id_pengajuan }}</li>
    <li>Tujuan: {{ $pembayaran->pengajuan->tujuan ?? '-' }}</li>
    <li>Nominal dibayar: Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</li>
    <li>No. rekening: {{ $pembayaran->no_rekening_pegawai ?? '-' }}</li>
    <li>Tanggal pembayaran: {{ optional($pembayaran->tanggal_pembayaran)->format('d M Y H:i') ?? '-' }}</li>
</ul>

<p>Invoice PDF terlampir pada email ini.</p>

<p>Terima kasih.</p>
