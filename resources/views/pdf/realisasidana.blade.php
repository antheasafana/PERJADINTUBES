<!DOCTYPE html>
<html>
<head>
    <title>Invoice Realisasi Dana</title>

    <style>
        body{
            font-family: Arial, sans-serif;
        }

        h2{
            text-align: center;
            margin-bottom: 20px;
        }

        table{
            border-collapse: collapse;
        }

        th{
            background-color: #f2f2f2;
            text-align: left;
            width: 35%;
        }

        th, td{
            border: 1px solid black;
            padding: 10px;
        }
    </style>
</head>

<body>

    <h2>Invoice Realisasi Dana</h2>

    <table width="100%">
        <tr>
            <th>Tujuan</th>
            <td>
                {{ $realisasi->pengajuan->tujuan }}
            </td>
        </tr>

        <tr>
            <th>Jenis Pengajuan</th>
            <td>
                {{ $realisasi->pengajuan->jenis_pengajuan }}
            </td>
        </tr>

        <tr>
            <th>Total Realisasi</th>
            <td>
                Rp {{ number_format($realisasi->total_realisasi, 0, ',', '.') }}
            </td>
        </tr>

        <tr>
            <th>Tanggal Realisasi</th>
            <td>
                {{ $realisasi->tgl_realisasi }}
            </td>
        </tr>

        <tr>
            <th>Status</th>
            <td>
                {{ $realisasi->status }}
            </td>
        </tr>

        <tr>
            <th>Catatan</th>
            <td>
                {{ $realisasi->catatan ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>