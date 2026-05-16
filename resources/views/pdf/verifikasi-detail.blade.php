<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Verifikasi #{{ $verifikasi->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #444; padding: 6px 8px; text-align: left; }
        th { background: #e8f0fe; }
        .meta td { border: none; padding: 3px 0; }
        .meta td:first-child { width: 140px; font-weight: bold; }
        .highlight { background: #fff8e1; }
    </style>
</head>
<body>
    <h1>Laporan Verifikasi #{{ $verifikasi->id }}</h1>
    <p>Dicetak: {{ now()->format('d M Y H:i') }}</p>

    <h2>Ringkasan Verifikasi</h2>
    <table class="meta">
        <tr><td>Jenis</td><td>{{ $verifikasi->verification_type }}</td></tr>
        <tr><td>Status</td><td>{{ strtoupper($verifikasi->status) }}</td></tr>
        <tr><td>ID Pengajuan</td><td>#{{ $verifikasi->id_pengajuan }}</td></tr>
        <tr><td>Tujuan</td><td>{{ $verifikasi->pengajuan->tujuan ?? '-' }}</td></tr>
        <tr><td>Jenis Pengajuan</td><td>{{ $verifikasi->pengajuan->jenis_pengajuan ?? '-' }}</td></tr>
        <tr><td>Pegawai</td><td>{{ $verifikasi->pengajuan->pegawai->nama ?? '-' }}</td></tr>
        @if($verifikasi->catatan)<tr><td>Catatan</td><td>{{ $verifikasi->catatan }}</td></tr>@endif
        @if($verifikasi->alasan_reject)<tr><td>Alasan Tolak</td><td>{{ $verifikasi->alasan_reject }}</td></tr>@endif
    </table>

    @if($verifikasi->transaksiPengeluaran)
        <h2>Pengeluaran yang Diverifikasi</h2>
        <table>
            <tr>
                <th>ID</th><th>Tanggal</th><th>Uraian</th><th>Kategori</th><th>Nominal</th>
            </tr>
            <tr class="highlight">
                <td>#{{ $verifikasi->transaksiPengeluaran->id_transaksi_pengeluaran }}</td>
                <td>{{ optional($verifikasi->transaksiPengeluaran->tanggal_pengeluaran)->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $verifikasi->transaksiPengeluaran->uraian }}</td>
                <td>{{ $verifikasi->transaksiPengeluaran->kategoriBiaya->jenis_biaya ?? '-' }}</td>
                <td>Rp {{ number_format($verifikasi->transaksiPengeluaran->nominal, 0, ',', '.') }}</td>
            </tr>
        </table>
    @endif

    @php
        $semuaPengeluaran = $verifikasi->pengajuan->transaksiPengeluaran ?? collect();
        $totalPengeluaran = $semuaPengeluaran->sum('nominal');
        $realisasi = $verifikasi->pengajuan->realisasiDana->total_realisasi ?? 0;
    @endphp

    @if($semuaPengeluaran->count())
        <h2>Semua Pengeluaran pada Pengajuan #{{ $verifikasi->id_pengajuan }}</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th><th>Tanggal</th><th>Uraian</th><th>Kategori</th><th>Status</th><th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($semuaPengeluaran as $row)
                <tr @if($verifikasi->id_transaksi_pengeluaran == $row->id_transaksi_pengeluaran) class="highlight" @endif>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($row->tanggal_pengeluaran)->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $row->uraian }}</td>
                    <td>{{ $row->kategoriBiaya->jenis_biaya ?? '-' }}</td>
                    <td>{{ $row->status }}</td>
                    <td>Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <th colspan="5">Total Pengeluaran</th>
                    <th>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</th>
                </tr>
                @if($realisasi > 0)
                <tr>
                    <th colspan="5">Total Realisasi Dana</th>
                    <th>Rp {{ number_format($realisasi, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="5">Sisa Dana</th>
                    <th>Rp {{ number_format(max(0, $realisasi - $totalPengeluaran), 0, ',', '.') }}</th>
                </tr>
                @endif
            </tbody>
        </table>
    @endif
</body>
</html>
