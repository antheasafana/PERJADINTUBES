<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JurnalResource\Pages;
use App\Filament\Resources\JurnalResource\Widgets\JurnalUmum;
use App\Models\Jurnal;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;


class JurnalResource extends Resource
{
    protected static ?string $model = Jurnal::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Jurnal Umum';

    protected static ?string $pluralModelLabel = 'Jurnal Umum';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Deskripsi Jurnal')
                    ->schema([

                        DatePicker::make('tanggal')
                            ->required(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->required()
                            ->rows(1)
                            ->columnSpanFull(),

                    ]),

                Section::make('Detail Jurnal')
                    ->schema([

                        Repeater::make('details')
                            ->relationship()
                            ->schema([

                                Select::make('id_akun')
                                    ->label('Akun')
                                    ->relationship('akun', 'nama_akun')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('debit')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),

                                TextInput::make('kredit')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),

                            ])
                            ->columns(3)
                            ->defaultItems(2)
                            ->addActionLabel('Tambah Detail'),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id_jurnal')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('details_sum_debit')
                 ->label('Total Debit')
                ->sum('details', 'debit')
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                TextColumn::make('details_sum_kredit')
                    ->label('Total Kredit')
                    ->sum('details', 'kredit')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJurnals::route('/'),
            'create' => Pages\CreateJurnal::route('/create'),
            'edit' => Pages\EditJurnal::route('/{record}/edit'),
        ];
    }
}