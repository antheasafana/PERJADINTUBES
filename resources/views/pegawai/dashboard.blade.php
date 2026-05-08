<!DOCTYPE html>
<html>

<head>

    <title>Dashboard Pegawai</title>

</head>

<body>

    <h1>
        Dashboard Pegawai
    </h1>

    <h3>
        Selamat datang,
        {{ Auth::user()->name }}
    </h3>

    <hr>

    <a href="/pengajuan">
        Buat Pengajuan
    </a>

    <br><br>

    <form method="POST" action="/logout">

        @csrf

        <button type="submit">
            Logout
        </button>

    </form>

</body>

</html>