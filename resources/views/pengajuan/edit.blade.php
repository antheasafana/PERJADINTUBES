<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengajuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f7f4;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 16px;
        }
        .card-header {
            background-color: #2d6a4f;
            color: white;
            border-radius: 16px 16px 0 0 !important;
            padding: 20px 24px;
        }
        .btn-simpan {
            background-color: #2d6a4f;
            border-color: #2d6a4f;
            color: white;
            border-radius: 20px;
            padding: 8px 28px;
        }
        .btn-simpan:hover {
            background-color: #1b4332;
            border-color: #1b4332;
            color: white;
        }
        .btn-kembali {
            background-color: #f4f4f4;
            border-color: #ddd;
            color: #555;
            border-radius: 20px;
            padding: 8px 28px;
        }
        .btn-kembali:hover {
            background-color: #e0e0e0;
        }
        .form-control:focus {
            border-color: #52b788;
            box-shadow: 0 0 0 0.2rem rgba(82,183,136,0.25);
        }
        .form-label {
            font-weight: 600;
            color: #1b4332;
        }
        h2 {
            color: #1b4332;
            font-weight: 700;
        }
    </style>
</head>

<body>

<div class="container mt-5" style="max-width: 700px;">

    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('pengajuan.index') }}" class="btn btn-kembali">
            ← Kembali
        </a>
        <h2 class="mb-0">✏️ Edit Pengajuan</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">📋 Data Pengajuan #{{ $pengajuan->id_pengajuan }}</h5>
        </div>
        <div class="card-body p-4">

            <form action="{{ route('pengajuan.update', $pengajuan->id_pengajuan) }}"
                  method="POST">
                @csrf
                @method('PUT')

                {{-- JENIS PENGAJUAN (readonly) --}}
                <div class="mb-3">
                    <label class="form-label">Jenis Pengajuan</label>
                    <input type="text"
                           class="form-control bg-light"
                           value="{{ $pengajuan->jenis_pengajuan }}"
                           readonly>
                    <small class="text-muted">Jenis pengajuan tidak dapat diubah.</small>
                </div>

                {{-- TUJUAN --}}
                <div class="mb-3">
                    <label class="form-label">Tujuan Perjalanan</label>
                    <input type="text"
                           class="form-control"
                           name="tujuan"
                           value="{{ old('tujuan', $pengajuan->tujuan) }}"
                           required>
                </div>

                {{-- TANGGAL BERANGKAT --}}
                <div class="mb-3">
                    <label class="form-label">Tanggal Berangkat</label>
                    <input type="date"
                           class="form-control"
                           name="tgl_berangkat"
                           value="{{ old('tgl_berangkat', $pengajuan->tgl_berangkat) }}"
                           required>
                </div>

                {{-- TANGGAL KEMBALI --}}
                <div class="mb-3">
                    <label class="form-label">Tanggal Kembali</label>
                    <input type="date"
                           class="form-control"
                           name="tgl_kembali"
                           value="{{ old('tgl_kembali', $pengajuan->tgl_kembali) }}"
                           required>
                </div>

                {{-- ESTIMASI BIAYA --}}
                <div class="mb-4">
                    <label class="form-label">Estimasi Biaya</label>
                    <div class="input-group">
                        <span class="input-group-text"
                              style="background:#d8f3dc; border-color:#52b788; color:#1b4332; font-weight:600;">
                            Rp
                        </span>
                        <input type="number"
                               class="form-control"
                               name="estimasi_biaya"
                               value="{{ old('estimasi_biaya', $pengajuan->estimasi_biaya) }}">
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-simpan">
                        💾 Simpan Perubahan
                    </button>
                    <a href="{{ route('pengajuan.index') }}" class="btn btn-kembali">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>