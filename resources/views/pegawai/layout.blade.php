<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - PERJADINTUBES</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}" />
    <style>
        .wizard-step {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
        }
        .wizard-step .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
        }
        .wizard-step .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 18px;
            left: calc(50% + 20px);
            right: calc(-50% + 20px);
            height: 2px;
            background: #dee2e6;
            z-index: 0;
        }
        .wizard-step .step-item.active:not(:last-child)::after,
        .wizard-step .step-item.done:not(:last-child)::after {
            background: #5D87FF;
        }
        .step-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            border: 2px solid #dee2e6;
            background: white;
            color: #aaa;
            z-index: 1;
            position: relative;
        }
        .step-item.active .step-circle {
            background: #5D87FF;
            border-color: #5D87FF;
            color: white;
        }
        .step-item.done .step-circle {
            background: #13DEB9;
            border-color: #13DEB9;
            color: white;
        }
        .step-label {
            font-size: 12px;
            margin-top: 6px;
            color: #aaa;
            font-weight: 500;
            text-align: center;
        }
        .step-item.active .step-label,
        .step-item.done .step-label {
            color: #5D87FF;
        }
        .badge-status-diajukan  { background-color: #FFAE1F; color: white; }
        .badge-status-approved  { background-color: #13DEB9; color: white; }
        .badge-status-rejected  { background-color: #FA896B; color: white; }
        .sidebar-nav .nav-link.active {
            background: rgba(93,135,255,.1);
            color: #5D87FF !important;
        }
    </style>
</head>

<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    {{-- SIDEBAR --}}
    <aside class="left-sidebar">
        <div>
            <div class="brand-logo d-flex align-items-center justify-content-between">
                <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
                    <img src="{{ asset('images/logos/mukena.PNG') }}" width="150" alt="logo">
                </a>
                <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                    <i class="ti ti-x fs-8"></i>
                </div>
            </div>

            <nav class="sidebar-nav scroll-sidebar" data-simplebar>
                <ul id="sidebarnav">
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">MENU PEGAWAI</span>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}" aria-expanded="false">
                            <span><i class="ti ti-layout-dashboard"></i></span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('pengajuan.*') ? 'active' : '' }}"
                           href="{{ route('pengajuan.index') }}" aria-expanded="false">
                            <span><i class="ti ti-file-text"></i></span>
                            <span class="hide-menu">Pengajuan Saya</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('pengajuan.create') ? 'active' : '' }}"
                           href="{{ route('pengajuan.create') }}" aria-expanded="false">
                            <span><i class="ti ti-circle-plus"></i></span>
                            <span class="hide-menu">Buat Pengajuan</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    {{-- MAIN WRAPPER --}}
    <div class="body-wrapper">

        {{-- HEADER --}}
        <header class="app-header">
            <nav class="navbar navbar-expand-lg navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item d-block d-xl-none">
                        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav quick-links d-none d-lg-flex ms-3">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="ti ti-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pengajuan.index') }}">
                            <i class="ti ti-file-text me-1"></i> Pengajuan
                        </a>
                    </li>
                </ul>

                <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-icon-hover" href="javascript:void(0)"
                               id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset('images/profile/user-1.jpg') }}"
                                     alt="user" width="35" height="35" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                 aria-labelledby="drop2">
                                <div class="message-body">
                                    <div class="d-flex align-items-center gap-2 py-3 px-4 border-bottom">
                                        <img src="{{ asset('images/profile/user-1.jpg') }}"
                                             class="rounded-circle" alt="user-img" width="40" height="40">
                                        <div>
                                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                            <small class="text-muted">{{ Auth::user()->email }}</small>
                                            <br>
                                            <span class="badge bg-primary-subtle text-primary">Pegawai</span>
                                        </div>
                                    </div>
                                    <form method="POST" action="/logout">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-outline-primary mx-3 mt-2 d-block w-85">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        {{-- CONTENT --}}
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="ti ti-circle-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="{{ asset('libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/sidebarmenu.js') }}"></script>
<script src="{{ asset('js/app.min.js') }}"></script>
@stack('scripts')
</body>
</html>