<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Wizard Pengajuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #EEF4EF !important;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        .wizard {
            max-width: 800px;
            margin: 60px auto;
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0px 8px 25px rgba(0,0,0,0.08);
        }

        /* step tetap */
        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        h2 {
            color: #1F5E46;
            font-weight: 700;
            margin-bottom: 25px;
        }

        h4 {
            color: #2E7D5B;
            font-weight: 600;
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            color: #1F5E46;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #dce5dd;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #2E7D5B;
            box-shadow: 0 0 0 0.2rem rgba(46,125,91,0.2);
        }

        .btn-primary {
            background: #2E7D5B !important;
            border: none !important;
            border-radius: 25px;
            padding: 10px 24px;
        }

        .btn-primary:hover {
            background: #25674b !important;
        }

        .btn-secondary {
            background: #F4A261 !important;
            border: none !important;
            border-radius: 25px;
        }

        .btn-secondary:hover {
            background: #E78A3C !important;
        }

        .btn-success {
            background: #49B87C !important;
            border: none !important;
            border-radius: 25px;
        }

        .btn-success:hover {
            background: #389d66 !important;
        }
    </style>
</head>
<body>

<div class="wizard">

    <h2>🌿 Wizard Pengajuan</h2>

    <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- STEP 1 -->
        <div class="step active">
            <h4>Step 1 - Data Perjalanan</h4>

            <div class="mb-3">
                <label>Jenis Pengajuan</label>
                <select class="form-control" name="jenis_pengajuan" required>
                    <option value="">Pilih</option>
                    <option value="UANG_MUKA">Uang Muka</option>
                    <option value="REIMBURSEMENT">Reimbursement</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Tujuan</label>
                <input type="text" class="form-control" name="tujuan" required>
            </div>

            <div class="mb-3">
                <label>Tanggal Berangkat</label>
                <input type="date" class="form-control" name="tgl_berangkat" required>
            </div>

            <div class="mb-3">
                <label>Tanggal Kembali</label>
                <input type="date" class="form-control" name="tgl_kembali" required>
            </div>

            <button type="button" class="btn btn-primary nextBtn">
                Selanjutnya
            </button>
        </div>

        <!-- STEP 2 -->
        <div class="step">
            <h4>Step 2 - Biaya & Dokumen</h4>

            <div class="mb-3">
                <label>Estimasi Biaya</label>
                <input type="number" class="form-control" name="estimasi_biaya" required>
            </div>

            <div class="mb-3">
                <label>Upload Dokumen</label>
                <input type="file" class="form-control" name="dokumen">
            </div>

            <button type="button" class="btn btn-secondary prevBtn">
                Kembali
            </button>

            <button type="button" class="btn btn-primary nextBtn">
                Selanjutnya
            </button>
        </div>

        <!-- STEP 3 -->
        <div class="step">
            <h4>Step 3 - Konfirmasi</h4>

            <p>Pastikan semua data sudah benar sebelum submit.</p>

            <button type="button" class="btn btn-secondary prevBtn">
                Kembali
            </button>

            <button type="submit" class="btn btn-success">
                Submit Pengajuan
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let currentStep = 0;
    const steps = document.querySelectorAll('.step');

    function showStep(index){
        steps.forEach(step => step.classList.remove('active'));
        steps[index].classList.add('active');
    }

    document.querySelectorAll('.nextBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            if(currentStep < steps.length - 1){
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    document.querySelectorAll('.prevBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            if(currentStep > 0){
                currentStep--;
                showStep(currentStep);
            }
        });
    });

    showStep(currentStep);
});
</script>

</body>
</html>