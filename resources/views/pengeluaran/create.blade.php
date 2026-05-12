@extends('pegawai.layout')

@section('title', 'Input Pengeluaran')

@section('content')

<div class="top-card">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

        <div>
            <h1 class="page-title">
                Input Pengeluaran
            </h1>

            <p class="page-subtitle">
                Input beberapa pengeluaran dari pengajuan yang sudah direalisasi dana.
            </p>
        </div>

        {{-- TOMBOL KEMBALI DASHBOARD --}}
        <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
            ← Kembali Dashboard
        </a>

    </div>
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
            <div class="wizard-label">Verifikasi Pengeluaran</div>
        </div>

        <div class="wizard-line"></div>

        <div class="wizard-step">
            <div class="wizard-circle">5</div>
            <div class="wizard-label">Pembayaran</div>
        </div>

        <div class="wizard-line"></div>

        <div class="wizard-step">
            <div class="wizard-circle">6</div>
            <div class="wizard-label">Transaksi Tercatat</div>
        </div>

    </div>

    {{-- INFO PENGAJUAN --}}
    <div class="alert alert-info rounded-4 mb-4">

        <strong>Data Pengajuan</strong>

        <hr>

        <div class="row">

            <div class="col-md-6 mb-2">
                <strong>Jenis Pengajuan:</strong><br>
                {{ str_replace('_', ' ', $pengajuan->jenis_pengajuan) }}
            </div>

            <div class="col-md-6 mb-2">
                <strong>Tujuan:</strong><br>
                {{ $pengajuan->tujuan }}
            </div>

            <div class="col-md-6 mb-2">
                <strong>Estimasi Biaya:</strong><br>
                Rp {{ number_format($pengajuan->estimasi_biaya, 0, ',', '.') }}
            </div>

            <div class="col-md-6 mb-2">
                <strong>Realisasi Dana:</strong><br>

                Rp {{ number_format(
                    $pengajuan->realisasiDana->total_realisasi ?? 0,
                    0,
                    ',',
                    '.'
                ) }}
            </div>

        </div>

    </div>

    <form action="{{ route('pengeluaran.store', $pengajuan->id_pengajuan) }}"
          method="POST"
          enctype="multipart/form-data"
          id="form-pengeluaran">

        @csrf

        {{-- TANGGAL --}}
        <div class="row mb-4">

            <div class="col-md-6">

                <label class="form-label">
                    Jenis Pengeluaran
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ str_replace('_', ' ', $pengajuan->jenis_pengajuan) }}"
                       readonly>

            </div>

            <div class="col-md-6">

                <label class="form-label">
                    Tanggal Pengeluaran
                </label>

                <input type="date"
                       name="tanggal_pengeluaran"
                       value="{{ date('Y-m-d') }}"
                       class="form-control"
                       required>

            </div>

        </div>

        {{-- HEADER DETAIL --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h5 class="fw-bold mb-1">
                    Detail Pengeluaran
                </h5>

                <p class="text-muted mb-0">
                    Tambahkan beberapa pengeluaran dengan kategori biaya berbeda.
                </p>

            </div>

            <button type="button"
                    class="btn btn-green"
                    onclick="tambahPengeluaran()">

                + Tambah Pengeluaran

            </button>

        </div>

        {{-- WRAPPER --}}
        <div id="pengeluaran-wrapper">

            {{-- ITEM --}}
            <div class="pengeluaran-item border rounded-4 p-3 mb-3">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h6 class="fw-bold mb-0">
                        Pengeluaran #1
                    </h6>

                    <button type="button"
                            class="btn btn-sm btn-outline-danger rounded-pill d-none btn-hapus"
                            onclick="hapusPengeluaran(this)">

                        Hapus

                    </button>

                </div>

                <div class="row g-3">

                    {{-- KATEGORI --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Kategori Biaya
                        </label>

                        <select name="id_kategori[]"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih kategori
                            </option>

                            @foreach($kategori as $item)

                                <option value="{{ $item->id_kategori }}">
                                    {{ $item->jenis_biaya }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- AKUN --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Akun
                        </label>

                        <select name="id_akun[]"
                                class="form-select">

                            <option value="">
                                Pilih akun
                            </option>

                            @foreach($akun as $item)

                                <option value="{{ $item->id }}">

                                    {{ $item->kode_akun ?? '' }}
                                    -
                                    {{ $item->nama_akun }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- URAIAN --}}
                    <div class="col-md-8">

                        <label class="form-label">
                            Uraian Pengeluaran
                        </label>

                        <input type="text"
                               name="uraian[]"
                               class="form-control"
                               placeholder="Contoh: Hotel, makan, transportasi"
                               required>

                    </div>

                    {{-- NOMINAL --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Nominal
                        </label>

                        <input type="number"
                               name="nominal[]"
                               class="form-control nominal-input"
                               min="1"
                               placeholder="0"
                               required
                               oninput="hitungTotalPengeluaran()">

                    </div>

                    {{-- BUKTI --}}
                    <div class="col-md-12">

                        <label class="form-label">
                            Upload Bukti
                        </label>

                        <input type="file"
                               name="bukti[]"
                               class="form-control">

                        <small class="text-muted">
                            PDF, JPG, PNG, DOC, DOCX maksimal 2MB
                        </small>

                    </div>

                </div>

            </div>

        </div>

        {{-- TOTAL --}}
        <div class="alert alert-success rounded-4 mt-4">

            <div class="d-flex justify-content-between">

                <strong>Total Pengeluaran</strong>

                <strong id="total-pengeluaran">
                    Rp 0
                </strong>

            </div>

            <hr>

            <div class="d-flex justify-content-between">

                <span>Sisa Dana</span>

                <strong id="sisa-dana">

                    Rp {{ number_format(
                        $pengajuan->realisasiDana->total_realisasi ?? 0,
                        0,
                        ',',
                        '.'
                    ) }}

                </strong>

            </div>

        </div>

        {{-- BUTTON --}}
        <div class="d-flex gap-2 mt-4">

            <button type="submit"
                    class="btn btn-green">

                Simpan Semua Pengeluaran

            </button>

            <a href="{{ route('pengeluaran.index') }}"
               class="btn btn-outline-green">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection

@push('styles')

<style>

.wizard-wrapper{
    display:flex;
    align-items:center;
    overflow-x:auto;
    padding-bottom:10px;
}

.wizard-step{
    text-align:center;
    min-width:120px;
}

.wizard-circle{
    width:42px;
    height:42px;
    border-radius:50%;
    background:#d1d5db;
    color:#6b7280;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 8px;
    font-weight:700;
}

.wizard-label{
    font-size:13px;
    font-weight:600;
    color:#6b7280;
    white-space:nowrap;
}

.wizard-step.active .wizard-circle{
    background:#2E7D5B;
    color:white;
}

.wizard-step.active .wizard-label{
    color:#2E7D5B;
}

.wizard-step.done .wizard-circle{
    background:#13DEB9;
    color:white;
}

.wizard-step.done .wizard-label{
    color:#0f766e;
}

.wizard-line{
    height:3px;
    min-width:60px;
    background:#d1d5db;
    margin-bottom:28px;
}

.wizard-line.active{
    background:#13DEB9;
}

.pengeluaran-item{
    background:#f8faf9;
}

</style>

@endpush

@push('scripts')

<script>

let nomorPengeluaran = 1;

const totalRealisasi =
    {{ $pengajuan->realisasiDana->total_realisasi ?? 0 }};

function tambahPengeluaran()
{
    nomorPengeluaran++;

    const wrapper =
        document.getElementById('pengeluaran-wrapper');

    const pertama =
        wrapper.querySelector('.pengeluaran-item');

    const baru =
        pertama.cloneNode(true);

    baru.querySelector('h6').innerText =
        'Pengeluaran #' + nomorPengeluaran;

    baru.querySelectorAll('input').forEach(function(input){

        if(input.type !== 'file'){
            input.value = '';
        }

        if(input.type === 'file'){
            input.value = null;
        }

    });

    baru.querySelectorAll('select').forEach(function(select){
        select.selectedIndex = 0;
    });

    baru.querySelector('.btn-hapus')
        .classList.remove('d-none');

    wrapper.appendChild(baru);

    hitungTotalPengeluaran();
}

function hapusPengeluaran(button)
{
    button.closest('.pengeluaran-item').remove();

    updateNomor();

    hitungTotalPengeluaran();
}

function updateNomor()
{
    const items =
        document.querySelectorAll('.pengeluaran-item');

    items.forEach(function(item,index){

        item.querySelector('h6').innerText =
            'Pengeluaran #' + (index + 1);

        const btn =
            item.querySelector('.btn-hapus');

        if(index === 0){
            btn.classList.add('d-none');
        }else{
            btn.classList.remove('d-none');
        }

    });

    nomorPengeluaran = items.length;
}

function hitungTotalPengeluaran()
{
    let total = 0;

    document.querySelectorAll('.nominal-input')
        .forEach(function(input){

        total += Number(input.value || 0);

    });

    document.getElementById('total-pengeluaran')
        .innerText = formatRupiah(total);

    const sisa =
        totalRealisasi - total;

    document.getElementById('sisa-dana')
        .innerText = formatRupiah(sisa);

    if(sisa < 0){

        document.getElementById('sisa-dana')
            .classList.add('text-danger');

    }else{

        document.getElementById('sisa-dana')
            .classList.remove('text-danger');

    }
}

function formatRupiah(angka)
{
    return 'Rp ' + angka.toLocaleString('id-ID');
}

document.getElementById('form-pengeluaran')
.addEventListener('submit', function(e){

    let total = 0;

    document.querySelectorAll('.nominal-input')
        .forEach(function(input){

        total += Number(input.value || 0);

    });

    if(total <= 0){

        e.preventDefault();

        alert('Total pengeluaran harus lebih dari 0');

        return;
    }

});

</script>

@endpush