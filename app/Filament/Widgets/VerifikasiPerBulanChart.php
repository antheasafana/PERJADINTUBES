<?php

namespace App\Filament\Widgets;

use App\Models\Verifikasi;
use Filament\Widgets\ChartWidget;

class VerifikasiPerBulanChart extends ChartWidget
{
    protected static ?string $heading = 'Verifikasi Per Bulan';

    protected function getData(): array
    {
        $data = Verifikasi::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $bulan = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Verifikasi',
                    'data' => $data->pluck('total')->toArray(),
                ],
            ],
            'labels' => $data->map(fn ($item) => $bulan[$item->bulan - 1])->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}