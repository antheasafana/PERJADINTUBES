<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Wizard Pengajuan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #EEF4EF;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        .wizard-container {
            max-width: 900px;
            margin: 60px auto;
        }

        .wizard-card {
            background: white;
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0px 8px 25px rgba(0,0,0,0.05);
        }

        .main-title {
            font-size: 42px;
            font-weight: 700;
            color: #1F5E46;
            margin-bottom: 20px;
        }

        .step-title {
            font-size: 28px;
            font-weight: 600;
            color: #2E7D5B;
            margin-bottom: 25px;
        }

        label {
            font-weight: 500;
            color: #1F5E46;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-radius: 15px;
            padding: 12px;
            border: 1px solid #dce5dd;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2E7D5B;
            box-shadow: 0 0 0 0.2rem rgba(46,125,91,0.2);
        }

        .btn-next {
            background: #2E7D5B;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
        }

        .btn-next:hover {
            background: #25674b;
            color: white;
        }

        .btn-back {
            background: #F4A261;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
        }

        .btn-back:hover {
            background: #E78A3C;
            color: white;
        }

        .progress {
            height: 10px;
            border-radius: 20px;
            margin-bottom: 30px;
        }

        .progress-bar {
            background: #58C98B;
        }
    </style>
</head>
<body>

<a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
    ← Kembali Dashboard
</a>

<div class="wizard-container">

    <div class="wizard-card">

        <h1 class="main-title">🌿 Wizard Pengajuan</h1>

        <!-- Progress bar -->
        <div class="progress">
            <div class="progress-bar" style="width: 33%"></div>
        </div>

        <h3 class="step-title">Step 1 - Data Perjalanan</h3>

        <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label>Jenis Pengajuan</label>
                <select name="jenis_pengajuan" class="form-select">
                    <option value="">Pilih</option>
                    <option value="REIMBURSEMENT">Reimbursement</option>
                    <option value="UANG_MUKA">Uang Muka</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Tujuan</label>
                <input type="text" name="tujuan" class="form-control">
            </div>

            <div class="mb-3">
                <label>Tanggal Berangkat</label>
                <input type="date" name="tgl_berangkat" class="form-control">
            </div>

            <div class="mb-3">
                <label>Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" class="form-control">
            </div>

            <div class="mb-3">
                <label>Estimasi Biaya</label>
                <input type="number" name="estimasi_biaya" class="form-control">
            </div>

            <div class="mb-4">
                <label>Upload ST</label>
                <input type="file" name="dokumen" class="form-control">
            </div>

            <button type="submit" class="btn btn-next">
                Submit Pengajuan
            </button>

            <a href="/pengajuan" class="btn btn-back ms-2">
                Kembali
            </a>

        </form>

    </div>

</div>

</body>
</html>