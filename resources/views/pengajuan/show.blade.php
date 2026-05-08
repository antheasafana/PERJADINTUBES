@extends('pegawai.layout')

@section('title', 'Detail Pengajuan #' . $pengajuan->id_pengajuan)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Detail Pengajuan #{{ $pengajuan->id_pengajuan }}</h4>
            <a href="{{ route('pegawai.pengajuan.index') }}" class="btn btn-light btn-sm">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Status Banner --}}
        @php
            $statusConfig = match($pengajuan->status) {
                'Approved' => ['bg' => '#E6FFFA', 'color' => '#13DEB9', 'icon' => 'ti-circle-check', 'text' => 'Pengajuan Anda telah disetujui oleh admin.'],
                'Rejected' => ['bg' => '#FDEDE8', 'color' => '#FA896B', 'icon' => 'ti-circle-x',    'text' => 'Pengajuan Anda ditolak. Silakan buat pengajuan baru.'],
                default    => ['bg' => '#FEF5E5', 'color' => '#FFAE1F', 'icon' => 'ti-clock',        'text' => 'Pengajuan Anda sedang menunggu persetujuan admin.'],
            };
        @endphp
        <div class="card mb-4" style="background: {{ $statusConfig['bg'] }}; border: 1.5px solid {{ $statusConfig['color'] }};">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <i class="ti {{ $statusConfig['icon'] }} fs-3" style="color: {{ $statusConfig['color'] }}"></i>
                <div>
                    <h6 class="mb-0 fw-semibold" style="color: {{ $statusConfig['color'] }}">Status: {{ $pengajuan->status }}</h6>
                    <small class="text-muted">{{ $statusConfig['text'] }}</small>
                </div>
            </div>
        </div>

        {{-- Detail Pengajuan --}}
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-4">Informasi Pengajuan</h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Jenis Pengajuan</p>
                        <span class="badge fs-6 {{ $pengajuan->jenis_pengajuan === 'UANG_MUKA' ? 'bg-primary' : 'bg-warning text-dark' }}">
                            {{ $pengajuan->jenis_pengajuan === 'UANG_MUKA' ? 'Uang Muka' : 'Reimbursement' }}
                        </span>
                    </div>

                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Tujuan Perjalanan</p>
                        <p class="fw-semibold mb-0">{{ $pengajuan->tujuan }}</p>
                    </div>

                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Tanggal Berangkat</p>
                        <p class="fw-semibold mb-0">
                            <i class="ti ti-calendar me-1 text-primary"></i>
                            {{ \Carbon\Carbon::parse($pengajuan->tgl_berangkat)->format('d M Y') }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Tanggal Kembali</p>
                        <p class="fw-semibold mb-0">
                            <i class="ti ti-calendar me-1 text-primary"></i>
                            {{ \Carbon\Carbon::parse($pengajuan->tgl_kembali)->format('d M Y') }}
                        </p>
                    </div>

                    @if($pengajuan->estimasi_biaya)
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Estimasi Biaya</p>
                        <p class="fw-semibold mb-0 text-success fs-5">
                            Rp {{ number_format($pengajuan->estimasi_biaya, 0, ',', '.') }}
                        </p>
                    </div>
                    @endif

                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Tanggal Dibuat</p>
                        <p class="fw-semibold mb-0">{{ $pengajuan->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Dokumen --}}
                <h6 class="fw-semibold mb-3">Dokumen Terlampir</h6>
                @php $dokumen = $pengajuan->dokumen ?? []; @endphp

                @if(empty($dokumen))
                    <p class="text-muted">Tidak ada dokumen terlampir.</p>
                @else
                    <div class="d-flex flex-column gap-3">
                        @if(!empty($dokumen['surat_tugas']))
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-file-text fs-5 text-primary"></i>
                                    <div>
                                        <p class="mb-0 fw-medium">Surat Tugas</p>
                                        <small class="text-muted">{{ basename($dokumen['surat_tugas']) }}</small>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $dokumen['surat_tugas']) }}"
                                   target="_blank" class="btn btn-sm btn-primary">
                                    <i class="ti ti-download me-1"></i>Lihat
                                </a>
                            </div>
                        @endif

                        @if(!empty($dokumen['lpj']))
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-file-description fs-5 text-warning"></i>
                                    <div>
                                        <p class="mb-0 fw-medium">LPJ (Laporan Pertanggungjawaban)</p>
                                        <small class="text-muted">{{ basename($dokumen['lpj']) }}</small>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $dokumen['lpj']) }}"
                                   target="_blank" class="btn btn-sm btn-primary">
                                    <i class="ti ti-download me-1"></i>Lihat
                                </a>
                            </div>
                        @endif

                        @if(!empty($dokumen['sppd']))
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-file-invoice fs-5 text-success"></i>
                                    <div>
                                        <p class="mb-0 fw-medium">SPPD</p>
                                        <small class="text-muted">{{ basename($dokumen['sppd']) }}</small>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $dokumen['sppd']) }}"
                                   target="_blank" class="btn btn-sm btn-primary">
                                    <i class="ti ti-download me-1"></i>Lihat
                                </a>
                            </div>
                        @endif

                        @if(!empty($dokumen['dokumentasi']))
                            <div class="p-3 bg-light rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="ti ti-photos fs-5 text-info"></i>
                                    <p class="mb-0 fw-medium">Dokumentasi Kegiatan ({{ count($dokumen['dokumentasi']) }} file)</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($dokumen['dokumentasi'] as $i => $doc)
                                        <a href="{{ asset('storage/' . $doc) }}" target="_blank"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="ti ti-photo me-1"></i>Foto {{ $i + 1 }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
