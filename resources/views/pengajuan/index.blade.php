@extends('pegawai.layout')

@section('title', 'Pengajuan Saya')

@push('styles')
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
    }

    .page-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: #1F5E46;
        margin: 0;
    }

    .btn-add {
        background: #2E7D5B;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 30px;
        font-weight: 500;
        text-decoration: none;
    }

    .btn-add:hover {
        background: #25674b;
        color: white;
    }

    .table-card {
        background: white;
        border-radius: 25px;
        padding: 30px;
        box-shadow: 0px 5px 20px rgba(0,0,0,0.05);
    }

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

    .btn-view {
        background: #49B87C;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 8px 18px;
        text-decoration: none;
    }

    .btn-view:hover {
        background: #389d66;
        color: white;
    }

    .btn-edit {
        background: #F4A261;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 8px 18px;
        text-decoration: none;
    }

    .btn-edit:hover {
        background: #E78A3C;
        color: white;
    }

    .btn-danger {
        border-radius: 20px;
        padding: 8px 18px;
    }

    .empty-state {
        padding: 40px;
        color: #6b7280;
        font-size: 1rem;
    }
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">🌿 Pengajuan Saya</h1>
        <p class="text-muted">Kelola pengajuan perjalanan dinas dan lihat status terbaru.</p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            ← Kembali Dashboard
        </a>
        <a href="{{ route('pengajuan.create') }}" class="btn-add">
            + Buat Pengajuan
        </a>
    </div>
</div>

<div class="table-card">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Jenis Pengajuan</th>
                    <th>Tujuan</th>
                    <th>Berangkat</th>
                    <th>Kembali</th>
                    <th>Estimasi Biaya</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($data as $item)
                    <tr>
                        <td>{{ str_replace('_', ' ', $item->jenis_pengajuan) }}</td>
                        <td>{{ $item->tujuan }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_berangkat)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') }}</td>
                        <td>Rp {{ number_format($item->estimasi_biaya,0,',','.') }}</td>

                        <td>
                            @php
                                $status = $item->status;

                                $badgeClass = match($status) {
                                    'Approved' => 'badge-approved',
                                    'Disetujui' => 'badge-approved',
                                    'Rejected' => 'badge-rejected',
                                    'Ditolak' => 'badge-rejected',
                                    'Verifikasi Pengeluaran' => 'badge-verifikasi',
                                    'Pembayaran' => 'badge-pembayaran',
                                    'Realisasi Dana' => 'badge-approved',
                                    'Direalisasikan' => 'badge-approved',
                                    'Menunggu Realisasi Dana' => 'badge-verifikasi',
                                    default => 'badge-diajukan',
                                };
                            @endphp

                            <span class="{{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex flex-wrap gap-2">

                                @if(
                                    in_array($item->jenis_pengajuan, ['REIMBURSEMENT', 'PENGEMBALIAN'])
                                    && $item->realisasiDana
                                    && $item->realisasiDana->status === 'PENDING'
                                )
                                <a href="{{ route('pengajuan.realisasi', $item->id_pengajuan) }}"
                                   class="btn btn-sm rounded-pill"
                                   style="background:#3b82c4;color:#fff;">
                                    Input Realisasi
                                </a>
                                @endif

                                <a href="{{ route('pengajuan.pdf.ringkas', $item->id_pengajuan) }}"
                                   class="btn btn-sm btn-outline-secondary rounded-pill"
                                   target="_blank"
                                   title="PDF Pengajuan">
                                    📄 PDF
                                </a>

                                @if($item->realisasiDana && $item->realisasiDana->status === 'TEREALISASI')
                                <a href="{{ route('pengajuan.realisasi.pdf', $item->id_pengajuan) }}"
                                   class="btn btn-sm rounded-pill"
                                   style="background:#3b82c4;color:#fff;"
                                   target="_blank"
                                   title="PDF Realisasi">
                                    💰 Realisasi
                                </a>
                                @endif

                                @if($item->transaksiPengeluaran->count() > 0)
                                <a href="{{ route('pengajuan.pdf', $item->id_pengajuan) }}"
                                   class="btn btn-sm btn-outline-success rounded-pill"
                                   target="_blank"
                                   title="PDF + Pengeluaran">
                                    📋 Lengkap
                                </a>
                                @endif

                                <a href="{{ route('pengajuan.view', $item->id_pengajuan) }}"
                                   class="btn-view btn-sm">
                                    👁 View
                                </a>

                                <a href="{{ route('pengajuan.edit', $item->id_pengajuan) }}"
                                   class="btn-edit btn-sm">
                                    ✏ Edit
                                </a>

                                <form action="{{ route('pengajuan.destroy', $item->id_pengajuan) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus pengajuan ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm rounded-pill">
                                        🗑 Delete
                                    </button>

                                </form>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center empty-state">
                            🌱 Belum ada pengajuan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection