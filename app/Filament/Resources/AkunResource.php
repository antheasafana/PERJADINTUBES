<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AkunResource\Pages;
use App\Filament\Resources\AkunResource\RelationManagers;
use App\Models\Akun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// tambahan untuk komponen input form
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Radio;
// tambahan untuk komponen kolom
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Grid;

class AkunResource extends Resource
{
    protected static ?string $model = Akun::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_akun')
                ->label('Kode Akun')
                ->required(),

            TextInput::make('nama_akun')
                ->label('Nama Akun')
                ->required(),

            Select::make('jenis_akun')
                ->label('Jenis Akun')
                ->options([
                    'Aset' => 'Aset',
                    'Kewajiban' => 'Kewajiban',
                    'Modal' => 'Modal',
                ])
                ->required(),

            FileUpload::make('dokumen')
                ->label('Dokumen')
                ->image()
                ->directory('dokumen-akun'),

            DatePicker::make('tanggal_dibuat')
                ->label('Tanggal Dibuat')
                ->required(),  
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('kode_akun')
                ->label('Kode Akun')
                ->searchable(),
                
                TextColumn::make('nama_akun')
                ->label('Nama Akun')
                ->searchable(),
                
                BadgeColumn::make('jenis_akun')
                    ->label('Kategori')
                    ->colors([
                        'Aset' => 'gray',
                        'Kewajiban' => 'yellow',
                        'Modal' => 'red',
                    ]),

                TextColumn::make('tanggal_dibuat')->date(),
                ImageColumn::make('dokumen')->label('Dokumen'), 
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
            'index' => Pages\ListAkuns::route('/'),
            'create' => Pages\CreateAkun::route('/create'),
            'edit' => Pages\EditAkun::route('/{record}/edit'),
        ];
    }
}
