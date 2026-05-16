<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · E-Perjadin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            background: #EEF4EF;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .login-wrap {
            width: 100%;
            max-width: 400px;
        }

        .login-brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-brand h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #155F52;
            margin: 0 0 6px;
        }

        .login-brand p {
            margin: 0;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 32px 28px 28px;
            box-shadow: 0 10px 32px rgba(46, 125, 91, 0.08);
            border: 1px solid rgba(46, 125, 91, 0.12);
        }

        .login-card h2 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 22px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #d1d5db;
            padding: 10px 14px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #2E7D5B;
            box-shadow: 0 0 0 3px rgba(46, 125, 91, 0.15);
        }

        .btn-login {
            width: 100%;
            margin-top: 8px;
            padding: 11px;
            border: none;
            border-radius: 12px;
            background: #2E7D5B;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background: #24674b;
            color: #fff;
        }

        .alert-login {
            border-radius: 12px;
            font-size: 0.85rem;
            padding: 10px 14px;
            margin-bottom: 18px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <div class="login-wrap">
        <div class="login-brand">
            <h1>E-Perjadin</h1>
            <p>Portal pegawai · perjalanan dinas</p>
        </div>

        <div class="login-card">
            <h2>Masuk ke akun Anda</h2>

            @if ($errors->any())
                <div class="alert-login">
                    @foreach ($errors->all() as $error)
                        {{ $error }}@if (!$loop->last)<br>@endif
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.process') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        required
                        autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>

        <p class="login-footer">© {{ date('Y') }} E-Perjadin</p>
    </div>

</body>
</html>
