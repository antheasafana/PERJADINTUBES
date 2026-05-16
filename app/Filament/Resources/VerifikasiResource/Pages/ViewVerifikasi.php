<?php

namespace App\Filament\Resources\VerifikasiResource\Pages;

use App\Filament\Resources\VerifikasiResource;
use App\Models\Pembayaran;
use App\Services\PerjadinDocumentService;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewVerifikasi extends ViewRecord
{
    protected static string $resource = VerifikasiResource::class;

    protected static string $view = 'filament.resources.verifikasi-resource.pages.view-verifikasi';

    protected function resolveRecord(string|int $key): \Illuminate\Database\Eloquent\Model
    {
        return static::getModel()::query()
            ->with([
                'pengajuan.pegawai',
                'pengajuan.realisasiDana',
                'pengajuan.transaksiPengeluaran.kategoriBiaya',
                'pengajuan.transaksiPengeluaran.akun',
                'transaksiPengeluaran.kategoriBiaya',
                'transaksiPengeluaran.akun',
            ])
            ->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportPdf')
                ->label('Unduh PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $pdf = PerjadinDocumentService::verifikasiPdf($this->record);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'verifikasi-' . $this->record->id . '.pdf'
                    );
                }),

            Actions\Action::make('exportPengajuanPdf')
                ->label('PDF Pengajuan + Pengeluaran')
                ->icon('heroicon-o-document-text')
                ->visible(fn () => $this->record->pengajuan !== null)
                ->action(function () {
                    $pdf = PerjadinDocumentService::pengajuanPdf($this->record->pengajuan);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'pengajuan-' . $this->record->id_pengajuan . '-lengkap.pdf'
                    );
                }),

            Actions\Action::make('approve')
                ->label('Setujui')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->form([
                    Textarea::make('catatan')->label('Catatan (opsional)')->rows(3),
                ])
                ->action(function (array $data) {
                    $record = $this->record;

                    $record->update([
                        'status' => 'approve',
                        'catatan' => $data['catatan'] ?? null,
                        'tanggal_verifikasi' => now(),
                    ]);

                    if ($record->transaksiPengeluaran) {
                        $transaksi = $record->transaksiPengeluaran;
                        $transaksi->update(['status' => 'pembayaran', 'tanggal_verifikasi' => now()]);
                        $transaksi->pengajuan?->update(['status' => 'Pembayaran']);
                        Pembayaran::createPendingForPengajuan($transaksi->pengajuan, $transaksi);
                    } elseif ($record->pengajuan?->jenis_pengajuan === 'UANG_MUKA') {
                        Pembayaran::createPendingForPengajuan($record->pengajuan);
                        $record->pengajuan->update(['status' => 'Pembayaran']);
                    }

                    $record->refresh();
                    PerjadinDocumentService::sendVerifikasiEmail($record, 'approve');

                    Notification::make()->title('Verifikasi disetujui')->success()->send();

                    return redirect(VerifikasiResource::getUrl('index'));
                }),

            Actions\Action::make('reject')
                ->label('Tolak')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn () => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->form([
                    Textarea::make('alasan_reject')->label('Alasan penolakan')->required()->rows(3),
                ])
                ->action(function (array $data) {
                    $record = $this->record;

                    $record->update([
                        'status' => 'reject',
                        'alasan_reject' => $data['alasan_reject'],
                        'tanggal_verifikasi' => now(),
                    ]);

                    if ($record->transaksiPengeluaran) {
                        $record->transaksiPengeluaran->update([
                            'status' => 'ditolak',
                            'catatan_verifikasi' => $data['alasan_reject'],
                        ]);
                        $record->transaksiPengeluaran->pengajuan?->update(['status' => 'Pengeluaran Ditolak']);
                    } else {
                        $record->pengajuan?->update(['status' => 'Pengajuan Ditolak']);
                    }

                    $record->refresh();
                    PerjadinDocumentService::sendVerifikasiEmail($record, 'reject');

                    Notification::make()->title('Verifikasi ditolak')->success()->send();

                    return redirect(VerifikasiResource::getUrl('index'));
                }),
        ];
    }
}
