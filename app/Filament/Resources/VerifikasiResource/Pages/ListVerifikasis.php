<?php

namespace App\Filament\Resources\VerifikasiResource\Pages;

use App\Filament\Resources\VerifikasiResource;
use App\Filament\Resources\VerifikasiResource\Widgets\AiInsightVerifikasiWidget;
use App\Models\AiInsightVerifikasi;
use App\Models\Verifikasi;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Http;

class ListVerifikasis extends ListRecords
{
    protected static string $resource = VerifikasiResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            AiInsightVerifikasiWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateInsight')
                ->label('Generate AI Insight')
                ->icon('heroicon-o-sparkles')
                ->requiresConfirmation()
                ->action(function () {

                    $total = Verifikasi::count();

                    $approve = Verifikasi::where(
                        'status',
                        'approve'
                    )->count();

                    $reject = Verifikasi::where(
                        'status',
                        'reject'
                    )->count();

                    $pending = Verifikasi::where(
                        'status',
                        'pending'
                    )->count();

                    $prompt = "
                    Analisis data verifikasi berikut dan berikan maksimal 4 poin insight singkat.

                    Total Verifikasi: {$total}
                    Approve: {$approve}
                    Reject: {$reject}
                    Pending: {$pending}

                    Berikan insight dalam bentuk poin-poin yang mudah dipahami admin.
                    ";

                    $apiKey = env('GEMINI_API_KEY');

                    $response = Http::post(
                        "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                        [
                            'contents' => [
                                [
                                    'parts' => [
                                        [
                                            'text' => $prompt
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    );

                    $insight =
                        $response->json()['candidates'][0]['content']['parts'][0]['text']
                        ?? 'Gagal membuat insight.';

                    AiInsightVerifikasi::create([
                        'insight' => $insight,
                    ]);

                    Notification::make()
                        ->title('AI Insight berhasil dibuat')
                        ->success()
                        ->send();
                }),
        ];
    }
}