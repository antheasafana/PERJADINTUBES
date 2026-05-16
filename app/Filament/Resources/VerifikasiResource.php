<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerifikasiResource\Pages;
use App\Models\Pembayaran;
use App\Models\Verifikasi;
use App\Services\PerjadinDocumentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VerifikasiResource extends Resource
{
    protected static ?string $model = Verifikasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Verifikasi Pengajuan';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('catatan')->label('Catatan Verifikasi')->rows(4),
            Textarea::make('alasan_reject')->label('Alasan Reject')->rows(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Verifikasi::query()
                    ->where('status', 'pending')
                    ->with([
                        'pengajuan.pegawai',
                        'transaksiPengeluaran.kategoriBiaya',
                        'transaksiPengeluaran.pengajuan',
                    ])
            )
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),

                TextColumn::make('verification_type')
                    ->label('Jenis Verifikasi')
                    ->badge(),

                TextColumn::make('transaksiPengeluaran.uraian')
                    ->label('Uraian Pengeluaran')
                    ->limit(35)
                    ->placeholder('—')
                    ->description(fn ($record) => $record->transaksiPengeluaran
                        ? 'Rp ' . number_format($record->transaksiPengeluaran->nominal, 0, ',', '.')
                        : null),

                TextColumn::make('pengajuan.tujuan')
                    ->label('Tujuan Pengajuan')
                    ->searchable(),

                TextColumn::make('pengajuan.jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->badge(),

                TextColumn::make('pengajuan.pegawai.nama')
                    ->label('Pegawai'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approve' => 'success',
                        'reject' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime(),
            ])
            ->headerActions([
                Action::make('exportPdf')
                    ->label('Unduh PDF Semua Pending')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $verifikasis = Verifikasi::with([
                            'pengajuan',
                            'transaksiPengeluaran.pengajuan',
                        ])
                            ->where('status', 'pending')
                            ->latest()
                            ->get();

                        $pengajuan = $verifikasis->whereNull('id_transaksi_pengeluaran');
                        $pengeluaran = $verifikasis->whereNotNull('id_transaksi_pengeluaran');

                        $pdf = Pdf::loadView('pdf.verifikasi', compact('pengajuan', 'pengeluaran'))
                            ->setPaper('a4', 'landscape');

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'verifikasi-transaksi.pdf'
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail & Approve'),

                Tables\Actions\Action::make('pdfDetail')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($record) {
                        $pdf = PerjadinDocumentService::verifikasiPdf($record);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'verifikasi-' . $record->id . '.pdf'
                        );
                    }),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('catatan')->label('Catatan (opsional)')->rows(3),
                    ])
                    ->action(function (array $data, $record) {
                        $record->update([
                            'status' => 'approve',
                            'catatan' => $data['catatan'] ?? null,
                            'tanggal_verifikasi' => now(),
                        ]);

                        if ($record->transaksiPengeluaran) {
                            $transaksi = $record->transaksiPengeluaran;
                            $transaksi->update(['status' => 'pembayaran', 'tanggal_verifikasi' => now()]);
                            $transaksi->pengajuan?->update(['status' => 'Pembayaran']);
                            Pembayaran::createPendingForPengajuan($transaksi->pengajuan, $transaksi);
                        } elseif ($record->pengajuan?->jenis_pengajuan === 'UANG_MUKA') {
                            Pembayaran::createPendingForPengajuan($record->pengajuan);
                            $record->pengajuan->update(['status' => 'Pembayaran']);
                        }

                        $record->refresh();
                        PerjadinDocumentService::sendVerifikasiEmail($record, 'approve');

                        Notification::make()->title('Disetujui & email terkirim')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('alasan_reject')->label('Alasan Penolakan')->required(),
                    ])
                    ->action(function (array $data, $record) {
                        $record->update([
                            'status' => 'reject',
                            'alasan_reject' => $data['alasan_reject'],
                            'tanggal_verifikasi' => now(),
                        ]);

                        if ($record->transaksiPengeluaran) {
                            $record->transaksiPengeluaran->update([
                                'status' => 'ditolak',
                                'catatan_verifikasi' => $data['alasan_reject'],
                            ]);
                            $record->transaksiPengeluaran->pengajuan?->update(['status' => 'Pengeluaran Ditolak']);
                        } else {
                            $record->pengajuan?->update(['status' => 'Ditolak']);
                        }

                        $record->refresh();
                        PerjadinDocumentService::sendVerifikasiEmail($record, 'reject');

                        Notification::make()->title('Ditolak & email terkirim')->success()->send();
                    }),
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
            'index' => Pages\ListVerifikasis::route('/'),
            'create' => Pages\CreateVerifikasi::route('/create'),
            'view' => Pages\ViewVerifikasi::route('/{record}'),
            'edit' => Pages\EditVerifikasi::route('/{record}/edit'),
        ];
    }
}
