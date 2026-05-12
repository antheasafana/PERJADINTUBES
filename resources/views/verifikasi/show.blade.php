@extends('admin.layout')

@section('title', 'Detail Verifikasi')

@section('content')

<div class="top-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title">Detail Verifikasi</h1>
            <p class="page-subtitle mb-0">
                Detail request verifikasi {{ $verifikasi->transaksiPengeluaran ? 'pengeluaran' : 'pengajuan' }}.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('verifikasi.index') }}" class="btn btn-outline-secondary mb-3">
                ← Kembali Verifikasi
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
                Dashboard
            </a>
        </div>
    </div>
</div>

<div class="table-card mb-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="text-secondary mb-1">ID Verifikasi</div>
            <div class="fw-semibold">#{{ $verifikasi->id }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">ID Pengajuan</div>
            <div class="fw-semibold">#{{ $verifikasi->id_pengajuan }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Jenis Pengajuan</div>
            <div class="fw-semibold">{{ str_replace('_', ' ', $verifikasi->pengajuan->jenis_pengajuan ?? '-') }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Tujuan</div>
            <div class="fw-semibold">{{ $verifikasi->pengajuan->tujuan ?? '-' }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Status Verifikasi</div>
            <div>
                <span class="badge-status {{ $verifikasi->status == 'pending' ? 'badge-verifikasi' : ($verifikasi->status == 'approve' ? 'badge-realisasi' : 'badge-ditolak') }}">
                    {{ $verifikasi->statusLabel ?? ucfirst($verifikasi->status) }}
                </span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Tanggal Permintaan</div>
            <div class="fw-semibold">{{ optional($verifikasi->created_at)->format('d M Y, H:i') }}</div>
        </div>

        @if($verifikasi->catatan)
            <div class="col-md-12">
                <div class="text-secondary mb-1">Catatan</div>
                <div class="fw-semibold">{{ $verifikasi->catatan }}</div>
            </div>
        @endif

        @if($verifikasi->alasan_reject)
            <div class="col-md-12">
                <div class="text-secondary mb-1">Alasan Reject</div>
                <div class="fw-semibold">{{ $verifikasi->alasan_reject }}</div>
            </div>
        @endif

        @if($verifikasi->transaksiPengeluaran)
            <div class="col-md-4">
                <div class="text-secondary mb-1">ID Transaksi</div>
                <div class="fw-semibold">#{{ $verifikasi->id_transaksi_pengeluaran }}</div>
            </div>

            <div class="col-md-4">
                <div class="text-secondary mb-1">Nominal</div>
                <div class="fw-semibold">Rp {{ number_format($verifikasi->transaksiPengeluaran->nominal ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="col-md-4">
                <div class="text-secondary mb-1">Uraian</div>
                <div class="fw-semibold">{{ $verifikasi->transaksiPengeluaran->uraian ?? '-' }}</div>
            </div>

            <div class="col-md-12">
                <div class="text-secondary mb-1">Bukti Pengeluaran</div>
                @if($verifikasi->transaksiPengeluaran->bukti)
                    <a href="{{ asset('bukti_pengeluaran/' . $verifikasi->transaksiPengeluaran->bukti) }}" target="_blank" class="btn btn-outline-green btn-sm">
                        Lihat Bukti
                    </a>
                @else
                    -
                @endif
            </div>
        @endif
    </div>
</div>

@if($verifikasi->status == 'pending')
    <div class="table-card">
        <h4 class="fw-bold mb-3">Aksi Verifikasi</h4>

        <div class="row g-4">
            <div class="col-md-6">
                <form action="{{ route('verifikasi.approve', $verifikasi->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-green w-100">
                        Setujui {{ $verifikasi->transaksiPengeluaran ? 'Pengeluaran' : 'Pengajuan' }}
                    </button>
                </form>
            </div>

            <div class="col-md-6">
                <form action="{{ route('verifikasi.reject', $verifikasi->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Alasan Reject</label>
                        <textarea name="catatan" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-danger w-100">
                        Tolak {{ $verifikasi->transaksiPengeluaran ? 'Pengeluaran' : 'Pengajuan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

@if(
    $verifikasi->transaksiPengeluaran &&
    $verifikasi->transaksiPengeluaran->status == 'pembayaran'
)

<form
    action="{{ route('pengeluaran.pembayaran', $verifikasi->transaksiPengeluaran->id_transaksi_pengeluaran) }}"
    method="POST"
>
    @csrf

    <button class="btn btn-success">
        Proses Pembayaran
    </button>
</form>

@endif
@endsection
