<?php

namespace App\Filament\Resources\RealisasiDanaResource\Pages;

use App\Filament\Resources\RealisasiDanaResource;
use App\Models\Pengajuan;
use App\Models\RealisasiDana;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;

class ViewRealisasiDana extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = RealisasiDanaResource::class;

    protected static string $view = 'filament.resources.realisasi-dana-resource.pages.view-realisasi-dana';

    public ?array $data = [];

    public Pengajuan $pengajuan;

    public function mount($record): void
    {
        $this->pengajuan = Pengajuan::findOrFail($record);

        $this->form->fill([
            'id_pengajuan' => $this->pengajuan->id_pengajuan,
            'jenis_pengajuan' => $this->pengajuan->jenis_pengajuan,
            'tgl_pengajuan' => $this->pengajuan->created_at?->format('Y-m-d'),
            'dokumen_spt' => $this->pengajuan->dokumen,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                // 🔵 STEP 1 AUTO DATA
                Forms\Components\TextInput::make('id_pengajuan')
                    ->readOnly(),

                Forms\Components\TextInput::make('jenis_pengajuan')
                    ->readOnly(),

                Forms\Components\TextInput::make('tgl_pengajuan')
                    ->readOnly(),

                Forms\Components\TextInput::make('dokumen_spt')
                    ->readOnly(),

                // 🟢 STEP 2 INPUT ADMIN
                Forms\Components\TextInput::make('total_realisasi')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\DatePicker::make('tgl_realisasi')
                    ->default(now())
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        RealisasiDana::create([
            'id_pengajuan' => $this->pengajuan->id_pengajuan,
            'total_realisasi' => $this->data['total_realisasi'],
            'tgl_realisasi' => $this->data['tgl_realisasi'],
        ]);

        $this->pengajuan->update([
            'status' => 'Direalisasikan'
        ]);

        return redirect()->route('filament.admin.resources.realisasi-dana.index');
    }
}