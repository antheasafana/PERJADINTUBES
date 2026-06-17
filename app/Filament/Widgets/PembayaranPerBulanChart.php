<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PembayaranPerBulanChart extends ChartWidget
{
    protected static ?string $heading = 'Pembayaran Per Bulan';

    protected function getData(): array
    {
        $data = Pembayaran::query()
        ->where('status', 'dibayar')
        ->whereNotNull('tanggal_pembayaran')
        ->selectRaw('MONTH(tanggal_pembayaran) as bulan, SUM(nominal) as total')
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get();

        $namaBulan = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',
            5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',
            9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Total Pembayaran',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => '#3B82F6',
                    'borderColor' => '#FFCE56',
                    'borderWidth' => 2,
                ],
            ],

            'labels' => $data->map(function ($item) use ($namaBulan) {
                return $namaBulan[$item->bulan];
            }),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}