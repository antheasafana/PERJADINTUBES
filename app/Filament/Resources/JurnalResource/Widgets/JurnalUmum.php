<?php

namespace App\Filament\Widgets;

use App\Models\JurnalDetail;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class JurnalUmumWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JurnalDetail::query()
                ->with(['jurnal','akun'])
                ->orderBy('id_jurnal_detail', 'desc')
            )
            ->columns([

                Tables\Columns\TextColumn::make('jurnal.tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jurnal.keterangan')
                    ->label('Keterangan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('akun.nama_akun')
                    ->label('Akun')
                    ->searchable(),

                Tables\Columns\TextColumn::make('debit')
                    ->label('Debit')
                    ->formatStateUsing(
                        fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')
                    ),

                Tables\Columns\TextColumn::make('kredit')
                    ->label('Kredit')
                    ->formatStateUsing(
                        fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')
                    ),
            ])
            ->defaultSort('id_jurnal_detail', 'desc');
    }
}