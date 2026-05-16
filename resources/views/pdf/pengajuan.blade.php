<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengajuan #{{ $pengajuan->id_pengajuan }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 18px; color: #2d6a4f; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #444; padding: 8px; text-align: left; }
        th { background: #d8f3dc; width: 32%; }
        .footer { margin-top: 24px; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <h1>Bukti Pengajuan Perjalanan Dinas</h1>
    <p>No. Pengajuan: #{{ $pengajuan->id_pengajuan }} · Dicetak: {{ now()->format('d M Y H:i') }}</p>

    <table>
        <tr><th>ID Pengajuan</th><td>#{{ $pengajuan->id_pengajuan }}</td></tr>
        <tr><th>Pegawai</th><td>{{ $pengajuan->pegawai->nama ?? '-' }}</td></tr>
        <tr><th>Jenis Pengajuan</th><td>{{ str_replace('_', ' ', $pengajuan->jenis_pengajuan) }}</td></tr>
        <tr><th>Tujuan</th><td>{{ $pengajuan->tujuan }}</td></tr>
        <tr><th>Tanggal Berangkat</th><td>{{ \Carbon\Carbon::parse($pengajuan->tgl_berangkat)->format('d M Y') }}</td></tr>
        <tr><th>Tanggal Kembali</th><td>{{ \Carbon\Carbon::parse($pengajuan->tgl_kembali)->format('d M Y') }}</td></tr>
        <tr><th>Estimasi Biaya</th><td>Rp {{ number_format($pengajuan->estimasi_biaya ?? 0, 0, ',', '.') }}</td></tr>
        <tr><th>Status</th><td>{{ $pengajuan->status }}</td></tr>
        @if($pengajuan->dokumen)
        <tr><th>Dokumen</th><td>{{ $pengajuan->dokumen }}</td></tr>
        @endif
    </table>

    <p class="footer">Dokumen ini dicetak dari sistem E-Perjadin sebagai bukti pengajuan.</p>
</body>
</html>
