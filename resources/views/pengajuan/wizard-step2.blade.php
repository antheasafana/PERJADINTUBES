@extends('pegawai.layout')

@section('title', 'Buat Pengajuan - Step 2')

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
                    <div class="step-item active">
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

        {{-- Info Badge Jenis --}}
        <div class="alert alert-primary d-flex align-items-center" role="alert">
            <i class="ti ti-info-circle me-2 fs-5"></i>
            <div>
                Jenis Pengajuan:
                <strong>{{ $jenis === 'UANG_MUKA' ? 'Uang Muka' : 'Reimbursement' }}</strong>
            </div>
        </div>

        {{-- Step 2 Form --}}
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-1">Step 2: Informasi Perjalanan Dinas</h5>
                <p class="text-muted mb-4">Isi detail informasi perjalanan dinas Anda.</p>

                <form method="POST" action="{{ route('pegawai.pengajuan.step3') }}">
                    @csrf
                    <input type="hidden" name="jenis_pengajuan" value="{{ $jenis }}">

                    {{-- Tujuan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Tujuan Perjalanan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('tujuan') is-invalid @enderror"
                               name="tujuan" value="{{ old('tujuan') }}"
                               placeholder="Contoh: Jakarta - Rapat Koordinasi Nasional">
                        @error('tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Tanggal Berangkat <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('tgl_berangkat') is-invalid @enderror"
                                   name="tgl_berangkat" value="{{ old('tgl_berangkat') }}"
                                   min="{{ date('Y-m-d') }}" id="tgl_berangkat">
                            @error('tgl_berangkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Tanggal Kembali <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('tgl_kembali') is-invalid @enderror"
                                   name="tgl_kembali" value="{{ old('tgl_kembali') }}"
                                   id="tgl_kembali">
                            @error('tgl_kembali')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Estimasi Biaya (hanya UANG_MUKA) --}}
                    @if($jenis === 'UANG_MUKA')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Estimasi Biaya <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('estimasi_biaya') is-invalid @enderror"
                                   name="estimasi_biaya" value="{{ old('estimasi_biaya') }}"
                                   placeholder="0" min="1">
                            @error('estimasi_biaya')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">Masukkan perkiraan biaya perjalanan dinas.</small>
                    </div>
                    @endif

                    {{-- Referensi Uang Muka (hanya REIMBURSEMENT) --}}
                    @if($jenis === 'REIMBURSEMENT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Referensi Uang Muka</label>
                        <select class="form-select @error('id_pengajuan_parent') is-invalid @enderror"
                                name="id_pengajuan_parent">
                            <option value="">-- Tidak ada referensi uang muka --</option>
                            @foreach($uangMukaList as $um)
                                <option value="{{ $um->id_pengajuan }}" {{ old('id_pengajuan_parent') == $um->id_pengajuan ? 'selected' : '' }}>
                                    #{{ $um->id_pengajuan }} - {{ $um->tujuan }}
                                    (Rp {{ number_format($um->estimasi_biaya, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_pengajuan_parent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pilih jika reimbursement ini berkaitan dengan uang muka sebelumnya.</small>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pegawai.pengajuan.create') }}" class="btn btn-light">
                            <i class="ti ti-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
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
// Pastikan tgl_kembali >= tgl_berangkat
document.getElementById('tgl_berangkat').addEventListener('change', function() {
    document.getElementById('tgl_kembali').min = this.value;
});
</script>
@endpush
