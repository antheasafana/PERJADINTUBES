@extends('pegawai.layout')

@section('title', 'Buat Pengajuan - Step 4')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="mb-0">Buat Pengajuan Baru</h4>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Wizard Progress --}}
        <div class="card mb-4">
            <div class="card-body py-4">
                <div class="wizard-step">
                    <div class="step-item done">
                        <div class="step-circle"><i class="ti ti-check"></i></div>
                        <div class="step-label">Jenis</div>
                    </div>
                    <div class="step-item done">
                        <div class="step-circle"><i class="ti ti-check"></i></div>
                        <div class="step-label">Informasi</div>
                    </div>
                    <div class="step-item done">
                        <div class="step-circle"><i class="ti ti-check"></i></div>
                        <div class="step-label">Dokumen</div>
                    </div>
                    <div class="step-item active">
                        <div class="step-circle">4</div>
                        <div class="step-label">Konfirmasi</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Review Card --}}
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-1">Step 4: Review & Konfirmasi</h5>
                <p class="text-muted mb-4">Periksa kembali semua data sebelum mengirimkan pengajuan.</p>

                {{-- Info Pengajuan --}}
                <div class="bg-light rounded-3 p-4 mb-4">
                    <h6 class="fw-semibold text-primary mb-3">
                        <i class="ti ti-file-description me-2"></i>Detail Pengajuan
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Jenis Pengajuan</p>
                            <p class="mb-0 fw-semibold">
                                <span class="badge {{ $data['jenis_pengajuan'] === 'UANG_MUKA' ? 'bg-primary' : 'bg-warning text-dark' }} fs-6">
                                    {{ $data['jenis_pengajuan'] === 'UANG_MUKA' ? 'Uang Muka' : 'Reimbursement' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Tujuan</p>
                            <p class="mb-0 fw-semibold">{{ $data['tujuan'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Tanggal Berangkat</p>
                            <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($data['tgl_berangkat'])->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Tanggal Kembali</p>
                            <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($data['tgl_kembali'])->format('d M Y') }}</p>
                        </div>
                        @if(!empty($data['estimasi_biaya']))
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Estimasi Biaya</p>
                            <p class="mb-0 fw-semibold text-success">
                                Rp {{ number_format($data['estimasi_biaya'], 0, ',', '.') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Dokumen terupload --}}
                <div class="bg-light rounded-3 p-4 mb-4">
                    <h6 class="fw-semibold text-primary mb-3">
                        <i class="ti ti-paperclip me-2"></i>Dokumen Terupload
                    </h6>
                    <div class="d-flex flex-column gap-2">
                        @if(!empty($data['dokumen']['surat_tugas']))
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-file-check text-success fs-5"></i>
                                <span>Surat Tugas</span>
                                <span class="badge bg-success-subtle text-success">✓ Terupload</span>
                            </div>
                        @endif
                        @if(!empty($data['dokumen']['lpj']))
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-file-check text-success fs-5"></i>
                                <span>Laporan Pertanggungjawaban (LPJ)</span>
                                <span class="badge bg-success-subtle text-success">✓ Terupload</span>
                            </div>
                        @endif
                        @if(!empty($data['dokumen']['sppd']))
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-file-check text-success fs-5"></i>
                                <span>SPPD</span>
                                <span class="badge bg-success-subtle text-success">✓ Terupload</span>
                            </div>
                        @endif
                        @if(!empty($data['dokumen']['dokumentasi']))
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-photos text-success fs-5"></i>
                                <span>Dokumentasi Kegiatan</span>
                                <span class="badge bg-success-subtle text-success">✓ {{ count($data['dokumen']['dokumentasi']) }} file</span>
                            </div>
                        @endif
                        @if(empty($data['dokumen']))
                            <p class="text-muted mb-0">Tidak ada dokumen.</p>
                        @endif
                    </div>
                </div>

                {{-- Info Email --}}
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="ti ti-mail me-2 fs-5"></i>
                    <div>
                        Setelah submit, notifikasi email akan dikirim ke akun Anda dan ke admin melalui <strong>Mailtrap</strong>.
                    </div>
                </div>

                {{-- Tombol --}}
                <form method="POST" action="{{ route('pegawai.pengajuan.store') }}">
                    @csrf
                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('pegawai.pengajuan.create') }}" class="btn btn-light">
                            <i class="ti ti-arrow-left me-1"></i> Mulai Ulang
                        </a>
                        <button type="submit" class="btn btn-success px-5" id="btn-submit">
                            <i class="ti ti-send me-1"></i> Submit Pengajuan
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...';
});
</script>
@endpush
