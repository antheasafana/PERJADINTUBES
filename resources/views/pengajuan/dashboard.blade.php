<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pegawai</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #EEF4EF;
        }

        .sidebar {
            width: 235px;
            height: 100vh;
            background: linear-gradient(180deg, #2E7D5B, #4CAF7A);
            padding: 28px 26px;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar h2 {
            font-weight: 700;
            margin-bottom: 38px;
            font-size: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 22px;
            font-size: 16px;
            font-weight: 500;
        }

        .sidebar a:hover,
        .sidebar a.active {
            opacity: .85;
            padding-left: 5px;
        }

        .content {
            margin-left: 235px;
            padding: 36px 54px;
        }

        .hero-card {
            background: white;
            border-radius: 26px;
            padding: 48px 44px;
            box-shadow: 0 12px 35px rgba(0,0,0,.05);
            margin-bottom: 28px;
        }

        .hero-card h1 {
            font-size: 44px;
            font-weight: 700;
            color: #155F52;
            margin-bottom: 10px;
        }

        .hero-card p {
            font-size: 18px;
            color: #5f6368;
            margin-bottom: 25px;
        }

        .btn-green {
            background: #2E7D5B;
            color: white;
            border-radius: 28px;
            padding: 11px 25px;
            text-decoration: none;
            display: inline-block;
            border: none;
        }

        .btn-green:hover {
            background: #24674b;
            color: white;
        }

        .stat-card {
            background: white;
            border-radius: 22px;
            padding: 25px;
            box-shadow: 0 10px 28px rgba(0,0,0,.05);
            height: 100%;
        }

        .stat-card h5 {
            color: #6c757d;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .stat-card h2 {
            color: #155F52;
            font-weight: 700;
            margin-bottom: 0;
        }

        .table-card {
            background: white;
            border-radius: 22px;
            padding: 25px;
            box-shadow: 0 10px 28px rgba(0,0,0,.05);
            margin-top: 28px;
        }

        .badge-status {
            border-radius: 20px;
            padding: 7px 12px;
            font-size: 12px;
        }

        .badge-verifikasi {
            background: #fff3cd;
            color: #856404;
        }

        .badge-pembayaran {
            background: #cff4fc;
            color: #055160;
        }

        .badge-tercatat {
            background: #d1e7dd;
            color: #0f5132;
        }

        .badge-ditolak {
            background: #f8d7da;
            color: #842029;
        }

        /* Tambahan Style untuk Banner AI */
        .ai-card {
            background: linear-gradient(135deg, #ffffff, #f4faf6);
            border: 1px solid #d0e7da;
            border-left: 6px solid #2E7D5B;
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 10px 28px rgba(46, 125, 91, 0.04);
            margin-bottom: 28px;
        }

        .ai-badge {
            background: #e1f0e8;
            color: #1b5e3c;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ai-title {
            color: #155F52;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 6px;
        }

        .ai-desc {
            font-size: 14px;
            color: #5f6368;
            margin-bottom: 20px;
        }

        .ai-pill-info {
            background: white;
            border: 1px solid #e2ece6;
            border-radius: 16px;
            padding: 16px;
            height: 100%;
            transition: all 0.3s ease;
        }

        .ai-pill-info:hover {
            border-color: #badcd0;
            transform: translateY(-2px);
        }

        .ai-pill-title {
            font-size: 12px;
            color: #8c939d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }

        .ai-pill-value {
            color: #155F52;
            font-size: 16px;
            font-weight: 600;
        }

        .ai-analysis-box {
            background: #ffffff;
            border: 1px solid #e2ece6;
            border-radius: 16px;
            padding: 20px;
            margin-top: 18px;
        }

        .ai-analysis-title {
            font-size: 13px;
            font-weight: 600;
            color: #1b5e3c;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>E- Perjadin</h2>

        <a href="{{ route('dashboard') }}" class="active">Dashboard</a>
        <a href="{{ route('pengajuan.index') }}">Pengajuan Saya</a>
        <a href="{{ route('pengajuan.create') }}">Buat Pengajuan</a>
        <a href="{{ route('realisasi.index') }}">Realisasi Dana</a>
        <a href="{{ route('pengeluaran.index') }}">Transaksi Pengeluaran</a>
    </div>

    <div class="content">

        <!-- NOTIFIKASI HASIL PROSES AI -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 px-4 mb-4 border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <span class="fs-5 me-2">🎉</span>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 px-4 mb-4 border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <span class="fs-5 me-2">⚠️</span>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="hero-card">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h1>Dashboard Pegawai</h1>
                    <p>Selamat datang di sistem perjalanan dinas.</p>
                </div>
                <div class="col-md-5 text-md-end text-start mt-3 mt-md-0">
                    <!-- TOMBOL UNTUK MEMICU REFRESH AI -->
                    <form action="{{ route('dashboard.refreshRekomendasiAi') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn text-white rounded-pill px-4 py-2 fw-semibold shadow-sm" style="background: #e5a93c; border: none; font-size: 14px;">
                            ✨ Perbarui Rekomendasi AI
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('pengajuan.create') }}" class="btn-green">
                    Buat Pengajuan
                </a>

                <a href="{{ route('realisasi.index') }}" class="btn-green ms-2">
                    Realisasi Dana
                </a>
                <a href="{{ route('pengeluaran.index') }}" class="btn-green ms-2">
                    Transaksi Pengeluaran
                </a>
            </div>
        </div>

        <!-- PANEL INFORMASI REKOMENDASI PERJALANAN AI -->
        <div class="ai-card">
            <span class="ai-badge">⚡ Generative AI Module</span>
            <h3 class="ai-title">Rekomendasi Perjalanan Pintar</h3>
            <p class="ai-desc">
                Sistem menganalisis histori perjalanan dinas Anda untuk menemukan pola rute terpopuler serta memberikan saran optimalisasi anggaran dinas yang efisien.
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="ai-pill-info">
                        <span class="ai-pill-title">Tujuan Populer Anda</span>
                        <div class="ai-pill-value">
                            {{ $rekomendasiPerjalanan->tujuan_terpopuler ?? 'Belum ada data' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="ai-pill-info" style="border-left: 4px solid #e5a93c;">
                        <span class="ai-pill-title" style="color: #c4821a;">Saran Efisiensi Anggaran</span>
                        <div class="ai-pill-value text-muted" style="font-size: 14px; font-weight: 500;">
                            {{ $rekomendasiPerjalanan->saran_efisiensi ?? 'Silakan tekan tombol "Perbarui Rekomendasi AI" untuk menganalisis anggaran Anda.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="ai-analysis-box">
                <div class="ai-analysis-title">
                    <span>💡</span> Analisis Rute & Rekomendasi Efisiensi
                </div>
                <p class="mb-0 text-secondary" style="font-size: 14px; line-height: 1.6;">
                    {{ $rekomendasiPerjalanan->analisis_rekomendasi ?? 'Analisis perjalanan dinas Anda belum diproses. Tekan tombol "Perbarui Rekomendasi AI" di kanan atas halaman untuk mulai mendeteksi rute perjalanan dinas paling hemat berdasarkan data transaksi Anda.' }}
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <h5>Pengajuan Sudah Realisasi</h5>
                    <h2>{{ $totalPengajuanRealisasi ?? 0 }}</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <h5>Total Pengeluaran</h5>
                    <h2>
                        Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                    </h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <h5>Pengeluaran Terbaru</h5>
                    <h2>{{ isset($pengeluaranTerbaru) ? $pengeluaranTerbaru->count() : 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold mb-1">Pengajuan Siap Input Pengeluaran</h4>
                    <p class="text-muted mb-0">
                        Data ini muncul jika pengajuan sudah memiliki realisasi dana.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Tujuan</th>
                            <th>Estimasi Biaya</th>
                            <th>Realisasi Dana</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pengajuanRealisasi ?? [] as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ str_replace('_', ' ', $item->jenis_pengajuan) }}
                                </td>

                                <td>
                                    {{ $item->tujuan }}
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
                                        <a href="{{ route('pengeluaran.index') }}"
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada pengajuan yang sudah direalisasi dana.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-card">
            <h4 class="fw-bold mb-3">Transaksi Pengeluaran Terbaru</h4>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengajuan</th>
                            <th>Tanggal</th>
                            <th>Uraian</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pengeluaranTerbaru ?? [] as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    #{{ $item->id_pengajuan }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $item->pengajuan->tujuan ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $item->tanggal_pengeluaran ? $item->tanggal_pengeluaran->format('d/m/Y') : '-' }}
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
                                        <span class="badge-status badge-verifikasi">{{ $item->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada transaksi pengeluaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS untuk dismissible alert -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>