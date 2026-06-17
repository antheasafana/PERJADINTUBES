<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use Filament\Widgets\ChartWidget;

class StatusPembayaranChart extends ChartWidget
{
    protected static ?string $heading = 'Status Pembayaran';

    protected function getData(): array
    {
        $sudahDibayar = Pembayaran::where('status', 'dibayar')->count();

        $belumDibayar = Pembayaran::where('status', 'pending')->count();

        return [

            'datasets' => [
                [
                    'data' => [
                        $sudahDibayar,
                        $belumDibayar,
                    ],

                    'backgroundColor' => [
                        '#22C55E', // hijau
                        '#EF4444', // merah
                    ],
                ],
            ],

            'labels' => [
                'Sudah Dibayar',
                'Belum Dibayar',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}