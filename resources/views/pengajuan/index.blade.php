<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan Saya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #EEF4EF;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Container */
        .main-container {
            padding: 50px;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .page-title {
            font-size: 48px;
            font-weight: 700;
            color: #1F5E46;
        }

        /* Button tambah */
        .btn-add {
            background: #2E7D5B;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 30px;
            font-weight: 500;
        }

        .btn-add:hover {
            background: #25674b;
            color: white;
        }

        /* Card table */
        .table-card {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0px 5px 20px rgba(0,0,0,0.05);
        }

        /* Table */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border: none;
            color: #1F5E46;
            font-weight: 600;
            font-size: 17px;
            padding-bottom: 20px;
        }

        .table tbody td {
            border: none;
            padding: 18px 10px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #F8FCF9;
        }

        /* Status badge */
        .badge-diajukan {
            background: #58C98B;
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
        }

        .badge-approved {
            background: #2E7D5B;
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
        }

        .badge-rejected {
            background: #E76F51;
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
        }

        /* Button view */
        .btn-view {
            background: #49B87C;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
        }

        .btn-view:hover {
            background: #389d66;
            color: white;
        }

        /* Button edit */
        .btn-edit {
            background: #F4A261;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
        }

        .btn-edit:hover {
            background: #E78A3C;
            color: white;
        }

        /* Alert */
        .alert-success {
            background: #D8F3DC;
            border: none;
            color: #1F5E46;
            border-radius: 15px;
        }

        /* Empty state */
        .empty-state {
            padding: 40px;
            color: #888;
            font-size: 18px;
        }
    </style>
</head>

<body>

<div class="main-container">

    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">🌿 Pengajuan Saya</h1>

        <a href="/pengajuan/create" class="btn btn-add">
            + Buat Pengajuan
        </a>
    </div>

    <!-- Alert sukses -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table Card -->
    <div class="table-card">

        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Jenis Pengajuan</th>
                    <th>Tujuan</th>
                    <th>Tanggal Berangkat</th>
                    <th>Tanggal Kembali</th>
                    <th>Estimasi Biaya</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>{{ $item->jenis_pengajuan }}</td>
                    <td>{{ $item->tujuan }}</td>
                    <td>{{ $item->tgl_berangkat }}</td>
                    <td>{{ $item->tgl_kembali }}</td>
                    <td>
                        Rp {{ number_format($item->estimasi_biaya,0,',','.') }}
                    </td>

                    <td>
                        @php
                            $status = $item->status;

                            $badgeClass = match($status) {
                                'Approved' => 'badge-approved',
                                'Rejected' => 'badge-rejected',
                                default => 'badge-diajukan',
                            };
                        @endphp

                        <span class="{{ $badgeClass }}">
                            {{ $status }}
                        </span>
                    </td>

                    <td>
                        <a href="{{ route('pengajuan.view', $item->id_pengajuan) }}"
                           class="btn btn-view btn-sm me-2">
                            👁 View
                        </a>

                        <a href="{{ route('pengajuan.edit', $item->id_pengajuan) }}"
                           class="btn btn-edit btn-sm">
                            ✏ Edit
                        </a>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="7" class="text-center empty-state">
                        🌱 Belum ada pengajuan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>