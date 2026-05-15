<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengajuan Perjalanan Dinas</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: auto; }
        h2 { color: #2d7a4f; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #2d7a4f; color: white; }
        .status { background-color: #2d7a4f; color: white; padding: 5px 12px; border-radius: 20px; display: inline-block; }
        .footer { margin-top: 20px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🌿 E-Perjadin</h2>
        <p>Pengajuan perjalanan dinas Anda telah berhasil diajukan!</p>

        <table>
            <tr>
                <th colspan="2">Detail Pengajuan</th>
            </tr>
            <tr>
                <td><strong>Jenis Pengajuan</strong></td>
                <td>{{ $pengajuan->jenis_pengajuan }}</td>
            </tr>
            <tr>
                <td><strong>Tujuan</strong></td>
                <td>{{ $pengajuan->tujuan }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Berangkat</strong></td>
                <td>{{ \Carbon\Carbon::parse($pengajuan->tgl_berangkat)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Kembali</strong></td>
                <td>{{ \Carbon\Carbon::parse($pengajuan->tgl_kembali)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td><strong>Estimasi Biaya</strong></td>
                <td>Rp {{ number_format($pengajuan->estimasi_biaya, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td><span class="status">{{ $pengajuan->status }}</span></td>
            </tr>
        </table>

        <p class="footer">Email ini dikirim otomatis oleh sistem E-Perjadin. Mohon tidak membalas email ini.</p>
    </div>
</body>
</html>