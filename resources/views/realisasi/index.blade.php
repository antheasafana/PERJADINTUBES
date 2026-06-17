@extends('pegawai.layout')

@section('title', 'Realisasi Dana')

@push('styles')
<style>
    .hero-realisasi {
    background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 55%, #52b788 100%);
        border-radius: 26px;
        padding-bottom: 60px;
        color: #0bdf40;
        margin-bottom: 28px;
        box-shadow: 0 14px 40px rgba(30, 77, 123, 0.25);
    }

    .hero-realisasi .page-title,
    .hero-realisasi .page-subtitle {
           color: #084b12;
    }

    .hero-realisasi .step-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 999px;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .section-realisasi {
        background: #fff;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        border-left: 5px solid #3b82c4;
        box-shadow: 0 8px 24px rgba(30, 77, 123, 0.08);
    }

    .section-realisasi.pending {
        border-left-color: #f59e0b;
        background: linear-gradient(to right, #fffbeb 0%, #ffffff 12%);
    }

    .section-realisasi.done {
        border-left-color: #2563eb;
        background: linear-gradient(to right, #eff6ff 0%, #ffffff 12%);
    }

    .section-realisasi h4 {
        color: #1e4d7b;
        font-weight: 700;
    }

    .realisasi-card {
        border: 1px solid #dbeafe;
        border-radius: 18px;
        padding: 20px;
        height: 100%;
        background: #fff;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .realisasi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(37, 99, 235, 0.12);
    }

    .realisasi-card.pending-card {
        border-color: #fde68a;
        background: #fffdf5;
    }

    .realisasi-card .jenis-tag {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #1e4d7b;
        background: #dbeafe;
        padding: 4px 10px;
        border-radius: 8px;
    }

    .realisasi-card.pending-card .jenis-tag {
        background: #fef3c7;
        color: #b45309;
    }

    .nominal-highlight {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1d4ed8;
    }

    .btn-realisasi {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 10px 20px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }

    .btn-realisasi:hover {
        color: #fff;
        opacity: 0.92;
    }

    .btn-realisasi-outline {
        border: 2px solid #3b82c4;
        color: #1e4d7b;
        border-radius: 999px;
        padding: 8px 18px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        background: #fff;
    }

    .btn-realisasi-outline:hover {
        background: #eff6ff;
        color: #1e4d7b;
    }

    .empty-realisasi {
        text-align: center;
        padding: 48px 24px;
        color: #64748b;
    }

    .empty-realisasi .icon {
        font-size: 3rem;
        margin-bottom: 12px;
    }
</style>
@endpush

@section('content')

<a href="{{ route('dashboard') }}"
   class="btn btn-sm rounded-pill px-4 mb-4"
   style="background:#2d6a4f; color:white; border:none;">
            ← Dashboard
        </a>
<div class="hero-realisasi">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title mb-2">Realisasi Dana</h1>
            <p class="page-subtitle mb-0 opacity-90">
                Catat total dana aktual yang Anda keluarkan saat perjalanan dinas (reimbursement & pengembalian).
            </p>
        </div>
</div>
<div style="margin-bottom: 40px;"></div>
@if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

{{-- MENUNGGU REALISASI --}}
<div class="section-realisasi pending">
    <div class="d-flex align-items-center gap-2 mb-3">
        <span style="font-size:1.5rem;">⏳</span>
        <div>
            <h4 class="mb-0">Menunggu Input Realisasi</h4>
            <p class="text-muted mb-0 small">Isi nominal sebelum lanjut ke transaksi pengeluaran.</p>
        </div>
    </div>

    @if($menungguRealisasi->count() > 0)
        <div class="row g-3">
            @foreach($menungguRealisasi as $item)
            <div class="col-md-6 col-lg-4">
                <div class="realisasi-card pending-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="jenis-tag">{{ str_replace('_', ' ', $item->jenis_pengajuan) }}</span>
                        <small class="text-muted">#{{ $item->id_pengajuan }}</small>
                    </div>
                    <h6 class="fw-bold mb-2">{{ $item->tujuan }}</h6>
                    <p class="text-muted small mb-2">Estimasi pengajuan</p>
                    <p class="nominal-highlight mb-3" style="color:#b45309;">
                        Rp {{ number_format($item->estimasi_biaya, 0, ',', '.') }}
                    </p>
                    <a href="{{ route('pengajuan.realisasi', $item->id_pengajuan) }}" class="btn-realisasi w-100 text-center">
                        Input Realisasi Dana
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-realisasi">
            <div class="icon">✅</div>
            <p class="mb-0">Tidak ada pengajuan yang menunggu realisasi.</p>
        </div>
    @endif
</div>

{{-- SUDAH REALISASI --}}
<div class="section-realisasi done">
    <div class="d-flex align-items-center gap-2 mb-3">
        <span style="font-size:1.5rem;">💰</span>
        <div>
            <h4 class="mb-0">Sudah Direalisasi</h4>
            <p class="text-muted mb-0 small">Lanjutkan ke menu Transaksi Pengeluaran untuk input rincian biaya.</p>
        </div>
    </div>

    @if($sudahRealisasi->count() > 0)
        <div class="row g-3">
            @foreach($sudahRealisasi as $item)
            <div class="col-md-6 col-lg-4">
                <div class="realisasi-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="jenis-tag">{{ str_replace('_', ' ', $item->jenis_pengajuan) }}</span>
                        <small class="text-muted">{{ $item->realisasiDana->tgl_realisasi ? \Carbon\Carbon::parse($item->realisasiDana->tgl_realisasi)->format('d/m/Y') : '-' }}</small>
                    </div>
                    <h6 class="fw-bold mb-2">{{ $item->tujuan }}</h6>
                    <p class="text-muted small mb-1">Total realisasi</p>
                    <p class="nominal-highlight mb-3">
                        Rp {{ number_format($item->realisasiDana->total_realisasi ?? 0, 0, ',', '.') }}
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('pengajuan.realisasi.pdf', $item->id_pengajuan) }}"
                           class="btn-realisasi-outline w-100 text-center"
                           target="_blank">
                            📄 Unduh PDF Realisasi
                        </a>

                    <form action="{{ route('realisasi.batalkan', $item->id_pengajuan) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                onclick="return confirm('Batalkan realisasi dana ini?')"
                                class="btn-realisasi-outline w-100 text-center"
                            >
                                Batalkan Realisasi
                            </button>
                        </form>
                                                
                        <a href="{{ route('pengeluaran.index') }}" class="btn-realisasi-outline w-100 text-center">
                            Ke Transaksi Pengeluaran →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-realisasi">
            <div class="icon">📋</div>
            <p class="mb-0">Belum ada data realisasi dana.</p>
        </div>
    @endif
</div>

@endsection
