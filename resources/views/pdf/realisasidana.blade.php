<!DOCTYPE html>
<html>
<head>

    <title>
        Invoice Realisasi Dana
    </title>

</head>

<body>

    <h2>
        Invoice Realisasi Dana
    </h2>

    <table border="1" width="100%" cellpadding="10">

        <tr>
            <th>Tujuan</th>

            <td>
                {{ $realisasi->tujuan }}
            </td>
        </tr>

        <tr>
            <th>Jenis Pengajuan</th>

            <td>
                {{ $realisasi->jenis_pengajuan }}
            </td>
        </tr>

        <tr>
            <th>Total Realisasi</th>

            <td>
                Rp {{ number_format($realisasi->total_realisasi,0,',','.') }}
            </td>
        </tr>

        <tr>
            <th>Tanggal Realisasi</th>

            <td>
                {{ $realisasi->tgl_realisasi }}
            </td>
        </tr>

    </table>

</body>
</html>