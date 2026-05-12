@extends('pegawai.layout')

@section('title', 'Edit Pengajuan')

@push('styles')
    .card-custom {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 80px rgba(0, 0, 0, 0.08);
    }

    .card-custom .card-header {
        background-color: #2d6a4f;
        color: white;
        padding: 22px 28px;
        border-radius: 16px 16px 0 0;
    }

    .btn-simpan {
        background-color: #2d6a4f;
        border-color: #2d6a4f;
        color: white;
        border-radius: 20px;
        padding: 10px 26px;
    }

    .btn-simpan:hover {
        background-color: #1b4332;
        border-color: #1b4332;
        color: white;
    }

    .btn-kembali {
        background-color: #f4f4f4;
        border-color: #ddd;
        color: #555;
        border-radius: 20px;
        padding: 10px 24px;
        text-decoration: none;
    }

    .btn-kembali:hover {
        background-color: #e0e0e0;
    }

    .form-control:focus {
        border-color: #52b788;
        box-shadow: 0 0 0 0.15rem rgba(82, 183, 136, 0.25);
    }

    .form-label {
        font-weight: 600;
        color: #1b4332;
    }
@endpush

@section('content')

<div class="container py-5" style="max-width: 760px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">✏️ Edit Pengajuan</h1>
            <p class="text-muted mb-0">Perbarui data pengajuan Anda sebelum dikirim ulang.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                ← Kembali Dashboard
            </a>
            <a href="{{ route('pengajuan.index') }}" class="btn btn-kembali">
                Kembali Pengajuan Saya
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-custom border-0">
        <div class="card-header">
            <h5 class="mb-0">📋 Data Pengajuan #{{ $pengajuan->id_pengajuan }}</h5>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('pengajuan.update', $pengajuan->id_pengajuan) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Jenis Pengajuan</label>
                    <input type="text" class="form-control bg-light" value="{{ $pengajuan->jenis_pengajuan }}" readonly>
                    <small class="text-muted">Jenis pengajuan tidak dapat diubah.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tujuan Perjalanan</label>
                    <input type="text" class="form-control" name="tujuan" value="{{ old('tujuan', $pengajuan->tujuan) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Berangkat</label>
                        <input type="date" class="form-control" name="tgl_berangkat" value="{{ old('tgl_berangkat', $pengajuan->tgl_berangkat) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Kembali</label>
                        <input type="date" class="form-control" name="tgl_kembali" value="{{ old('tgl_kembali', $pengajuan->tgl_kembali) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Estimasi Biaya</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:#d8f3dc; border-color:#52b788; color:#1b4332; font-weight:600;">Rp</span>
                        <input type="number" class="form-control" name="estimasi_biaya" value="{{ old('estimasi_biaya', $pengajuan->estimasi_biaya) }}">
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-simpan">💾 Simpan Perubahan</button>
                    <a href="{{ route('pengajuan.index') }}" class="btn btn-kembali">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
