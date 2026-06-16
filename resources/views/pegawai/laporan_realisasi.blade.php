@extends('pegawai.layout')

@section('title', 'Laporan Realisasi Dana')

@section('content')

<div class="container">

    <h2 class="mb-4">
        📊 Laporan Realisasi Dana
    </h2>

    <div class="card shadow-sm border-0 p-4 mb-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h4 class="mb-0">
                🤖 AI Insight Realisasi Dana
            </h4>

            <form action="{{ route('dashboard.refreshRekomendasiAi') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    ✨ Perbarui Insight AI
                </button>
            </form>

        </div>

        <hr>

        <div class="row">

            <div class="col-md-4 mb-3">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <small>Total Pengajuan</small>
                        <h4>
                            Rp {{ number_format($totalPengajuan,0,',','.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <small>Total Realisasi</small>
                        <h4>
                            Rp {{ number_format($totalRealisasi,0,',','.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <small>Efisiensi</small>
                        <h4>
                            {{ $efisiensi }}%
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        <div class="alert alert-success mt-3">
            <strong> Rekomendasi dan Insight:</strong><br>
            {{ $aiInsight }}
        </div>

    </div>

    <div class="row">

    {{-- BAR CHART --}}
    <div class="col-md-6 mb-4">

        <div class="card shadow-sm border-0 p-4 h-100">

            <h5 class="mb-3">
                📊 Realisasi vs Pengajuan
            </h5>

            <canvas id="barChart"></canvas>

        </div>

    </div>

    {{-- LINE CHART --}}
    <div class="col-md-6 mb-4">

        <div class="card shadow-sm border-0 p-4 h-100">

            <h5 class="mb-3">
                📈 Tren Realisasi Dana
            </h5>

            <canvas id="lineChart"></canvas>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const barCtx = document.getElementById('barChart');

new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: @json($barLabels),
        datasets: [{
            label: 'Nominal (Rp)',
            data: @json($barData),
            backgroundColor: '#198754'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        }
    }
});

const lineCtx = document.getElementById('lineChart');

new Chart(lineCtx, {
    type: 'line',
    data: {
        labels: @json($lineLabels),
        datasets: [{
            label: 'Tren Realisasi Dana',
            data: @json($lineData),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true
    }
});

</script>

@endsection