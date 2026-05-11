<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Pengajuan #{{ $pengajuan->id_pengajuan }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7f4;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-title {
            font-size: 48px;
            font-weight: 800;
            color: #0b3d2e;
        }

        .card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        .card-header {
            background: #2d6a4f;
            color: white;
            padding: 24px 32px;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 700;
        }

        .card-body {
            padding: 35px;
            background: white;
        }

        .info-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 18px;
            font-weight: 600;
            color: #212529;
        }

        .status-box {
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .btn-kembali {
            border-radius: 999px;
            padding: 10px 28px;
            background: white;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-kembali:hover {
            background: #f1f1f1;
        }

        .badge-custom {
            font-size: 15px;
            padding: 10px 18px;
            border-radius: 12px;
        }

        .dokumen-box {
            background: #f8f9fa;
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 15px;
        }

        .btn-lihat {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container py-5">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-4 mb-4">

        <a href="{{ route('pengajuan.index') }}" class="btn-kembali">
            ← Kembali
        </a>

        <h1 class="page-title mb-0">
            📄 Detail Pengajuan
        </h1>

    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">

            {{-- STATUS --}}
            @php
                $statusConfig = match($pengajuan->status) {

                    'Approved' => [
                        'bg' => '#E6FFFA',
                        'border' => '#13DEB9',
                        'title' => 'Pengajuan Disetujui',
                        'text' => 'Pengajuan Anda telah disetujui admin.'
                    ],

                    'Rejected' => [
                        'bg' => '#FDEDE8',
                        'border' => '#FA896B',
                        'title' => 'Pengajuan Ditolak',
                        'text' => 'Pengajuan Anda ditolak.'
                    ],

                    default => [
                        'bg' => '#FEF5E5',
                        'border' => '#FFAE1F',
                        'title' => 'Menunggu Persetujuan',
                        'text' => 'Pengajuan Anda sedang diproses admin.'
                    ]
                };
            @endphp

            <div class="status-box"
                 style="background: {{ $statusConfig['bg'] }};
                        border: 2px solid {{ $statusConfig['border'] }};">

                <h5 class="fw-bold mb-2">
                    {{ $statusConfig['title'] }}
                </h5>

                <p class="mb-0 text-muted">
                    {{ $statusConfig['text'] }}
                </p>

            </div>

            {{-- CARD --}}
            <div class="card">

                <div class="card-header">
                    <h3>📋 Data Pengajuan #{{ $pengajuan->id_pengajuan }}</h3>
                </div>

                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-6">

                            <div class="info-label">
                                Jenis Pengajuan
                            </div>

                            @if($pengajuan->jenis_pengajuan == 'UANG_MUKA')

                                <span class="badge bg-primary badge-custom">
                                    Uang Muka
                                </span>

                            @else

                                <span class="badge bg-warning text-dark badge-custom">
                                    Reimbursement
                                </span>

                            @endif

                        </div>

                        <div class="col-md-6">

                            <div class="info-label">
                                Tujuan Perjalanan
                            </div>

                            <div class="info-value">
                                {{ $pengajuan->tujuan }}
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-label">
                                Tanggal Berangkat
                            </div>

                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($pengajuan->tgl_berangkat)->format('d M Y') }}
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-label">
                                Tanggal Kembali
                            </div>

                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($pengajuan->tgl_kembali)->format('d M Y') }}
                            </div>

                        </div>

                        @if($pengajuan->estimasi_biaya)

                        <div class="col-md-6">

                            <div class="info-label">
                                Estimasi Biaya
                            </div>

                            <div class="info-value text-success">
                                Rp {{ number_format($pengajuan->estimasi_biaya, 0, ',', '.') }}
                            </div>

                        </div>

                        @endif

                        <div class="col-md-6">

                            <div class="info-label">
                                Tanggal Dibuat
                            </div>

                            <div class="info-value">
                                {{ $pengajuan->created_at->format('d M Y, H:i') }} WIB
                            </div>

                        </div>

                    </div>

                    <hr class="my-5">

                    {{-- DOKUMEN --}}
                    <h4 class="fw-bold mb-4">
                        📎 Dokumen Terlampir
                    </h4>

                    @php

                        $dokumen = $pengajuan->dokumen ?? [];

                        if(is_string($dokumen)){
                            $dokumen = json_decode($dokumen, true);
                        }

                    @endphp

                    @if(!empty($dokumen['file']))

                    <div class="dokumen-box d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="fw-bold mb-1">
                                File Pengajuan
                            </h6>

                            <small class="text-muted">
                                {{ $dokumen['file'] }}
                            </small>

                        </div>

                        <a href="{{ asset('dokumen/' . $dokumen['file']) }}"
                           target="_blank"
                           class="btn btn-primary btn-lihat">

                            Lihat

                        </a>

                    </div>

                    @else

                    <p class="text-muted">
                        Tidak ada dokumen terlampir.
                    </p>

                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>