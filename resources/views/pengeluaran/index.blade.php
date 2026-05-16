@extends('pegawai.layout')

@section('title', 'Transaksi Pengeluaran')

@push('styles')
<style>
    .hero-pengeluaran {
        background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 50%, #52b788 100%);
        border-radius: 26px;
        padding: 32px 36px;
        color: #fff;
        margin-bottom: 28px;
        box-shadow: 0 14px 40px rgba(27, 67, 50, 0.22);
    }

    .hero-pengeluaran .page-title,
    .hero-pengeluaran .page-subtitle {
        color: #fff;
    }

    .hero-pengeluaran .step-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 999px;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .section-pengeluaran {
        background: #fff;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        border-left: 5px solid #2d6a4f;
        box-shadow: 0 8px 24px rgba(45, 106, 79, 0.08);
    }

    .section-pengeluaran.siap-input {
        border-left-color: #40916c;
        background: linear-gradient(to right, #f0fdf4 0%, #ffffff 14%);
    }

    .section-pengeluaran.daftar {
        border-left-color: #1b4332;
    }

    .section-pengeluaran h4 {
        color: #1b4332;
        font-weight: 700;
    }

    .pengajuan-pengeluaran-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        padding: 18px 20px;
        border: 1px solid #d8f3dc;
        border-radius: 16px;
        margin-bottom: 12px;
        background: #fafdfb;
    }

    .pengajuan-pengeluaran-row:hover {
        border-color: #95d5b2;
        background: #f1faf4;
    }

    .pengeluaran-receipt {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 18px 20px;
        border: 1px dashed #95d5b2;
        border-radius: 16px;
        margin-bottom: 12px;
        background: #fff;
        position: relative;
    }

    .pengeluaran-receipt::before {
        content: '🧾';
        font-size: 1.75rem;
        line-height: 1;
        flex-shrink: 0;
    }

    .pengeluaran-receipt .nominal {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1b4332;
    }

    .btn-pengeluaran {
        background: #2d6a4f;
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 10px 20px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }

    .btn-pengeluaran:hover {
        background: #1b4332;
        color: #fff;
    }

    .link-realisasi-hint {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        color: #1e4d7b;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .link-realisasi-hint:hover {
        background: #dbeafe;
        color: #1e4d7b;
    }

    .empty-pengeluaran {
        text-align: center;
        padding: 40px;
        color: #6b7280;
    }
</style>
@endpush

@section('content')

<div class="hero-pengeluaran">
    <span class="step-badge">Langkah 2 · Transaksi Pengeluaran</span>
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title mb-2">Transaksi Pengeluaran</h1>
            <p class="page-subtitle mb-0 opacity-90">
                Input rincian pengeluaran per kategori setelah realisasi dana selesai.
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm rounded-pill px-4">
            ← Dashboard
        </a>
    </div>
</div>

<a href="{{ route('realisasi.index') }}" class="link-realisasi-hint">
    ← Belum realisasi? Kembali ke halaman <strong>Realisasi Dana</strong>
</a>

{{-- SIAP INPUT PENGELUARAN --}}
<div class="section-pengeluaran siap-input">
    <div class="d-flex align-items-center gap-2 mb-3">
        <span style="font-size:1.5rem;">📝</span>
        <div>
            <h4 class="mb-0">Siap Input Pengeluaran</h4>
            <p class="text-muted mb-0 small">Pengajuan yang sudah direalisasi dan siap diisi rincian biayanya.</p>
        </div>
    </div>

    @forelse($pengajuanSiapPengeluaran as $item)
        <div class="pengajuan-pengeluaran-row">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                        {{ str_replace('_', ' ', $item->jenis_pengajuan) }}
                    </span>
                    <small class="text-muted">#{{ $item->id_pengajuan }}</small>
                </div>
                <h6 class="fw-bold mb-1">{{ $item->tujuan }}</h6>
                <small class="text-muted">
                    Realisasi: Rp {{ number_format($item->realisasiDana->total_realisasi ?? 0, 0, ',', '.') }}
                    @if($item->jenis_pengajuan === 'PENGEMBALIAN')
                        · Sisa: Rp {{ number_format($item->sisaDana ?? 0, 0, ',', '.') }}
                    @endif
                </small>
            </div>
            <div class="text-end d-flex flex-column gap-2 align-items-end">
                <a href="{{ route('pengajuan.pdf', $item->id_pengajuan) }}"
                   class="btn btn-sm btn-outline-secondary rounded-pill"
                   target="_blank">Unduh PDF Pengajuan</a>
                @if($item->transaksiPengeluaran->count() > 0)
                    <span class="badge bg-secondary">Sudah ada {{ $item->transaksiPengeluaran->count() }} transaksi</span>
                    <a href="#daftar-transaksi" class="btn btn-outline-success btn-sm rounded-pill">
                        Lihat di bawah
                    </a>
                @else
                    <a href="{{ route('pengeluaran.create', $item->id_pengajuan) }}" class="btn-pengeluaran">
                        + Input Pengeluaran
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-pengeluaran">
            <p class="mb-2">Belum ada pengajuan siap input pengeluaran.</p>
            <a href="{{ route('realisasi.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                Ke Realisasi Dana
            </a>
        </div>
    @endforelse
</div>

{{-- DAFTAR TRANSAKSI --}}
<div class="section-pengeluaran daftar" id="daftar-transaksi">
    <div class="d-flex align-items-center gap-2 mb-3">
        <span style="font-size:1.5rem;">📑</span>
        <div>
            <h4 class="mb-0">Riwayat Transaksi Pengeluaran</h4>
            <p class="text-muted mb-0 small">Semua rincian pengeluaran yang sudah Anda input.</p>
        </div>
    </div>

    @forelse($pengeluaran as $item)
        <div class="pengeluaran-receipt">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-1">
                    <div>
                        <strong>{{ $item->uraian }}</strong>
                        <div class="text-muted small">
                            Pengajuan #{{ $item->id_pengajuan }} · {{ $item->pengajuan->tujuan ?? '-' }}
                        </div>
                    </div>
                    <span class="nominal">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                    <small class="text-muted">
                        {{ $item->tanggal_pengeluaran ? $item->tanggal_pengeluaran->format('d/m/Y') : '-' }}
                        · {{ str_replace('_', ' ', $item->jenis_pengeluaran) }}
                    </small>
                    @if($item->status == 'verifikasi_pengeluaran')
                        <span class="badge-status badge-verifikasi">Menunggu Verifikasi</span>
                    @elseif($item->status == 'pembayaran')
                        <span class="badge-status badge-pembayaran">Pembayaran</span>
                    @elseif($item->status == 'transaksi_tercatat')
                        <span class="badge-status badge-tercatat">Tercatat</span>
                    @elseif($item->status == 'ditolak')
                        <span class="badge-status badge-ditolak">Ditolak</span>
                    @endif
                    <a href="{{ route('pengeluaran.show', $item->id_transaksi_pengeluaran) }}"
                       class="btn btn-sm btn-outline-success rounded-pill ms-auto">
                        Detail
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-pengeluaran">
            <p class="mb-0">Belum ada transaksi pengeluaran.</p>
        </div>
    @endforelse
</div>

@endsection
