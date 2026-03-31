<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Filament\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
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

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 TextInput::make('nama')
                    ->label('Nama')
                    ->required(),

                TextInput::make('NIP')
                    ->label('NIP')
                    ->required(),

                Textarea::make('posisi')
                    ->label('posisi')
                    ->required(),

                DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->required(),

                FileUpload::make('upload_KTP')
                    ->label('Dokumen')
                    ->directory('documents')
                    ->columnSpan(2)
                    ->required(),

                Toggle::make('is_admin')
                    ->label('Admin?')
                    ->inline(false)
                    ->columnSpan(2)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                ->label('Nama')
                ->searchable()
                ->sortable(),

                TextColumn::make('NIP')
                    ->label('NIP')
                    ->sortable(),

                TextColumn::make('posisi')
                    ->label('posisi')
                    ->sortable(),

                TextColumn::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->sortable(),
                
                TextColumn::make('upload_KTP')
                    ->label('Dokumen')
                    ->url(fn($record) => asset('storage/' . $record->file_path), true)
                    ->formatStateUsing(fn($state) => $state 
                        ? '<a href="' . asset('storage/' . $state) . '" target="_blank"><i class="fas fa-file-pdf"></i> 📄 </a>' 
                        : 'Tidak Ada File')
                    ->html(), // Pastikan menggunakan html() agar bisa merender HTML
                    // Buka file saat diklik

                IconColumn::make('is_admin')
                    ->label('Admin?')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
