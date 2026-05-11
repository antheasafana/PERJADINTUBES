@extends('pegawai.layout')

@section('title', 'Input Pengeluaran')

@section('content')

<div class="top-card">
    <h1 class="page-title">Input Pengeluaran</h1>
    <p class="page-subtitle">
        Input beberapa pengeluaran dari pengajuan yang sudah direalisasi dana.
    </p>
</div>

<div class="table-card mb-4">

    {{-- WIZARD --}}
    <div class="wizard-wrapper mb-4">
        <div class="wizard-step done">
            <div class="wizard-circle">1</div>
            <div class="wizard-label">Pengajuan</div>
        </div>

        <div class="wizard-line active"></div>

        <div class="wizard-step done">
            <div class="wizard-circle">2</div>
            <div class="wizard-label">Realisasi Dana</div>
        </div>

        <div class="wizard-line active"></div>

        <div class="wizard-step active">
            <div class="wizard-circle">3</div>
            <div class="wizard-label">Input Pengeluaran</div>
        </div>

        <div class="wizard-line"></div>

        <div class="wizard-step">
            <div class="wizard-circle">4</div>
            <div class="wizard-label">Verifikasi</div>
        </div>

        <div class="wizard-line"></div>

        <div class="wizard-step">
            <div class="wizard-circle">5</div>
            <div class="wizard-label">Transaksi Tercatat</div>
        </div>
    </div>

    {{-- DATA PENGAJUAN --}}
    <div class="alert alert-info rounded-4 mb-4">
        <strong>Data Pengajuan</strong><br>
        Jenis: {{ str_replace('_', ' ', $pengajuan->jenis_pengajuan) }} <br>
        Tujuan: {{ $pengajuan->tujuan }} <br>
        Estimasi Biaya: Rp {{ number_format($pengajuan->estimasi_biaya, 0, ',', '.') }} <br>
        Realisasi Dana:
        Rp {{ number_format($pengajuan->realisasiDana->total_realisasi ?? 0, 0, ',', '.') }}
    </div>

    <form action="{{ route('pengeluaran.store', $pengajuan->id_pengajuan) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">Jenis Pengeluaran</label>
                <input type="text"
                       class="form-control"
                       value="{{ str_replace('_', ' ', $pengajuan->jenis_pengajuan) }}"
                       readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label">Tanggal Pengeluaran</label>
                <input type="date"
                       name="tanggal_pengeluaran"
                       value="{{ date('Y-m-d') }}"
                       class="form-control"
                       required>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">Detail Pengeluaran</h5>
                <p class="text-muted mb-0">
                    Tambahkan beberapa pengeluaran dengan kategori biaya yang berbeda.
                </p>
            </div>

            <button type="button" class="btn btn-green" onclick="tambahPengeluaran()">
                + Tambah Pengeluaran
            </button>
        </div>

        <div id="pengeluaran-wrapper">

            <div class="pengeluaran-item border rounded-4 p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Pengeluaran #1</h6>
                    <button type="button"
                            class="btn btn-sm btn-outline-danger rounded-pill d-none btn-hapus"
                            onclick="hapusPengeluaran(this)">
                        Hapus
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kategori Biaya</label>
                        <select name="id_kategori[]" class="form-select" required>
                            <option value="">Pilih kategori</option>
                            @foreach($kategori as $item)
                                <option value="{{ $item->id_kategori }}">
                                    {{ $item->jenis_biaya }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Akun</label>
                        <select name="id_akun[]" class="form-select">
                            <option value="">Pilih akun</option>
                            @foreach($akun as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->kode_akun ?? '' }} - {{ $item->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Uraian Pengeluaran</label>
                        <input type="text"
                               name="uraian[]"
                               class="form-control"
                               placeholder="Contoh: Biaya transport, hotel, makan, dll"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nominal</label>
                        <input type="number"
                               name="nominal[]"
                               class="form-control nominal-input"
                               placeholder="0"
                               min="1"
                               required
                               oninput="hitungTotalPengeluaran()">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Upload Bukti</label>
                        <input type="file"
                               name="bukti[]"
                               class="form-control">
                        <small class="text-muted">
                            Format: PDF, JPG, PNG, DOC, DOCX. Maksimal 2MB.
                        </small>
                    </div>
                </div>
            </div>

        </div>

        <div class="alert alert-success rounded-4 mt-4">
            <div class="d-flex justify-content-between">
                <strong>Total Pengeluaran</strong>
                <strong id="total-pengeluaran">Rp 0</strong>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-green">
                Simpan Semua Pengeluaran
            </button>

            <a href="{{ route('pengeluaran.index') }}" class="btn btn-outline-green">
                Kembali
            </a>
        </div>
    </form>
</div>

@endsection

@push('styles')
<style>
    .wizard-wrapper {
        display: flex;
        align-items: center;
        width: 100%;
        overflow-x: auto;
        padding-bottom: 10px;
    }

    .wizard-step {
        text-align: center;
        min-width: 110px;
    }

    .wizard-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #d1d5db;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: 700;
    }

    .wizard-label {
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        white-space: nowrap;
    }

    .wizard-step.active .wizard-circle {
        background: #2E7D5B;
        color: white;
    }

    .wizard-step.active .wizard-label {
        color: #2E7D5B;
    }

    .wizard-step.done .wizard-circle {
        background: #13DEB9;
        color: white;
    }

    .wizard-step.done .wizard-label {
        color: #0f766e;
    }

    .wizard-line {
        height: 3px;
        min-width: 60px;
        background: #d1d5db;
        margin-bottom: 28px;
    }

    .wizard-line.active {
        background: #13DEB9;
    }

    .pengeluaran-item {
        background: #f8faf9;
    }
</style>
@endpush

@push('scripts')
<script>
    let nomorPengeluaran = 1;

    function tambahPengeluaran() {
        nomorPengeluaran++;

        const wrapper = document.getElementById('pengeluaran-wrapper');
        const itemPertama = wrapper.querySelector('.pengeluaran-item');
        const itemBaru = itemPertama.cloneNode(true);

        itemBaru.querySelector('h6').innerText = 'Pengeluaran #' + nomorPengeluaran;

        itemBaru.querySelectorAll('input').forEach(function(input) {
            input.value = '';
        });

        itemBaru.querySelectorAll('select').forEach(function(select) {
            select.selectedIndex = 0;
        });

        itemBaru.querySelector('.btn-hapus').classList.remove('d-none');

        wrapper.appendChild(itemBaru);

        hitungTotalPengeluaran();
    }

    function hapusPengeluaran(button) {
        button.closest('.pengeluaran-item').remove();
        updateNomorPengeluaran();
        hitungTotalPengeluaran();
    }

    function updateNomorPengeluaran() {
        const items = document.querySelectorAll('.pengeluaran-item');

        items.forEach(function(item, index) {
            item.querySelector('h6').innerText = 'Pengeluaran #' + (index + 1);

            const btnHapus = item.querySelector('.btn-hapus');

            if (index === 0) {
                btnHapus.classList.add('d-none');
            } else {
                btnHapus.classList.remove('d-none');
            }
        });

        nomorPengeluaran = items.length;
    }

    function hitungTotalPengeluaran() {
        let total = 0;

        document.querySelectorAll('.nominal-input').forEach(function(input) {
            total += Number(input.value || 0);
        });

        document.getElementById('total-pengeluaran').innerText = formatRupiah(total);
    }

    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }
</script>
@endpush