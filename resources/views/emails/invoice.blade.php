<h2>
    Invoice Realisasi Dana
</h2>

<p>
    Halo,
</p>

<p>
    Pengajuan perjalanan dinas Anda telah terealisasi.
</p>

<p>
    Detail realisasi:
</p>

<ul>

    <li>
        Tujuan:
        {{ $data['tujuan'] }}
    </li>

    <li>
        Jenis Pengajuan:
        {{ $data['jenis_pengajuan'] }}
    </li>

    <li>
        Total Realisasi:
        Rp {{ number_format($data['total_realisasi'],0,',','.') }}
    </li>

</ul>

<p>
    Invoice PDF terlampir pada email ini.
</p>

<p>
    Terima kasih.
</p>