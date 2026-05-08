@extends('pegawai.layout')

@section('title', 'Buat Pengajuan - Step 1')

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
                    <div class="step-item active">
                        <div class="step-circle">1</div>
                        <div class="step-label">Jenis</div>
                    </div>
                    <div class="step-item">
                        <div class="step-circle">2</div>
                        <div class="step-label">Informasi</div>
                    </div>
                    <div class="step-item">
                        <div class="step-circle">3</div>
                        <div class="step-label">Dokumen</div>
                    </div>
                    <div class="step-item">
                        <div class="step-circle">4</div>
                        <div class="step-label">Konfirmasi</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 1 Form --}}
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-1">Step 1: Pilih Jenis Pengajuan</h5>
                <p class="text-muted mb-4">Pilih jenis perjalanan dinas yang ingin Anda ajukan.</p>

                <form method="POST" action="{{ route('pegawai.pengajuan.step2') }}">
                    @csrf

                    <div class="row g-4">
                        {{-- Uang Muka --}}
                        <div class="col-md-6">
                            <div class="jenis-card border rounded-3 p-4 cursor-pointer h-100"
                                 onclick="selectJenis('UANG_MUKA')"
                                 style="cursor:pointer; transition: all 0.2s;" id="card-UANG_MUKA">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width:48px;height:48px;background:#EBF3FE">
                                        <i class="ti ti-cash fs-5" style="color:#5D87FF"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">Uang Muka</h6>
                                        <small class="text-muted">UANG_MUKA</small>
                                    </div>
                                </div>
                                <p class="text-muted mb-0 small">
                                    Pengajuan uang muka sebelum perjalanan dinas dilakukan.
                                    Anda perlu melampirkan Surat Tugas dan estimasi biaya.
                                </p>
                            </div>
                        </div>

                        {{-- Reimbursement --}}
                        <div class="col-md-6">
                            <div class="jenis-card border rounded-3 p-4 cursor-pointer h-100"
                                 onclick="selectJenis('REIMBURSEMENT')"
                                 style="cursor:pointer; transition: all 0.2s;" id="card-REIMBURSEMENT">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width:48px;height:48px;background:#FEF5E5">
                                        <i class="ti ti-receipt fs-5" style="color:#FFAE1F"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">Reimbursement</h6>
                                        <small class="text-muted">REIMBURSEMENT</small>
                                    </div>
                                </div>
                                <p class="text-muted mb-0 small">
                                    Pengajuan penggantian biaya setelah perjalanan dinas selesai.
                                    Anda perlu melampirkan LPJ, SPPD, dan dokumentasi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="jenis_pengajuan" id="jenis_pengajuan" value="">

                    @error('jenis_pengajuan')
                        <div class="text-danger mt-2 small">{{ $message }}</div>
                    @enderror

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pegawai.pengajuan.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="btn-next" disabled>
                            Selanjutnya <i class="ti ti-arrow-right ms-1"></i>
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
function selectJenis(jenis) {
    // Reset semua card
    document.querySelectorAll('.jenis-card').forEach(el => {
        el.style.borderColor = '';
        el.style.background = '';
    });

    // Highlight card yang dipilih
    const card = document.getElementById('card-' + jenis);
    card.style.borderColor = '#5D87FF';
    card.style.background = '#EBF3FE';

    // Set hidden input
    document.getElementById('jenis_pengajuan').value = jenis;

    // Enable tombol next
    document.getElementById('btn-next').disabled = false;
}
</script>
@endpush
