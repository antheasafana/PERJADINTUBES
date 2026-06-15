<?php

namespace App\Filament\Resources\VerifikasiResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\Verifikasi; // Tambahkan ini untuk mengambil data database
use Illuminate\Support\Facades\Http; // Tambahkan ini untuk fitur HTTP Client

class AiInsightVerifikasiWidget extends Widget
{
    protected static string $view = 'filament.resources.verifikasi-resource.widgets.ai-insight-verifikasi-widget';

    protected int|string|array $columnSpan = 'full';

    // Definisikan properti agar bisa dibaca di file Blade (View)
    public ?string $insight = null;

    public function mount()
    {
        // 1. Ambil data riil/dinamis dari database
        $total = Verifikasi::count();
        $approve = Verifikasi::where('status', 'approve')->count();
        $reject = Verifikasi::where('status', 'reject')->count();
        $pending = Verifikasi::where('status', 'pending')->count();

        // 2. Kirim request ke Gemini API menggunakan data dinamis tersebut
        try {
            $response = Http::post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . env('GEMINI_API_KEY'),
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Analisis data verifikasi perjalanan dinas berikut. Total Verifikasi: {$total}. Approve: {$approve}. Reject: {$reject}. Pending: {$pending}. Berikan maksimal 4 insight singkat dalam bentuk bullet point untuk dashboard admin."
                                ]
                            ]
                        ]
                    ]
                ]
            );

            // 3. Simpan hasil teks ke properti $insight
            $this->insight = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal memuat insight dari AI.';
        } catch (\Exception $e) {
            // Berjaga-jaga jika koneksi internet terputus atau API Key salah
            $this->insight = 'Terjadi kesalahan saat menghubungkan ke AI: ' . $e->getMessage();
        }
    }
}