<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengajuan #{{ $pengajuan->id_pengajuan }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { font-size: 18px; }
        h2 { font-size: 14px; margin-top: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #444; padding: 6px 8px; }
        th { background: #d8f3dc; }
        .meta td { border: none; padding: 3px 0; }
        .meta td:first-child { width: 150px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Pengajuan & Pengeluaran</h1>
    <p>Dicetak: {{ now()->format('d M Y H:i') }}</p>

    <h2>Data Pengajuan</h2>
    <table class="meta">
        <tr><td>ID Pengajuan</td><td>#{{ $pengajuan->id_pengajuan }}</td></tr>
        <tr><td>Pegawai</td><td>{{ $pengajuan->pegawai->nama ?? '-' }}</td></tr>
        <tr><td>Jenis</td><td>{{ $pengajuan->jenis_pengajuan }}</td></tr>
        <tr><td>Tujuan</td><td>{{ $pengajuan->tujuan }}</td></tr>
        <tr><td>Berangkat</td><td>{{ $pengajuan->tgl_berangkat }}</td></tr>
        <tr><td>Kembali</td><td>{{ $pengajuan->tgl_kembali }}</td></tr>
        <tr><td>Estimasi</td><td>Rp {{ number_format($pengajuan->estimasi_biaya ?? 0, 0, ',', '.') }}</td></tr>
        <tr><td>Status</td><td>{{ $pengajuan->status }}</td></tr>
        @if($pengajuan->realisasiDana)
        <tr><td>Realisasi Dana</td><td>Rp {{ number_format($pengajuan->realisasiDana->total_realisasi, 0, ',', '.') }}</td></tr>
        @endif
    </table>

    <h2>Detail Pengeluaran</h2>
    @php $total = $pengajuan->transaksiPengeluaran->sum('nominal'); @endphp
    <table>
        <thead>
            <tr>
                <th>No</th><th>Tanggal</th><th>Uraian</th><th>Kategori</th><th>Akun</th><th>Status</th><th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuan->transaksiPengeluaran as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional($item->tanggal_pengeluaran)->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $item->uraian }}</td>
                <td>{{ $item->kategoriBiaya->jenis_biaya ?? '-' }}</td>
                <td>{{ $item->akun->nama_akun ?? '-' }}</td>
                <td>{{ $item->status }}</td>
                <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center">Belum ada pengeluaran.</td></tr>
            @endforelse
            @if($pengajuan->transaksiPengeluaran->count())
            <tr>
                <th colspan="6">Total</th>
                <th>Rp {{ number_format($total, 0, ',', '.') }}</th>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
