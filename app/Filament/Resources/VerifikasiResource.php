<?php

namespace App\Filament\Resources;

use App\Models\Verifikasi;
use App\Filament\Resources\VerifikasiResource\Pages;

use Filament\Forms;
use Filament\Tables;

use Filament\Forms\Form;
use Filament\Tables\Table;

use Filament\Resources\Resource;

use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

use App\Models\RealisasiDana;
use App\Models\TransaksiPengeluaran;

class VerifikasiResource extends Resource
{
    /*
    |--------------------------------------------------------------------------
    | MODEL
    |--------------------------------------------------------------------------
    | ✅ FIX: Gunakan model Verifikasi (bukan TransaksiPengeluaran)
    | Verifikasi di sini adalah approval admin atas pengajuan UANG_MUKA
    | sesuai alur Step ② di gambar.
    */

    protected static ?string $model = Verifikasi::class;

    protected static ?string $navigationIcon =
        'heroicon-o-check-badge';

    protected static ?string $navigationLabel =
        'Verifikasi Pengajuan';

    protected static ?string $navigationGroup =
        'Transaksi';

    protected static ?int $navigationSort = 2;

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Textarea::make('catatan')
                    ->label('Catatan Verifikasi')
                    ->rows(4),

                Textarea::make('alasan_reject')
                    ->label('Alasan Reject')
                    ->rows(4),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return $table

            /*
            |--------------------------------------------------------------------------
            | HANYA STATUS PENDING (menunggu approval admin)
            |--------------------------------------------------------------------------
            */

            ->query(
                Verifikasi::query()
                    ->where('status', 'pending')
                    ->with(['pengajuan', 'transaksiPengeluaran'])
            )

            ->columns([

                TextColumn::make('id')
                    ->label('ID'),

                TextColumn::make('verification_type')
                    ->label('Jenis Verifikasi')
                    ->badge(),

                TextColumn::make('transaksiPengeluaran.id_transaksi_pengeluaran')
                    ->label('ID Transaksi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pengajuan.tujuan')
                    ->label('Tujuan Pengajuan')
                    ->searchable(),

                TextColumn::make('pengajuan.jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->badge(),

                TextColumn::make('pengajuan.estimasi_biaya')
                    ->label('Estimasi Biaya')
                    ->money('IDR'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approve'  => 'success',
                        'reject'   => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime(),
            ])

            ->filters([])

            ->actions([

                Tables\Actions\ViewAction::make(),

                /*
                |--------------------------------------------------------------------------
                | APPROVE → trigger Realisasi Dana
                |--------------------------------------------------------------------------
                | Setelah approve, status pengajuan berubah → admin bisa buat Realisasi Dana
                */

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('catatan')
                            ->label('Catatan (opsional)')
                            ->rows(3),
                    ])
                    ->action(function (array $data, $record) {

                        $record->update([
                            'status'              => 'approve',
                            'catatan'             => $data['catatan'] ?? null,
                            'tanggal_verifikasi'  => now(),
                        ]);

                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE STATUS PENGAJUAN → siap direalisasi admin
                        |--------------------------------------------------------------------------
                        */

                        if ($record->pengajuan) {
                            $record->pengajuan->update([
                                'status' => 'Disetujui'
                            ]);
                        }
                    }),

                /*
                |--------------------------------------------------------------------------
                | REJECT
                |--------------------------------------------------------------------------
                */

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('alasan_reject')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {

                        $record->update([
                            'status'              => 'reject',
                            'alasan_reject'       => $data['alasan_reject'],
                            'tanggal_verifikasi'  => now(),
                        ]);

                        if ($record->pengajuan) {
                            $record->pengajuan->update([
                                'status' => 'Ditolak'
                            ]);
                        }
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
            'index'  => Pages\ListVerifikasis::route('/'),
            'create' => Pages\CreateVerifikasi::route('/create'),
            'edit'   => Pages\EditVerifikasi::route('/{record}/edit'),
        ];
    }
}