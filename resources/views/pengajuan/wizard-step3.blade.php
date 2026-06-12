@extends('pegawai.layout')

@section('title', 'Buat Pengajuan - Step 3')

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
                    <div class="step-item active">
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

        {{-- Step 3 Form --}}
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-1">Step 3: Upload ST</h5>
                <p class="text-muted mb-4">Upload dokumen pendukung pengajuan Anda. Format: PDF, JPG, PNG (maks. 5MB).</p>

                <form method="POST" action="{{ route('pegawai.pengajuan.step4') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Pass semua data dari step sebelumnya --}}
                    @foreach($data as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach

                    @if($data['jenis_pengajuan'] === 'UANG_MUKA')

                        {{-- Surat Tugas --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Surat Tugas <span class="text-danger">*</span>
                            </label>
                            <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center"
                                 onclick="document.getElementById('surat_tugas').click()"
                                 style="border: 2px dashed #dee2e6; cursor: pointer; transition: border-color 0.2s;"
                                 id="area-surat_tugas">
                                <i class="ti ti-cloud-upload fs-3 text-muted" id="icon-surat_tugas"></i>
                                <p class="mb-0 text-muted mt-2" id="label-surat_tugas">Klik untuk upload atau drag & drop</p>
                                <small class="text-muted">PDF, JPG, PNG - Maks. 5MB</small>
                            </div>
                            <input type="file" class="d-none @error('surat_tugas') is-invalid @enderror"
                                   name="surat_tugas" id="surat_tugas" accept=".pdf,.jpg,.jpeg,.png"
                                   onchange="previewFile(this, 'surat_tugas')">
                            @error('surat_tugas')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    @else

                        {{-- LPJ --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Laporan Pertanggungjawaban (LPJ) <span class="text-danger">*</span>
                            </label>
                            <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center"
                                 onclick="document.getElementById('lpj').click()"
                                 style="border: 2px dashed #dee2e6; cursor: pointer;"
                                 id="area-lpj">
                                <i class="ti ti-cloud-upload fs-3 text-muted" id="icon-lpj"></i>
                                <p class="mb-0 text-muted mt-2" id="label-lpj">Klik untuk upload atau drag & drop</p>
                                <small class="text-muted">PDF, JPG, PNG - Maks. 5MB</small>
                            </div>
                            <input type="file" class="d-none @error('lpj') is-invalid @enderror"
                                   name="lpj" id="lpj" accept=".pdf,.jpg,.jpeg,.png"
                                   onchange="previewFile(this, 'lpj')">
                            @error('lpj')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SPPD (opsional) --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                SPPD (Surat Perintah Perjalanan Dinas)
                                <span class="badge bg-secondary ms-1">Opsional</span>
                            </label>
                            <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center"
                                 onclick="document.getElementById('sppd').click()"
                                 style="border: 2px dashed #dee2e6; cursor: pointer;"
                                 id="area-sppd">
                                <i class="ti ti-cloud-upload fs-3 text-muted" id="icon-sppd"></i>
                                <p class="mb-0 text-muted mt-2" id="label-sppd">Klik untuk upload</p>
                                <small class="text-muted">PDF, JPG, PNG - Maks. 5MB</small>
                            </div>
                            <input type="file" class="d-none"
                                   name="sppd" id="sppd" accept=".pdf,.jpg,.jpeg,.png"
                                   onchange="previewFile(this, 'sppd')">
                        </div>

                        {{-- Dokumentasi (opsional, multiple) --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Dokumentasi Kegiatan
                                <span class="badge bg-secondary ms-1">Opsional</span>
                            </label>
                            <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center"
                                 onclick="document.getElementById('dokumentasi').click()"
                                 style="border: 2px dashed #dee2e6; cursor: pointer;"
                                 id="area-dokumentasi">
                                <i class="ti ti-photo fs-3 text-muted" id="icon-dokumentasi"></i>
                                <p class="mb-0 text-muted mt-2" id="label-dokumentasi">Klik untuk upload (bisa multiple)</p>
                                <small class="text-muted">JPG, PNG - Maks. 5MB per file</small>
                            </div>
                            <input type="file" class="d-none"
                                   name="dokumentasi[]" id="dokumentasi"
                                   accept=".jpg,.jpeg,.png" multiple
                                   onchange="previewMultiple(this, 'dokumentasi')">
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
function previewFile(input, fieldId) {
    const area  = document.getElementById('area-' + fieldId);
    const icon  = document.getElementById('icon-' + fieldId);
    const label = document.getElementById('label-' + fieldId);

    if (input.files && input.files[0]) {
        const file = input.files[0];
        area.style.borderColor  = '#13DEB9';
        area.style.background   = '#E6FFFA';
        icon.className          = 'ti ti-file-check fs-3 text-success';
        label.textContent       = '✅ ' + file.name;
        label.style.color       = '#13DEB9';
        label.style.fontWeight  = '600';
    }
}

function previewMultiple(input, fieldId) {
    const area  = document.getElementById('area-' + fieldId);
    const icon  = document.getElementById('icon-' + fieldId);
    const label = document.getElementById('label-' + fieldId);

    if (input.files && input.files.length > 0) {
        area.style.borderColor  = '#13DEB9';
        area.style.background   = '#E6FFFA';
        icon.className          = 'ti ti-photos fs-3 text-success';
        label.textContent       = '✅ ' + input.files.length + ' file dipilih';
        label.style.color       = '#13DEB9';
        label.style.fontWeight  = '600';
    }
}
</script>
@endpush
