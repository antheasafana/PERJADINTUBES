<?php

namespace App\Filament\Resources;

use App\Models\Verifikasi;

use App\Filament\Resources\VerifikasiResource\Pages;

use Filament\Forms;
use Filament\Tables;

use Filament\Forms\Form;
use Filament\Tables\Table;

use Filament\Resources\Resource;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Tables\Columns\TextColumn;

class VerifikasiResource extends Resource
{
    /*
    |--------------------------------------------------------------------------
    | MODEL
    |--------------------------------------------------------------------------
    */

    protected static ?string $model = Verifikasi::class;

    /*
    |--------------------------------------------------------------------------
    | NAVIGATION
    |--------------------------------------------------------------------------
    */

    protected static ?string $navigationIcon =
        'heroicon-o-check-badge';

    protected static ?string $navigationLabel =
        'Verifikasi';

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

                /*
                |--------------------------------------------------------------------------
                | PENGAJUAN
                |--------------------------------------------------------------------------
                */

                Select::make('id_pengajuan')

                    ->label('Pengajuan')

                    ->relationship(
                        'pengajuan',
                        'tujuan'
                    )

                    ->searchable()

                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | LEVEL
                |--------------------------------------------------------------------------
                */

                TextInput::make('level')

                    ->numeric()

                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                Select::make('status')

                    ->options([

                        'pending' => 'Pending',  // ← ditambahkan

                        'approve' => 'Approve',

                        'reject' => 'Reject',
                    ])

                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | CATATAN
                |--------------------------------------------------------------------------
                */

                Textarea::make('catatan')

                    ->rows(4),

                /*
                |--------------------------------------------------------------------------
                | ALASAN REJECT
                |--------------------------------------------------------------------------
                */

                Textarea::make('alasan_reject')

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
            ->columns([

                /*
                |--------------------------------------------------------------------------
                | TUJUAN
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'pengajuan.tujuan'
                )

                    ->label('Tujuan')

                    ->searchable(),

                /*
                |--------------------------------------------------------------------------
                | ADMIN
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'admin.name'
                )

                    ->label('Admin'),

                /*
                |--------------------------------------------------------------------------
                | LEVEL
                |--------------------------------------------------------------------------
                */

                TextColumn::make('level')

                    ->badge(),

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                TextColumn::make('status')

                    ->badge()

                    ->color(fn (string $state): string => match ($state) {

                        'approve' => 'success',

                        'reject'  => 'danger',

                        'pending' => 'warning',  // ← ditambahkan

                        default => 'gray',
                    }),

                /*
                |--------------------------------------------------------------------------
                | TANGGAL
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

                Tables\Actions\EditAction::make(),

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

            'index' => Pages\ListVerifikasis::route('/'),

            'create' => Pages\CreateVerifikasi::route('/create'),

            'edit' => Pages\EditVerifikasi::route('/{record}/edit'),

        ];
    }
}