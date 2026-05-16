<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Services\PerjadinDocumentService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPembayaran extends EditRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function resolveRecord(string|int $key): \Illuminate\Database\Eloquent\Model
    {
        return static::getModel()::query()
            ->with([
                'pengajuan.pegawai',
                'pengajuan.realisasiDana',
                'pengajuan.transaksiPengeluaran',
                'transaksiPengeluaran',
            ])
            ->findOrFail($key);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadInvoice')
                ->label('Unduh Invoice PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $pdf = PerjadinDocumentService::pembayaranInvoicePdf($this->record);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'invoice-pembayaran-' . $this->record->id_pembayaran . '.pdf'
                    );
                }),
            ...parent::getHeaderActions(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) === 'dibayar') {
            $data['tanggal_pembayaran'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $pembayaran = $this->record->fresh(['pengajuan', 'transaksiPengeluaran']);

        if ($pembayaran->status !== 'dibayar') {
            return;
        }

        if ($pembayaran->transaksiPengeluaran) {
            $transaksi = $pembayaran->transaksiPengeluaran;

            $transaksi->update([
                'status' => 'transaksi_tercatat',
                'tanggal_pembayaran' => $pembayaran->tanggal_pembayaran ?? now(),
                'tanggal_tercatat' => now(),
            ]);

            $transaksi->pengajuan?->update([
                'status' => 'Transaksi Tercatat',
            ]);

            return;
        }

        if ($pembayaran->jenis_pembayaran === 'uang_muka') {
            $pembayaran->pengajuan?->update([
                'status' => 'Transaksi Tercatat',
            ]);
        }

        PerjadinDocumentService::sendPembayaranInvoiceEmail($pembayaran);

        Notification::make()
            ->title('Pembayaran tercatat & invoice dikirim ke pegawai')
            ->success()
            ->send();
    }
}
