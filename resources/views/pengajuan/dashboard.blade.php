<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f7fe;
            font-family:Segoe UI;
        }

        .sidebar{
            width:250px;
            height:100vh;
            position:fixed;
            left:0;
            top:0;
            background:linear-gradient(180deg,#4f46e5,#7c3aed);
            padding:30px 20px;
            color:white;
        }

        .main{
            margin-left:250px;
            padding:30px;
        }

        .menu a{
            display:block;
            color:white;
            text-decoration:none;
            padding:14px;
            border-radius:10px;
            margin-bottom:10px;
        }

        .menu a:hover{
            background:rgba(255,255,255,0.2);
        }

        .hero{
            background:linear-gradient(135deg,#6366f1,#8b5cf6);
            color:white;
            padding:40px;
            border-radius:20px;
        }

    </style>

</head>
<body>

<div class="sidebar">

    <h2>E-Perjadin</h2>

    <div class="menu">

        <a href="/dashboard">
            Dashboard
        </a>

        <a href="/pengajuan">
            Pengajuan Saya
        </a>

        <a href="/pengajuan/create">
            Buat Pengajuan
        </a>

    </div>

</div>

<div class="main">

    <div class="hero">

        <h1>Dashboard Pegawai</h1>

        <p>
            Selamat datang di sistem perjalanan dinas.
        </p>

        <a href="/pengajuan/create" class="btn btn-light">
            Buat Pengajuan
        </a>

    </div>

</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))

<script>

    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session("success") }}',
        confirmButtonColor: '#4f46e5'
    });

</script>

@endif

</body>
</html>