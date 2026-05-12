@extends('pegawai.layout')

@section('title', 'Detail Transaksi Pengeluaran')

@section('content')

<div class="top-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title">Detail Transaksi Pengeluaran</h1>
            <p class="page-subtitle mb-0">
                Detail transaksi untuk pengajuan #{{ $pengeluaran->id_pengajuan }}.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
                ← Kembali Dashboard
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="btn btn-outline-secondary mb-3">
                Kembali Transaksi
            </a>
        </div>
    </div>
</div>

<div class="table-card mb-4">
    <h4 class="fw-bold mb-3">Informasi Pengajuan</h4>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="text-secondary mb-1">ID Pengajuan</div>
            <div class="fw-semibold">#{{ $pengeluaran->id_pengajuan }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Jenis Pengajuan</div>
            <div class="fw-semibold">{{ str_replace('_', ' ', $pengeluaran->jenis_pengeluaran) }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Status Transaksi</div>
            <div>
                @if($pengeluaran->status == 'verifikasi_pengeluaran')
                    <span class="badge-status badge-verifikasi">Verifikasi</span>
                @elseif($pengeluaran->status == 'pembayaran')
                    <span class="badge-status badge-pembayaran">Pembayaran</span>
                @elseif($pengeluaran->status == 'transaksi_tercatat')
                    <span class="badge-status badge-tercatat">Tercatat</span>
                @elseif($pengeluaran->status == 'ditolak')
                    <span class="badge-status badge-ditolak">Ditolak</span>
                @else
                    <span class="badge bg-secondary">{{ $pengeluaran->status }}</span>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Tujuan Perjalanan</div>
            <div class="fw-semibold">{{ $pengeluaran->pengajuan->tujuan ?? '-' }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Tanggal Pengeluaran</div>
            <div class="fw-semibold">{{ $pengeluaran->tanggal_pengeluaran ? $pengeluaran->tanggal_pengeluaran->format('d/m/Y') : '-' }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Nominal</div>
            <div class="fw-semibold">Rp {{ number_format($pengeluaran->nominal, 0, ',', '.') }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Akun</div>
            <div class="fw-semibold">{{ $pengeluaran->akun->nama_akun ?? '-' }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Kategori Biaya</div>
            <div class="fw-semibold">{{ $pengeluaran->kategoriBiaya->jenis_biaya ?? '-' }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Uraian</div>
            <div class="fw-semibold">{{ $pengeluaran->uraian }}</div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Realisasi Dana</div>
            <div class="fw-semibold">
                Rp {{ number_format($pengeluaran->pengajuan->realisasiDana->total_realisasi ?? 0, 0, ',', '.') }}
            </div>
        </div>

        <div class="col-md-4">
            <div class="text-secondary mb-1">Sisa Dana</div>
            <div class="fw-semibold">
                Rp {{ number_format($pengeluaran->pengajuan->sisaDana ?? 0, 0, ',', '.') }}
            </div>
        </div>

        @if($pengeluaran->bukti)
            <div class="col-md-12">
                <div class="text-secondary mb-1">Bukti Pengeluaran</div>
                <a href="{{ asset('bukti_pengeluaran/' . $pengeluaran->bukti) }}" target="_blank" class="btn btn-outline-green btn-sm">
                    Lihat Bukti
                </a>
            </div>
        @endif

        @if($pengeluaran->catatan_verifikasi)
            <div class="col-md-12">
                <div class="text-secondary mb-1">Catatan Verifikasi</div>
                <div class="fw-semibold">{{ $pengeluaran->catatan_verifikasi }}</div>
            </div>
        @endif
    </div>
</div>

<div class="table-card">
    <h4 class="fw-bold mb-3">Status Transaksi</h4>
    @if($pengeluaran->status == 'verifikasi_pengeluaran')
        <p class="text-muted mb-0">
            Transaksi ini saat ini menunggu verifikasi admin.
        </p>
    @elseif($pengeluaran->status == 'pembayaran')
        <p class="text-muted mb-0">
            Pengeluaran telah diverifikasi dan menunggu proses pembayaran admin.
        </p>
    @elseif($pengeluaran->status == 'transaksi_tercatat')
        <p class="text-muted mb-0">
            Transaksi ini telah selesai dan tercatat.
        </p>
    @elseif($pengeluaran->status == 'ditolak')
        <p class="text-muted mb-0">
            Transaksi ini ditolak dan tidak dapat diproses lebih lanjut.
        </p>
    @else
        <p class="text-muted mb-0">
            Status transaksi: {{ str_replace('_', ' ', $pengeluaran->status) }}.
        </p>
    @endif
</div>

@endsection
