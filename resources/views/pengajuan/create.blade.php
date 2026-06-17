<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #EEF4EF !important; font-family: 'Poppins', sans-serif; min-height: 100vh; }
        .wizard { max-width: 800px; margin: 60px auto; background: white; padding: 40px; border-radius: 30px; box-shadow: 0px 8px 25px rgba(0,0,0,0.08); }
        .step { display: none; }
        .step.active { display: block; }
        h2 { color: #1F5E46; font-weight: 700; margin-bottom: 25px; }
        h4 { color: #2E7D5B; font-weight: 600; margin-bottom: 20px; }
        .btn-primary { background: #2E7D5B !important; border: none !important; border-radius: 25px; }
        .btn-success { background: #49B87C !important; border: none !important; border-radius: 25px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="wizard">
        <h2>🌿 Pengajuan</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="step active">
                <h4>Step 1 - Data Perjalanan</h4>
                <div class="mb-3">
                    <label>Jenis Pengajuan</label>
                    <select class="form-control" name="jenis_pengajuan" required>
                        <option value="">Pilih</option>
                        <option value="UANG_MUKA">Uang Muka</option>
                        <option value="REIMBURSEMENT">Reimbursement</option>
                        <option value="PENGEMBALIAN">Pengembalian</option>
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
                <button type="button" class="btn btn-primary nextBtn">Selanjutnya</button>
            </div>

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
                <button type="button" class="btn btn-secondary prevBtn">Kembali</button>
                <button type="button" class="btn btn-primary nextBtn">Selanjutnya</button>
            </div>

            <div class="step">
                <h4>Step 3 - Konfirmasi</h4>
                <p>Pastikan semua data sudah benar sebelum submit.</p>
                <button type="button" class="btn btn-secondary prevBtn">Kembali</button>
                <button type="submit" class="btn btn-success">Submit Pengajuan</button>
            </div>
        </form>
    </div>
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
});
</script>
</body>
</html>