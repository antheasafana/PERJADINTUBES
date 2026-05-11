<?php

namespace App\Filament\Resources;

use App\Models\TransaksiPengeluaran;

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
        TransaksiPengeluaran::class;

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

                TransaksiPengeluaran::query()

                    ->where(
                        'status',
                        'pembayaran'
                    )
            )

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | ID PENGAJUAN
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'pengajuan.id_pengajuan'
                )

                    ->label(
                        'ID Pengajuan'
                    )

                    ->searchable(),

                /*
                |--------------------------------------------------------------------------
                | JENIS PENGELUARAN
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'jenis_pengeluaran'
                )

                    ->label(
                        'Jenis'
                    )

                    ->badge(),

                /*
                |--------------------------------------------------------------------------
                | URAIAN
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'uraian'
                )

                    ->limit(40)

                    ->searchable(),

                /*
                |--------------------------------------------------------------------------
                | NOMINAL
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'nominal'
                )

                    ->money('IDR'),

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'status'
                )

                    ->badge()

                    ->color(fn (
                        string $state
                    ): string => match ($state) {

                        'pembayaran'
                            => 'warning',

                        'transaksi_tercatat'
                            => 'success',

                        default
                            => 'gray',
                    }),

                /*
                |--------------------------------------------------------------------------
                | TANGGAL VERIFIKASI
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'tanggal_verifikasi'
                )

                    ->dateTime(),
            ])

            ->filters([

            ])

            ->actions([

                /*
                |--------------------------------------------------------------------------
                | TOMBOL BAYAR
                |--------------------------------------------------------------------------
                */

                Tables\Actions\Action::make(
                    'bayar'
                )

                    ->label(
                        'Bayar'
                    )

                    ->color(
                        'success'
                    )

                    ->icon(
                        'heroicon-o-banknotes'
                    )

                    ->requiresConfirmation()

                    ->action(function (
                        $record
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE STATUS
                        |--------------------------------------------------------------------------
                        */

                        $record->update([

                            'status' =>
                                'transaksi_tercatat',

                            'tanggal_pembayaran' =>
                                now(),

                            'tanggal_tercatat' =>
                                now(),
                        ]);

                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE STATUS PENGAJUAN
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $record->pengajuan
                        ) {

                            $record->pengajuan->update([

                                'status' =>
                                    'Transaksi Tercatat'
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