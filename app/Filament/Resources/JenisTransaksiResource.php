<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisTransaksiResource\Pages;
use App\Models\JenisTransaksi;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Form Components
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

// Table Columns
use Filament\Tables\Columns\TextColumn;

class JenisTransaksiResource extends Resource
{
    protected static ?string $model = JenisTransaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Jenis Transaksi';

    protected static ?string $pluralModelLabel = 'Jenis Transaksi';

    protected static ?string $modelLabel = 'Jenis Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('jenis_transaksi')
                    ->label('Jenis Transaksi')
                    ->options([
                        'Reimbursement' => 'Reimbursement',
                        'Uang muka' => 'Uang muka',
                    ])
                    ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan Transaksi')
                    ->rows(4),

                FileUpload::make('bukti_transaksi')
                    ->label('Upload Bukti Transaksi')
                    ->directory('documents')
                    ->disk('public')
                    ->maxSize(2048) // 2 MB
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('jenis_transaksi')
                    ->label('Jenis Transaksi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('bukti_transaksi')
                    ->label('Bukti Transaksi')
                    ->formatStateUsing(fn ($state) =>
                        $state
                            ? '<a href="' . asset('storage/' . $state) . '" target="_blank">📄 Lihat File</a>'
                            : 'Tidak ada'
                    )
                    ->html(),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

            ])

            ->filters([
                //
            ])

            ->actions([
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
            'index' => Pages\ListJenisTransaksis::route('/'),
            'create' => Pages\CreateJenisTransaksi::route('/create'),
            'edit' => Pages\EditJenisTransaksi::route('/{record}/edit'),
        ];
    }
}