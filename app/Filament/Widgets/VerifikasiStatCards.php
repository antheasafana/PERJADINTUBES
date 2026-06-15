<?php

namespace App\Filament\Widgets;

use App\Models\Verifikasi;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VerifikasiStatCards extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Total Disetujui',
                Verifikasi::where('status', 'approve')->count()
            )
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make(
                'Total Ditolak',
                Verifikasi::where('status', 'reject')->count()
            )
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}