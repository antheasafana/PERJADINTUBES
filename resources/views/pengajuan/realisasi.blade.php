@extends('pegawai.layout')

@section('title', 'Input Realisasi Dana')

@section('content')

<style>
    .wizard-center-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #efefef;
    }

    .wizard-custom-container {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .wizard-custom-step {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .wizard-custom-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .wizard-custom-text {
        font-size: 16px;
        font-weight: 600;
        color: #64748b;
        white-space: nowrap;
    }

    .wizard-custom-step.active .wizard-custom-number {
        background: #1a4731;
        color: #fff;
    }

    .wizard-custom-step.active .wizard-custom-text {
        color: #1a4731;
        font-weight: 700;
    }

    .wizard-custom-step.done .wizard-custom-number {
        background: #52b788;
        color: #fff;
    }

    .wizard-custom-step.done .wizard-custom-text {
        color: #2d6a4f;
    }

    .wizard-custom-line {
        width: 80px;
        height: 2px;
        background: #e2e8f0;
    }

    .step-panel-custom {
        display: none;
    }

    .step-panel-custom.active {
        display: block;
    }
</style>

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: #1a4731;">💵 Realisasi Dana</h1>
            <p class="text-muted mb-0 small">Silakan lengkapi tahapan berkas perjalanan dinas Anda.</p>
        </div>
        <a href="{{ route('realisasi.index') }}" class="btn btn-sm rounded-pill px-4 text-white" style="background-color: #e07b39;">
            ← Kembali Dashboard
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm p-4" style="border-radius: 22px;">

    {{-- WIZARD --}}
    <div class="wizard-center-wrapper">
        <div class="wizard-custom-container">
            <div class="wizard-custom-step active" id="w-step-1">
                <div class="wizard-custom-number">1</div>
                <div class="wizard-custom-text">Data Pengajuan</div>
            </div>
            <div class="wizard-custom-line"></div>
            <div class="wizard-custom-step" id="w-step-2">
                <div class="wizard-custom-number">2</div>
                <div class="wizard-custom-text">Realisasi Dana</div>
            </div>
        </div>
    </div>

    <h4 class="fw-bold mb-4" style="color: #1a4731;">
        Pengajuan #{{ $pengajuan->id_pengajuan }}
    </h4>

    {{-- STEP 1 --}}
    <div class="step-panel-custom active" id="p-step-1">

        <div class="mb-3">
            <label class="form-label fw-semibold text-secondary">Jenis Pengajuan</label>
            <input type="text" class="form-control bg-light" value="{{ str_replace('_', ' ', $pengajuan->jenis_pengajuan) }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-secondary">Tujuan</label>
            <input type="text" class="form-control bg-light" value="{{ $pengajuan->tujuan }}" disabled>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-secondary">Tanggal Pengajuan</label>
            <input type="text" class="form-control bg-light"
                   value="{{ \Carbon\Carbon::parse($pengajuan->tgl_pengajuan ?? $pengajuan->created_at)->format('d/m/Y') }}"
                   disabled>
        </div>


        <div class="mb-4">
            <label class="form-label fw-semibold text-secondary">Surat Tugas (ST)</label>

            @if(!empty($pengajuan->dokumen))
                <div class="p-3 border d-flex justify-content-between align-items-center bg-white"
                     style="border-radius: 12px;">

                    <div class="d-flex align-items-center gap-3">
                        <span style="font-size: 24px;">📄</span>

                        <div>
                            <div class="fw-semibold text-dark">
                                {{ $pengajuan->dokumen }}
                            </div>
                            <small class="text-muted">Dokumen Terlampir</small>
                        </div>
                    </div>

                    <a href="{{ asset('dokumen/' . $pengajuan->dokumen) }}"
                       target="_blank"
                       class="btn btn-sm btn-outline-success rounded-pill px-3">
                        Lihat
                    </a>
                </div>
            @else
                <div class="p-3 text-center bg-light text-muted"
                     style="border-radius: 12px; border: 1px dashed #ccc;">
                    Tidak ada dokumen Surat Tugas
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-end">
            <button type="button"
                    class="btn text-white px-5"
                    style="background:#1a4731"
                    onclick="pindahStep(2)">
                Selanjutnya →
            </button>
        </div>

    </div>

    {{-- STEP 2 --}}
    <div class="step-panel-custom" id="p-step-2">

        @if(session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif

        <form action="{{ route('pengajuan.realisasi.store', $pengajuan->id_pengajuan) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Total Realisasi Dana *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light fw-semibold">Rp</span>
                    <input type="number" name="total_realisasi" class="form-control" min="1" required value="{{ old('total_realisasi') }}">
                </div>
                @error('total_realisasi')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tanggal Realisasi *</label>
                <input type="date" name="tgl_realisasi" class="form-control" required value="{{ old('tgl_realisasi') }}">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Catatan</label>
                <textarea name="catatan" class="form-control" rows="4" placeholder="Contoh: biaya transport naik karena perubahan jadwal perjalanan" style="border-radius: 12px; resize: none;">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <small class="text-muted d-block mt-1">Diisi optional. kasih tanda "-" jika tidak perlu</small>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="pindahStep(1)">
                    ← Kembali
                </button>

                <button type="submit" class="btn text-white px-4"
                        style="background: linear-gradient(135deg,#1a4731,#2d6a4f);">
                    Realisasikan
                </button>
            </div>

        </form>
    </div>

</div>

<script>
function pindahStep(step) {
    document.getElementById('p-step-1').classList.remove('active');
    document.getElementById('p-step-2').classList.remove('active');

    document.getElementById('p-step-' + step).classList.add('active');

    if(step === 1) {
        document.getElementById('w-step-1').className = 'wizard-custom-step active';
        document.getElementById('w-step-2').className = 'wizard-custom-step';
    } else {
        document.getElementById('w-step-1').className = 'wizard-custom-step done';
        document.getElementById('w-step-2').className = 'wizard-custom-step active';
    }
}
</script>

@endsection