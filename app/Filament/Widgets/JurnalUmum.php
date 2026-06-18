<?php

namespace App\Filament\Resources\JurnalResource\Widgets;

use App\Models\JurnalDetail;
use Filament\Widgets\Widget;

class JurnalUmum extends Widget
{
    protected static string $view = 'filament.resources.jurnal-resource.widgets.jurnal-umum';

    protected int|string|array $columnSpan = 'full';

    public string $periode;

    public function mount(): void
    {
        $this->periode = now()->format('Y-m');
    }

    public function getData(): array
    {
        $details = JurnalDetail::with([
            'jurnal',
            'akun'
        ])
        ->whereHas('jurnal', function ($query) {
            $query->whereYear('tanggal', substr($this->periode, 0, 4))
                  ->whereMonth('tanggal', substr($this->periode, 5, 2));
        })
        ->get();

        return [
            'details' => $details,
            'totalDebit' => $details->sum('debit'),
            'totalKredit' => $details->sum('kredit'),
        ];
    }
}