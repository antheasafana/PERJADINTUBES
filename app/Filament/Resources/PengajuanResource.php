<?php

namespace App\Filament\Resources;

use App\Models\Pengajuan;
use App\Models\JenisTransaksi;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Pengajuan';

    // ================= FORM =================
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Select::make('jenis_pengajuan')
                ->label('Jenis Pengajuan')
                ->options(
                    JenisTransaksi::pluck('jenis_transaksi', 'jenis_transaksi')
                )
                ->searchable()
                ->reactive()
                ->required(),

            Forms\Components\TextInput::make('tujuan')
                ->required()
                ->maxLength(255),

            Forms\Components\DatePicker::make('tgl_berangkat')
                ->required(),

            Forms\Components\DatePicker::make('tgl_kembali')
                ->required()
                ->afterOrEqual('tgl_berangkat'),

            // ESTIMASI (hanya uang muka)
            Forms\Components\TextInput::make('estimasi_biaya')
                ->numeric()
                ->prefix('Rp')
                ->visible(fn ($get) => $get('jenis_pengajuan') === 'Uang muka'),

            // PARENT (hanya reimbursement)
            Forms\Components\Select::make('id_pengajuan_parent')
                ->label('Referensi Uang Muka')
                ->relationship(
                    'parent',
                    'tujuan',
                    fn ($query) => $query->where('jenis_pengajuan', 'Uang muka')
                )
                ->searchable()
                ->visible(fn ($get) => $get('jenis_pengajuan') === 'Reimbursement'),

            // ================= DOKUMEN =================

            // UANG MUKA → Surat Tugas
            Forms\Components\FileUpload::make('dokumen.surat_tugas')
                ->label('Upload Surat Tugas')
                ->directory('surat_tugas')
                ->required(fn ($get) => $get('jenis_pengajuan') === 'Uang muka')
                ->visible(fn ($get) => $get('jenis_pengajuan') === 'Uang muka'),

            // REIMBURSEMENT → LPJ
            Forms\Components\FileUpload::make('dokumen.lpj')
                ->label('Laporan Pertanggungjawaban')
                ->directory('lpj')
                ->required(fn ($get) => $get('jenis_pengajuan') === 'Reimbursement')
                ->visible(fn ($get) => $get('jenis_pengajuan') === 'Reimbursement'),

            // REIMBURSEMENT → Surat Tugas
            Forms\Components\FileUpload::make('dokumen.surat_tugas_reimburse')
                ->label('Surat Tugas')
                ->directory('surat_tugas')
                ->visible(fn ($get) => $get('jenis_pengajuan') === 'Reimbursement'),

            // REIMBURSEMENT → SPPD
            Forms\Components\FileUpload::make('dokumen.sppd')
                ->label('SPPD (Sudah Ditandatangani)')
                ->directory('sppd')
                ->visible(fn ($get) => $get('jenis_pengajuan') === 'Reimbursement'),

            // REIMBURSEMENT → Dokumentasi
            Forms\Components\FileUpload::make('dokumen.dokumentasi')
                ->label('Dokumentasi Kegiatan')
                ->multiple()
                ->image()
                ->directory('dokumentasi')
                ->visible(fn ($get) => $get('jenis_pengajuan') === 'Reimbursement'),
        ]);
    }

    // ================= TABLE =================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tujuan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan'),

                Tables\Columns\TextColumn::make('tgl_berangkat')
                    ->date(),

                Tables\Columns\TextColumn::make('tgl_kembali')
                    ->date(),

                Tables\Columns\TextColumn::make('estimasi_biaya')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'Diajukan',
                        'success' => 'Approved',
                        'danger' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    // ================= RELATION =================
    public static function getRelations(): array
    {
        return [];
    }

    // ================= PAGES =================
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PengajuanResource\Pages\ListPengajuans::route('/'),
            'create' => \App\Filament\Resources\PengajuanResource\Pages\CreatePengajuan::route('/create'),
            'edit' => \App\Filament\Resources\PengajuanResource\Pages\EditPengajuan::route('/{record}/edit'),
        ];
    }
}