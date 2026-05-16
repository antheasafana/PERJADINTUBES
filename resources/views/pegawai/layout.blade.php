<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'E-Perjadin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- BOOTSTRAP CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- FONT POPPINS --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #EEF4EF;
            color: #1f2937;
        }

        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 245px;
            min-height: 100vh;
            background: linear-gradient(180deg, #2E7D5B, #4CAF7A);
            padding: 30px 24px;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
        }

        .sidebar-logo {
            font-weight: 700;
            font-size: 30px;
            margin-bottom: 38px;
            color: white;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-menu a {
            display: block;
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            padding: 11px 14px;
            border-radius: 14px;
            transition: 0.2s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.20);
            color: white;
            transform: translateX(3px);
        }

        .sidebar-footer {
            margin-top: 40px;
        }

        .logout-btn {
            background: white;
            color: #2E7D5B;
            border: none;
            border-radius: 22px;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .logout-btn:hover {
            background: #f1f5f3;
            color: #24674b;
        }

        /* CONTENT */
        .content {
            margin-left: 245px;
            width: calc(100% - 245px);
            padding: 36px 50px;
        }

        /* CARD */
        .top-card {
            background: white;
            border-radius: 26px;
            padding: 34px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .table-card {
            background: white;
            border-radius: 22px;
            padding: 25px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 22px;
            padding: 25px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.05);
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

        .page-title {
            color: #155F52;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .page-subtitle {
            color: #6b7280;
            margin-bottom: 0;
        }

        /* BUTTON */
        .btn-green {
            background: #2E7D5B;
            color: white;
            border: none;
            border-radius: 24px;
            padding: 9px 22px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
        }

        .btn-green:hover {
            background: #24674b;
            color: white;
        }

        .btn-outline-green {
            background: transparent;
            color: #2E7D5B;
            border: 1px solid #2E7D5B;
            border-radius: 24px;
            padding: 8px 20px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
        }

        .btn-outline-green:hover {
            background: #2E7D5B;
            color: white;
        }

        /* TABLE */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 12px;
            white-space: nowrap;
        }

        .table tbody td {
            font-size: 14px;
            padding: 14px 12px;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background: #f8faf9;
        }

        /* BADGE STATUS */
        .badge-status {
            border-radius: 20px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
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

        .badge-realisasi {
            background: #d1e7dd;
            color: #0f5132;
        }

        .badge-belum {
            background: #fff3cd;
            color: #856404;
        }

        /* FORM */
        .form-control,
        .form-select {
            border-radius: 14px;
            padding: 10px 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2E7D5B;
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 91, 0.15);
        }

        label.form-label {
            font-weight: 600;
            color: #374151;
        }

        /* ALERT */
        .alert {
            border: none;
            border-radius: 18px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .app-wrapper {
                display: block;
            }

            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
                padding: 24px;
            }

            .sidebar-logo {
                font-size: 26px;
                margin-bottom: 20px;
            }

            .content {
                margin-left: 0;
                width: 100%;
                padding: 24px;
            }

            .top-card {
                padding: 25px;
            }

            .table-card {
                padding: 20px;
            }
        }
    </style>

    <style>
        @stack('styles')
    </style>
</head>

<body>

<div class="app-wrapper">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            E- Perjadin
        </div>

        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('pengajuan.index') }}"
               class="{{ request()->routeIs('pengajuan.index') || request()->routeIs('pengajuan.view') || request()->routeIs('pengajuan.edit') ? 'active' : '' }}">
                Pengajuan Saya
            </a>

            <a href="{{ route('pengajuan.create') }}"
               class="{{ request()->routeIs('pengajuan.create') ? 'active' : '' }}">
                Buat Pengajuan
            </a>

            <a href="{{ route('realisasi.index') }}"
               class="{{ request()->routeIs('realisasi.*') || request()->routeIs('pengajuan.realisasi*') ? 'active' : '' }}">
                Realisasi Dana
            </a>

            <a href="{{ route('pengeluaran.index') }}"
               class="{{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">
                Transaksi Pengeluaran
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- CONTENT --}}
    <main class="content">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>