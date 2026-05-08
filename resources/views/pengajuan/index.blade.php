<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f7fe;">

<div class="container mt-5">

    <div class="d-flex justify-content-between mb-4">
        <h2>Pengajuan Saya</h2>

        <a href="/pengajuan/create" class="btn btn-primary">
            + Buat Pengajuan
        </a>
    </div>

    {{-- popup sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <table class="table">
                <thead>
                    <tr>
                        <th>Jenis Pengajuan</th>
                        <th>Tujuan</th>
                        <th>Tanggal Berangkat</th>
                        <th>Tanggal Kembali</th>
                        <th>Estimasi Biaya</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>{{ $item->jenis_pengajuan }}</td>
                        <td>{{ $item->tujuan }}</td>
                        <td>{{ $item->tgl_berangkat }}</td>
                        <td>{{ $item->tgl_kembali }}</td>
                        <td>Rp {{ number_format($item->estimasi_biaya,0,',','.') }}</td>

                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Belum ada pengajuan
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>