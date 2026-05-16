<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Verifikasi Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
        }
        h1, h2 {
            margin: 0 0 16px 0;
            padding: 0;
        }
        h2 {
            margin-top: 30px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .empty-row td {
            text-align: center;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Laporan Verifikasi Transaksi</h1>
    <p>Diunduh pada: {{ now()->format('d M Y H:i') }}</p>

    <h2>Verifikasi Pengajuan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Verifikasi</th>
                <th>Tujuan</th>
                <th>Jenis</th>
                <th>Estimasi</th>
                <th>Waktu Request</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>#{{ $item->id }}</td>
                    <td>{{ $item->pengajuan->tujuan ?? '-' }}</td>
                    <td>{{ str_replace('_', ' ', $item->pengajuan->jenis_pengajuan ?? '-') }}</td>
                    <td>Rp {{ number_format($item->pengajuan->estimasi_biaya ?? 0, 0, ',', '.') }}</td>
                    <td>{{ optional($item->created_at)->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="6">Tidak ada verifikasi pengajuan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Verifikasi Pengeluaran</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Verifikasi</th>
                <th>ID Transaksi</th>
                <th>Tujuan</th>
                <th>Uraian</th>
                <th>Nominal</th>
                <th>Waktu Request</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengeluaran as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>#{{ $item->id }}</td>
                    <td>#{{ $item->id_transaksi_pengeluaran }}</td>
                    <td>{{ $item->transaksiPengeluaran->pengajuan->tujuan ?? '-' }}</td>
                    <td>{{ $item->transaksiPengeluaran->uraian ?? '-' }}</td>
                    <td>Rp {{ number_format($item->transaksiPengeluaran->nominal ?? 0, 0, ',', '.') }}</td>
                    <td>{{ optional($item->created_at)->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="7">Tidak ada verifikasi pengeluaran.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
