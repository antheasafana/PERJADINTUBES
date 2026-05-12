<?php

namespace App\Filament\Resources;

use App\Models\Pembayaran;

use App\Filament\Resources\PembayaranResource\Pages;

use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;

class PembayaranResource extends Resource
{
    /*
    |--------------------------------------------------------------------------
    | MODEL
    |--------------------------------------------------------------------------
    */

    protected static ?string $model =
        Pembayaran::class;

    /*
    |--------------------------------------------------------------------------
    | NAVIGATION
    |--------------------------------------------------------------------------
    */

    protected static ?string $navigationIcon =
        'heroicon-o-banknotes';

    protected static ?string $navigationLabel =
        'Pembayaran';

    protected static ?string $navigationGroup =
        'Transaksi';

    protected static ?int $navigationSort = 3;

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(
        \Filament\Forms\Form $form
    ): \Filament\Forms\Form {

        return $form
            ->schema([]);
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table
    ): Table {

        return $table

            /*
            |--------------------------------------------------------------------------
            | HANYA STATUS PEMBAYARAN
            |--------------------------------------------------------------------------
            */

            ->query(
                Pembayaran::query()
            )

            ->columns([
                TextColumn::make('id_pembayaran')
                    ->label('ID Pembayaran')
                    ->searchable(),

                TextColumn::make('pengajuan.id_pengajuan')
                    ->label('ID Pengajuan')
                    ->searchable(),

                TextColumn::make('jenis_pembayaran')
                    ->label('Jenis Pembayaran')
                    ->badge(),

                TextColumn::make('transaksiPengeluaran.uraian')
                    ->label('Uraian')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dibayar' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_pembayaran')
                    ->dateTime(),
            ])

            ->filters([

            ])

            ->actions([])

            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            'index' =>
                Pages\ListPembayarans::route('/'),

            'create' =>
                Pages\CreatePembayaran::route('/create'),

            'edit' =>
                Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}