@extends('pegawai.layout')

@section('title', 'Transaksi Pengeluaran')

@section('content')

<div class="top-card">
    <h1 class="page-title">Transaksi Pengeluaran</h1>
    <p class="text-muted mb-0">
        Pilih pengajuan yang sudah direalisasi dana untuk input pengeluaran.
    </p>
</div>

<div class="table-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Pengajuan Sudah Realisasi Dana</h4>
            <p class="text-muted mb-0">
                Data di bawah ini berasal dari pengajuan yang sudah masuk tahap realisasi dana.
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Pengajuan</th>
                    <th>Tujuan</th>
                    <th>Tanggal Berangkat</th>
                    <th>Tanggal Kembali</th>
                    <th>Estimasi Biaya</th>
                    <th>Realisasi Dana</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pengajuanRealisasi as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ str_replace('_', ' ', $item->jenis_pengajuan) }}
                        </td>

                        <td>
                            {{ $item->tujuan }}
                        </td>

                        <td>
                            {{ $item->tgl_berangkat }}
                        </td>

                        <td>
                            {{ $item->tgl_kembali }}
                        </td>

                        <td>
                            Rp {{ number_format($item->estimasi_biaya, 0, ',', '.') }}
                        </td>

                        <td>
                            Rp {{ number_format($item->realisasiDana->total_realisasi ?? 0, 0, ',', '.') }}
                        </td>

                        <td>
                            @if($item->transaksiPengeluaran->count() > 0)
                                <span class="badge-status badge-tercatat">
                                    Sudah Input Pengeluaran
                                </span>
                            @else
                                <span class="badge-status badge-verifikasi">
                                    Siap Input Pengeluaran
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($item->transaksiPengeluaran->count() > 0)
                                <a href="#daftar-pengeluaran"
                                   class="btn btn-sm btn-outline-success rounded-pill">
                                    Lihat Pengeluaran
                                </a>
                            @else
                                <a href="{{ route('pengeluaran.create', $item->id_pengajuan) }}"
                                   class="btn btn-sm btn-success rounded-pill">
                                    Input Pengeluaran
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Belum ada pengajuan yang sudah direalisasi dana.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="table-card" id="daftar-pengeluaran">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Daftar Transaksi Pengeluaran</h4>
            <p class="text-muted mb-0">
                Data pengeluaran yang sudah diinput oleh pegawai.
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pengajuan</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Uraian</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pengeluaran as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            #{{ $item->id_pengajuan }} <br>
                            <small class="text-muted">
                                {{ $item->pengajuan->tujuan ?? '-' }}
                            </small>
                        </td>

                        <td>
                            {{ $item->tanggal_pengeluaran ? $item->tanggal_pengeluaran->format('d/m/Y') : '-' }}
                        </td>

                        <td>
                            {{ str_replace('_', ' ', $item->jenis_pengeluaran) }}
                        </td>

                        <td>
                            {{ $item->uraian }}
                        </td>

                        <td>
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>

                        <td>
                            @if($item->status == 'verifikasi_pengeluaran')
                                <span class="badge-status badge-verifikasi">Verifikasi</span>
                            @elseif($item->status == 'pembayaran')
                                <span class="badge-status badge-pembayaran">Pembayaran</span>
                            @elseif($item->status == 'transaksi_tercatat')
                                <span class="badge-status badge-tercatat">Tercatat</span>
                            @elseif($item->status == 'ditolak')
                                <span class="badge-status badge-ditolak">Ditolak</span>
                            @else
                                <span class="badge bg-secondary">
                                    {{ $item->status }}
                                </span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('pengeluaran.show', $item->id_transaksi_pengeluaran) }}"
                               class="btn btn-sm btn-outline-success rounded-pill">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Belum ada transaksi pengeluaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection