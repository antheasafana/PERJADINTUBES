<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RealisasiDanaResource\Pages;
use App\Models\Pengajuan;
use App\Models\RealisasiDana;

use Filament\Forms\Form;

use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

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

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 1
                    |--------------------------------------------------------------------------
                    */

                    Step::make('Pilih Pengajuan')
                        ->schema([

                            Select::make('id_pengajuan')
                                ->label('Pengajuan')
                                ->options(
                                    Pengajuan::whereDoesntHave('realisasiDana')
                                        ->pluck('tujuan', 'id_pengajuan')
                                )
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {

                                    $pengajuan = Pengajuan::find($state);

                                    if ($pengajuan) {

                                        /*
                                        |--------------------------------------------------------------------------
                                        | AUTO FILL DATA PENGAJUAN
                                        |--------------------------------------------------------------------------
                                        */

                                        // jenis pengajuan
                                        $set(
                                            'jenis_pengajuan',
                                            $pengajuan->jenis_pengajuan
                                        );

                                        // tanggal pengajuan
                                        $set(
                                            'tgl_pengajuan',
                                            $pengajuan->created_at?->format('Y-m-d')
                                        );

                                        // dokumen
                                        $set(
                                            'dokumen_spt',
                                            $pengajuan->dokumen
                                        );
                                    }
                                })
                                ->required(),

                            /*
                            |--------------------------------------------------------------------------
                            | FIELD OTOMATIS
                            |--------------------------------------------------------------------------
                            */

                            TextInput::make('jenis_pengajuan')
                                ->label('Jenis Pengajuan')
                                ->readOnly(),

                            TextInput::make('tgl_pengajuan')
                                ->label('Tanggal Pengajuan')
                                ->readOnly(),

                           TextInput::make('dokumen_spt')
                            ->label('Dokumen')
                            ->readOnly()
                            ->suffixAction(
                                \Filament\Forms\Components\Actions\Action::make('lihat')
                                    ->icon('heroicon-m-eye')
                                    ->url(fn ($state) => asset('storage/' . $state), true)
                            ),

                        ])
                        ->columns(2),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 2
                    |--------------------------------------------------------------------------
                    */

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

                TextColumn::make('tgl_realisasi')
                    ->label('Tanggal Realisasi')
                    ->date(),

                TextColumn::make('total_realisasi')
                    ->label('Total Realisasi')
                    ->money('IDR'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRealisasiDana::route('/'),
            'create' => Pages\CreateRealisasiDana::route('/create'),
            'edit' => Pages\EditRealisasiDana::route('/{record}/edit'),
        ];
    }
}