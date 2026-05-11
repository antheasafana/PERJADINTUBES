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

        /* Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #2E7D5B, #4CAF7A);
            padding: 30px;
            color: white;
            position: fixed;
        }

        .sidebar h2 {
            font-weight: 700;
            margin-bottom: 40px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .sidebar a:hover {
            color: #d8f3dc;
        }

        /* Main content */
        .main-content {
            margin-left: 280px;
            padding: 40px;
        }

        /* Dashboard card */
        .dashboard-card {
            background: white;
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0px 5px 20px rgba(0,0,0,0.05);
        }

        .dashboard-card h1 {
            color: #1F5E46;
            font-weight: 700;
            font-size: 50px;
        }

        .dashboard-card p {
            color: #666;
            font-size: 20px;
        }

        /* Button */
        .btn-custom {
            background: #2E7D5B;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
        }

        .btn-custom:hover {
            background: #25674b;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>E-Perjadin</h2>

        <a href="/dashboard">Dashboard</a>
        <a href="/pengajuan">Pengajuan Saya</a>
        <a href="/pengajuan/create">Buat Pengajuan</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <div class="dashboard-card">
            <h1>Dashboard Pegawai</h1>
            <p>Selamat datang di sistem perjalanan dinas.</p>

            <a href="/pengajuan/create" class="btn btn-custom mt-3">
                Buat Pengajuan
            </a>
        </div>

    </div>

</body>
</html>