<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TotalPengeluaranChart extends ChartWidget
{
    protected static ?string $heading = 'Total Pengeluaran per Bulan';

    protected static ?string $description = 'Berdasarkan tanggal transaksi pengeluaran.';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '320px';

    protected int | string | array $columnSpan = 'full';

    protected static ?array $options = [
        'scales' => [
            'y' => [
                'beginAtZero' => true,
            ],
        ],
    ];

    protected function getData(): array
    {
        $rows = DB::table('transaksi_pengeluaran')
            ->selectRaw("DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') as periode")
            ->selectRaw('SUM(nominal) as total')
            ->whereNotNull('tanggal_pengeluaran')
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Total Pengeluaran',
                        'data' => [0],
                        'backgroundColor' => '#d1d5db',
                    ],
                ],
                'labels' => ['Belum ada data'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pengeluaran',
                    'data' => $rows
                        ->pluck('total')
                        ->map(fn ($total): float => (float) $total)
                        ->all(),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                ],
            ],
            'labels' => $rows
                ->pluck('periode')
                ->map(fn ($periode): string => $this->formatPeriodLabel((string) $periode))
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function formatPeriodLabel(string $period): string
    {
        return Carbon::createFromFormat('Y-m', $period)->format('M Y');
    }
}
