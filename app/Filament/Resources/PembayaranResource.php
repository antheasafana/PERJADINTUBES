<?php

namespace App\Filament\Resources;

use App\Models\Pembayaran;
use App\Filament\Resources\PembayaranResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\Action;
use App\Services\PerjadinDocumentService;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Transaksi')
                    ->schema([
                        TextInput::make('id_pengajuan')
                            ->label('ID Pengajuan')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('jenis_pembayaran')
                            ->label('Jenis Pembayaran')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'uang_muka' => 'Uang Muka',
                                'reimbursement' => 'Reimbursement',
                                'pengembalian_dana' => 'Pengembalian Dana',
                                default => $state ?? '-',
                            })
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('arah_transaksi')
                            ->label('Arah Transaksi')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'admin_ke_pegawai' => 'Admin ke Pegawai',
                                'pegawai_ke_admin' => 'Pegawai ke Admin (Pengembalian Sisa Dana)',
                                default => $state ?? '-',
                            })
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('sisa_dana_info')
                            ->label('Sisa Dana (Pengembalian)')
                            ->formatStateUsing(function ($record) {
                                if ($record?->pengajuan?->jenis_pengajuan !== 'PENGEMBALIAN') {
                                    return '-';
                                }

                                $pengajuan = $record->pengajuan;
                                $realisasi = $pengajuan->realisasiDana->total_realisasi ?? 0;
                                $pengeluaran = $pengajuan->transaksiPengeluaran->sum('nominal');

                                return 'Rp ' . number_format(max(0, $realisasi - $pengeluaran), 0, ',', '.');
                            })
                            ->visible(fn ($record) => $record?->pengajuan?->jenis_pengajuan === 'PENGEMBALIAN')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('pegawai_nama')
                            ->label('Nama Pegawai')
                            ->formatStateUsing(
                                fn ($record) => $record?->pengajuan?->pegawai?->nama ?? '-'
                            )
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('transaksi_uraian')
                            ->label('Uraian Pengeluaran')
                            ->formatStateUsing(
                                fn ($record) => $record?->transaksiPengeluaran?->uraian ?? '-'
                            )
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Data Pembayaran')
                    ->schema([
                        TextInput::make('nominal')
                            ->label('Nominal Dibayar')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(1)
                            ->required(),

                        TextInput::make('no_rekening_pegawai')
                            ->label('No. Rekening Pegawai')
                            ->maxLength(50)
                            ->required(fn (callable $get): bool => $get('status') === 'dibayar'),

                        Select::make('status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Menunggu Pembayaran',
                                'dibayar' => 'Sudah Dibayar',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Pembayaran::query()
                    ->with(['pengajuan.pegawai', 'transaksiPengeluaran'])
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id_pembayaran')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('pengajuan.id_pengajuan')
                    ->label('ID Pengajuan')
                    ->sortable(),

                TextColumn::make('pengajuan.pegawai.nama')
                    ->label('Pegawai')
                    ->searchable(),

                TextColumn::make('jenis_pembayaran')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'uang_muka' => 'Uang Muka',
                        'reimbursement' => 'Reimbursement',
                        'pengembalian_dana' => 'Pengembalian',
                        default => $state,
                    }),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('no_rekening_pegawai')
                    ->label('No. Rekening')
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dibayar' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'dibayar' => 'Dibayar',
                        'pending' => 'Menunggu',
                        default => $state,
                    }),

                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal Bayar')
                    ->dateTime()
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'dibayar' => 'Dibayar',
                    ]),
                Tables\Filters\SelectFilter::make('jenis_pembayaran')
                    ->label('Jenis Pembayaran')
                    ->options([
                        'uang_muka' => 'Uang Muka',
                        'reimbursement' => 'Reimbursement',
                        'pengembalian_dana' => 'Pengembalian Dana',
                    ]),
            ])
            ->actions([
                Action::make('invoicePdf')
                    ->label('Invoice PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($record) {
                        $pdf = PerjadinDocumentService::pembayaranInvoicePdf($record);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'invoice-' . $record->id_pembayaran . '.pdf'
                        );
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Proses Pembayaran'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}
