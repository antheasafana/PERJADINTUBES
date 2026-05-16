@extends('admin.layout')

@section('title', 'Verifikasi')

@section('content')

<div class="top-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title">Verifikasi</h1>
            <p class="page-subtitle mb-0">
                Review semua permintaan verifikasi pengajuan dan pengeluaran dari pegawai.
            </p>
        </div>
        <div>
            <a href="{{ route('verifikasi.pdf') }}" target="_blank" class="btn btn-success">
                Unduh PDF Verifikasi
            </a>
        </div>
    </div>
</div>

<div class="table-card mb-4">
    <h4 class="fw-bold mb-3">Verifikasi Pengajuan</h4>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Verifikasi</th>
                    <th>Tujuan</th>
                    <th>Jenis</th>
                    <th>Estimasi</th>
                    <th>Waktu Request</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->pengajuan->tujuan ?? '-' }}</td>
                        <td>{{ str_replace('_', ' ', $item->pengajuan->jenis_pengajuan ?? '-') }}</td>
                        <td>Rp {{ number_format($item->pengajuan->estimasi_biaya ?? 0, 0, ',', '.') }}</td>
                        <td>{{ optional($item->created_at)->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('verifikasi.show', $item->id) }}" class="btn btn-primary btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada verifikasi pengajuan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="table-card">
    <h4 class="fw-bold mb-3">Verifikasi Pengeluaran</h4>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Verifikasi</th>
                    <th>ID Transaksi</th>
                    <th>Tujuan</th>
                    <th>Uraian</th>
                    <th>Nominal</th>
                    <th>Waktu Request</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengeluaran as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>#{{ $item->id }}</td>
                        <td>#{{ $item->id_transaksi_pengeluaran }}</td>
                        <td>{{ $item->transaksiPengeluaran->pengajuan->tujuan ?? '-' }}</td>
                        <td>{{ $item->transaksiPengeluaran->uraian ?? '-' }}</td>
                        <td>Rp {{ number_format($item->transaksiPengeluaran->nominal ?? 0, 0, ',', '.') }}</td>
                        <td>{{ optional($item->created_at)->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('verifikasi.show', $item->id) }}" class="btn btn-primary btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada verifikasi pengeluaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
