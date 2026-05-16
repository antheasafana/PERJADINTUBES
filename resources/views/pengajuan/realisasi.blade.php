@extends('pegawai.layout')

@section('title', 'Input Realisasi Dana')

@push('styles')
<style>
    .hero-realisasi-form {
        background: linear-gradient(135deg, #1e4d7b 0%, #3b82c4 100%);
        border-radius: 26px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 24px;
    }

    .hero-realisasi-form .page-title,
    .hero-realisasi-form .page-subtitle {
        color: #fff;
    }

    .form-realisasi-card {
        background: #fff;
        border-radius: 22px;
        padding: 28px;
        border-left: 5px solid #3b82c4;
        box-shadow: 0 10px 30px rgba(30, 77, 123, 0.1);
    }

    .info-pill {
        background: #eff6ff;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 12px;
    }

    .info-pill .label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .info-pill .value {
        font-weight: 600;
        color: #1e4d7b;
    }

    .btn-simpan-realisasi {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 12px 28px;
        font-weight: 600;
    }

    .btn-simpan-realisasi:hover {
        color: #fff;
        opacity: 0.9;
    }
</style>
@endpush

@section('content')

<div class="hero-realisasi-form">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <span class="badge bg-light text-primary mb-2">Form Realisasi Dana</span>
            <h1 class="page-title mb-2">Input Realisasi Dana</h1>
            <p class="page-subtitle mb-0 opacity-90">
                Catat total dana yang benar-benar Anda keluarkan selama perjalanan dinas.
            </p>
        </div>
        <a href="{{ route('realisasi.index') }}" class="btn btn-light btn-sm rounded-pill px-4">
            ← Kembali
        </a>
    </div>
</div>

<div class="form-realisasi-card">
    <h5 class="fw-bold text-primary mb-4">Pengajuan #{{ $pengajuan->id_pengajuan }}</h5>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="info-pill">
                <div class="label">Jenis</div>
                <div class="value">{{ str_replace('_', ' ', $pengajuan->jenis_pengajuan) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-pill">
                <div class="label">Tujuan</div>
                <div class="value">{{ $pengajuan->tujuan }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-pill">
                <div class="label">Estimasi Pengajuan</div>
                <div class="value">Rp {{ number_format($pengajuan->estimasi_biaya, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('pengajuan.realisasi.store', $pengajuan->id_pengajuan) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Total Realisasi Dana <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-primary text-white">Rp</span>
                <input type="number"
                       name="total_realisasi"
                       class="form-control form-control-lg @error('total_realisasi') is-invalid @enderror"
                       min="1"
                       step="1"
                       value="{{ old('total_realisasi') }}"
                       required>
            </div>
            @error('total_realisasi')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="text-muted">
                Nominal aktual yang Anda keluarkan selama perjalanan dinas.
                @if($pengajuan->jenis_pengajuan === 'PENGEMBALIAN')
                    Sisa dana akan dihitung setelah pengeluaran diinput.
                @endif
            </small>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Tanggal Realisasi <span class="text-danger">*</span></label>
            <input type="date"
                   name="tgl_realisasi"
                   class="form-control @error('tgl_realisasi') is-invalid @enderror"
                   value="{{ old('tgl_realisasi', now()->format('Y-m-d')) }}"
                   required>
            @error('tgl_realisasi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-simpan-realisasi">
            Simpan Realisasi Dana
        </button>
    </form>
</div>

@endsection
