<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RealisasiDanaResource\Pages;
use App\Models\Pengajuan;
use App\Models\RealisasiDana;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;

// tambahan untuk tombol unduh pdf
use Filament\Tables\Actions\Action; //untuk dapat menggunakan action
use Barryvdh\DomPDF\Facade\Pdf; // Kalau kamu pakai DomPDF
use Illuminate\Support\Facades\Storage;
// Pastikan model Penjualan di-import jika digunakan di PDF
use App\Models\Penjualan; 

class RealisasiDanaResource extends Resource
{
    protected static ?string $model = RealisasiDana::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 11;

    protected static ?string $pluralModelLabel = 'Realisasi Dana';

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Data Pengajuan')
                        ->schema([
                            Select::make('id_pengajuan')
                                ->label('Pengajuan')
                                ->options(
                                    Pengajuan::whereDoesntHave('realisasiDana')
                                        ->where(function ($query) {
                                            $query->whereIn('jenis_pengajuan', [
                                                'REIMBURSEMENT',
                                                'PENGEMBALIAN',
                                            ])
                                            ->orWhere(function ($subQuery) {
                                                $subQuery
                                                    ->where('jenis_pengajuan', 'UANG_MUKA')
                                                    ->whereHas('verifikasi', function ($verifikasi) {
                                                        $verifikasi->where('status', 'approve');
                                                    });
                                            });
                                        })
                                        ->pluck('tujuan', 'id_pengajuan')
                                )
                                ->disabled(),

                            TextInput::make('jenis_pengajuan')
                                ->label('Jenis Pengajuan')
                                ->formatStateUsing(
                                    fn ($record) => $record?->pengajuan?->jenis_pengajuan
                                )
                                ->readOnly(),

                            TextInput::make('tgl_pengajuan')
                                ->label('Tanggal Pengajuan')
                                ->formatStateUsing(
                                    fn ($record) => $record?->pengajuan?->created_at?->format('Y-m-d')
                                )
                                ->readOnly(),

                            TextInput::make('dokumen_spt')
                                ->label('Dokumen')
                                ->formatStateUsing(
                                    fn ($record) => $record?->pengajuan?->dokumen
                                )
                                ->readOnly()
                                ->suffixAction(
                                    \Filament\Forms\Components\Actions\Action::make('lihat')
                                        ->icon('heroicon-m-eye')
                                        ->url(
                                            fn ($state) => asset('dokumen/' . $state),
                                            true
                                        )
                                ),
                        ])
                        ->columns(2),

                    Step::make('Realisasi Dana')
                        ->schema([
                            TextInput::make('total_realisasi')
                                ->label('Total Realisasi')
                                ->numeric()
                                ->prefix('Rp')
                                ->required(),

                            DatePicker::make('tgl_realisasi')
                                ->label('Tanggal Realisasi')
                                ->default(now())
                                ->required(),
                            
                            Textarea::make('catatan')
                                ->label('Catatan')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                ])
                ->columnSpanFull(),
            ])
            ->columns(1);
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pengajuan.tujuan')
                    ->label('Tujuan')
                    ->searchable(),

                TextColumn::make('pengajuan.jenis_pengajuan')
                    ->label('Jenis Pengajuan'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'TEREALISASI' => 'success',
                        default => 'warning',
                    }),

                TextColumn::make('tgl_realisasi')
                    ->label('Tanggal Realisasi')
                    ->date(),

                TextColumn::make('total_realisasi')
                    ->label('Total Realisasi')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp '),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('View & Realisasikan')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'PENDING'),

                Tables\Actions\ViewAction::make()
                    ->visible(fn ($record) => $record->status === 'TEREALISASI'),

                Tables\Actions\Action::make('terealisasi')
                    ->label('Terealisasi')
                    ->color('success')
                    ->disabled()
                    ->visible(fn ($record) => $record->status === 'TEREALISASI'),

                Tables\Actions\Action::make('batalkan')
                    ->label('Batalkan Realisasi')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'TEREALISASI')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'PENDING',
                        ]);
                    }), // <--- DI SINI: Kurung penutup action 'batalkan' yang tadinya tertinggal
            ], position: ActionsPosition::AfterColumns)
            ->actionsAlignment('center') 
            ->headerActions([ // <--- SEKARANG: headerActions sejajar dengan properti table lainnya
                // Tombol Unduh PDF
                Action::make('downloadPdf')
                ->label('Unduh PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {

                    $realisasiDana = RealisasiDana::all();

                    $pdf = Pdf::loadView(
                        'pdf.realisasidana',
                        [
                            'realisasiDana' => $realisasiDana
                        ]
                    );

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'realisasi-dana.pdf'
        );
    })
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRealisasiDana::route('/'),
            'create' => Pages\CreateRealisasiDana::route('/create'),
            'edit' => Pages\EditRealisasiDana::route('/{record}/edit'),
        ];
    }
}