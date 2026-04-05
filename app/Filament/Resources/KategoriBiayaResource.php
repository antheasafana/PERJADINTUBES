<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriBiayaResource\Pages;
use App\Filament\Resources\KategoriBiayaResource\RelationManagers;
use App\Models\KategoriBiaya;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class KategoriBiayaResource extends Resource
{
    protected static ?string $model = KategoriBiaya::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('jenis_biaya')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_kategori')->label('ID'),
                TextColumn::make('jenis_biaya')->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriBiaya::route('/'),
            'create' => Pages\CreateKategoriBiaya::route('/create'),
            'edit' => Pages\EditKategoriBiaya::route('/{record}/edit'),
        ];
    }
}
