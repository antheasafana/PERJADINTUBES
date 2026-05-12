<!DOCTYPE html>
<html>
<head>
    <title>PDF Pengajuan</title>

    <style>
        body{
            font-family: sans-serif;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        td,th{
            border:1px solid #000;
            padding:8px;
        }
    </style>
</head>
<body>

<h2>Detail Pengajuan</h2>

<table>
    <tr>
        <th>ID Pengajuan</th>
        <td>{{ $pengajuan->id_pengajuan }}</td>
    </tr>

    <tr>
        <th>Jenis</th>
        <td>{{ $pengajuan->jenis_pengajuan }}</td>
    </tr>

    <tr>
        <th>Tujuan</th>
        <td>{{ $pengajuan->tujuan }}</td>
    </tr>

    <tr>
        <th>Status</th>
        <td>{{ $pengajuan->status }}</td>
    </tr>

    <tr>
        <th>Estimasi</th>
        <td>
            Rp {{ number_format($pengajuan->estimasi_biaya,0,',','.') }}
        </td>
    </tr>
</table>

</body>
</html>