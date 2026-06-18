<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BiayaPerKategoriChart extends ChartWidget
{
    protected static ?string $heading = 'Proporsi Biaya per Kategori';

    protected static ?string $description = 'Berdasarkan nominal transaksi pengeluaran.';

    protected static ?int $sort = 1;

    protected static ?string $maxHeight = '320px';

    protected int | string | array $columnSpan = 'full';

    private const COLORS = [
        '#2563eb',
        '#16a34a',
        '#f59e0b',
        '#dc2626',
        '#7c3aed',
        '#0891b2',
        '#db2777',
        '#65a30d',
    ];

    protected function getData(): array
    {
        $rows = DB::table('transaksi_pengeluaran')
            ->leftJoin(
                'KategoriBiaya',
                'transaksi_pengeluaran.id_kategori',
                '=',
                'KategoriBiaya.id_kategori'
            )
            ->selectRaw("COALESCE(KategoriBiaya.jenis_biaya, 'Tanpa Kategori') as kategori")
            ->selectRaw('SUM(transaksi_pengeluaran.nominal) as total')
            ->groupBy('KategoriBiaya.jenis_biaya')
            ->orderByDesc('total')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Total Biaya',
                        'data' => [0],
                        'backgroundColor' => ['#d1d5db'],
                    ],
                ],
                'labels' => ['Belum ada data'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Biaya',
                    'data' => $rows
                        ->pluck('total')
                        ->map(fn ($total): float => (float) $total)
                        ->all(),
                    'backgroundColor' => $this->colorsForRows($rows->count()),
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $rows
                ->pluck('kategori')
                ->map(fn ($kategori): string => (string) $kategori)
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    private function colorsForRows(int $count): array
    {
        return collect(range(0, $count - 1))
            ->map(fn (int $index): string => self::COLORS[$index % count(self::COLORS)])
            ->all();
    }
}
