@extends('pegawai.layout')

@section('title', 'Detail Pengajuan')

@push('styles')
    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0b3d2e;
        margin-bottom: 0;
    }

    .card-custom {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .card-custom .card-header {
        background: #2d6a4f;
        color: white;
        padding: 24px 32px;
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
@endpush

@section('content')

<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="page-title">📄 Detail Pengajuan</h1>
            <p class="text-muted mb-0">Lihat detail, status, dan dokumen pengajuan Anda.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-kembali">
                ← Kembali Dashboard
            </a>
            <a href="{{ route('pengajuan.index') }}" class="btn btn-kembali">
                Kembali Pengajuan Saya
            </a>
        </div>
    </div>

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

    <div class="status-box" style="background: {{ $statusConfig['bg'] }}; border: 2px solid {{ $statusConfig['border'] }};">
        <h5 class="fw-bold mb-2">{{ $statusConfig['title'] }}</h5>
        <p class="mb-0 text-muted">{{ $statusConfig['text'] }}</p>
    </div>

    <div class="card card-custom">
        <div class="card-header">
            <h3 class="mb-0">📋 Data Pengajuan #{{ $pengajuan->id_pengajuan }}</h3>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Jenis Pengajuan</div>
                    @if($pengajuan->jenis_pengajuan == 'UANG_MUKA')
                        <span class="badge bg-primary badge-custom">Uang Muka</span>
                    @else
                        <span class="badge bg-warning text-dark badge-custom">Reimbursement</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Tujuan Perjalanan</div>
                    <div class="fw-semibold">{{ $pengajuan->tujuan }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Tanggal Berangkat</div>
                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($pengajuan->tgl_berangkat)->format('d M Y') }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Tanggal Kembali</div>
                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($pengajuan->tgl_kembali)->format('d M Y') }}</div>
                </div>
                @if($pengajuan->estimasi_biaya)
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Estimasi Biaya</div>
                        <div class="fw-semibold text-success">Rp {{ number_format($pengajuan->estimasi_biaya, 0, ',', '.') }}</div>
                    </div>
                @endif
                <div class="col-md-6">
                    <div class="text-muted small mb-1">Tanggal Dibuat</div>
                    <div class="fw-semibold">{{ $pengajuan->created_at->format('d M Y, H:i') }} WIB</div>
                </div>
            </div>

            <hr class="my-5">

            <h4 class="fw-bold mb-4">📎 Dokumen Terlampir</h4>

            @php
                $dokumen = $pengajuan->dokumen ?? [];
                if (is_string($dokumen)) {
                    $dokumen = json_decode($dokumen, true);
                }
            @endphp

            @if(!empty($dokumen['file']))
                <div class="dokumen-box d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1">File Pengajuan</h6>
                        <small class="text-muted">{{ $dokumen['file'] }}</small>
                    </div>
                    <a href="{{ asset('dokumen/' . $dokumen['file']) }}" target="_blank" class="btn btn-primary btn-lihat">Lihat</a>
                </div>
            @else
                <p class="text-muted">Tidak ada dokumen terlampir.</p>
            @endif
        </div>
    </div>
</div>

@endsection
