<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Wizard Pengajuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f4f7fe;
        }

        .wizard{
            max-width:800px;
            margin:auto;
            margin-top:50px;
            background:white;
            padding:40px;
            border-radius:20px;
        }

        .step{
            display:none;
        }

        .step.active{
            display:block;
        }
    </style>
</head>
<body>

<div class="wizard shadow">

    <h2 class="mb-4">Wizard Pengajuan</h2>

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
</script>

</body>
</html>