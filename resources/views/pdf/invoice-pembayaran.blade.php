<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Pembayaran #{{ $pembayaran->id_pembayaran }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { border-bottom: 2px solid #2d6a4f; padding-bottom: 12px; margin-bottom: 20px; }
        h1 { color: #2d6a4f; margin: 0; font-size: 22px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #d8f3dc; text-align: left; }
        .total { font-size: 16px; font-weight: bold; color: #1b4332; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE PEMBAYARAN</h1>
        <p>E-Perjadin · No. INV-{{ str_pad($pembayaran->id_pembayaran, 6, '0', STR_PAD_LEFT) }}</p>
        <p>Tanggal: {{ optional($pembayaran->tanggal_pembayaran)->format('d M Y H:i') ?? now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <tr><th width="35%">Penerima</th><td>{{ $pembayaran->pengajuan->pegawai->nama ?? '-' }}</td></tr>
        <tr><th>No. Rekening</th><td>{{ $pembayaran->no_rekening_pegawai ?? '-' }}</td></tr>
        <tr><th>ID Pengajuan</th><td>#{{ $pembayaran->id_pengajuan }}</td></tr>
        <tr><th>Tujuan Perjalanan</th><td>{{ $pembayaran->pengajuan->tujuan ?? '-' }}</td></tr>
        <tr><th>Jenis Pembayaran</th><td>{{ str_replace('_', ' ', $pembayaran->jenis_pembayaran) }}</td></tr>
        <tr><th>Arah Transaksi</th><td>{{ str_replace('_', ' ', $pembayaran->arah_transaksi) }}</td></tr>
        @if($pembayaran->transaksiPengeluaran)
        <tr><th>Uraian Pengeluaran</th><td>{{ $pembayaran->transaksiPengeluaran->uraian }}</td></tr>
        @endif
        <tr><th>Status</th><td>{{ strtoupper($pembayaran->status) }}</td></tr>
        <tr>
            <th class="total">Nominal Dibayar</th>
            <td class="total">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
        </tr>
    </table>

    <p style="margin-top:24px;font-size:10px;color:#666;">
        Dokumen ini merupakan bukti pembayaran resmi dari sistem E-Perjadin.
    </p>
</body>
</html>
