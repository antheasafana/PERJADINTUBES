<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\RekomendasiPerjalanan;
use App\Models\Pengajuan;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RekomendasiPerjalananController extends Controller {

    public function generateRekomendasi() {
        $user = Auth::user();
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return redirect()->back()->with('error', 'Kunci API Gemini belum dikonfigurasi.');
        }

        // ===== PERBAIKAN =====
        // Tabel `pengajuan` menyimpan relasi ke `pegawai` (kolom id_pegawai),
        // bukan langsung ke `users`. Jadi kita cari dulu data pegawai
        // berdasarkan email user yang sedang login.
        $pegawai = Pegawai::where('email', $user->email)->first();

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Data pegawai untuk akun Anda (' . $user->email . ') tidak ditemukan. Hubungi admin.');
        }

        $historiPerjalanan = Pengajuan::where('id_pegawai', $pegawai->id)
            ->select('tujuan', 'estimasi_biaya', 'jenis_pengajuan')
            ->get();
        // ===== AKHIR PERBAIKAN =====

        if ($historiPerjalanan->isEmpty()) {
            return redirect()->back()->with('error', 'Data histori tidak mencukupi.');
        }

        $promptData = $historiPerjalanan->map(function($item) {
            return "- Tujuan: {$item->tujuan}, Jenis: {$item->jenis_pengajuan}, Biaya: Rp " . number_format($item->estimasi_biaya, 0, ',', '.');
        })->implode("\n");

        $promptAI = "Anda adalah AI Perjalanan Dinas. Analisis data histori perjalanan dinas berikut: \n" . $promptData . 
            "\n\nInstruksi:\n" .
            "1. Tentukan tujuan/kota yang PALING SERING muncul (frekuensi terbanyak) sebagai 'terpopuler'. Sebutkan nama kota/tujuannya secara spesifik, JANGAN jawab 'tidak tersedia' meskipun datanya hanya sedikit — selama ada minimal 1 data, tujuan dengan frekuensi tertinggi WAJIB disebutkan.\n" .
            "2. Berikan analisis singkat pola perjalanan pada field 'analisis'.\n" .
            "3. Berikan saran efisiensi anggaran pada field 'saran_efisiensi'.\n\n" .
            "Berikan jawaban dalam format JSON murni TANPA markdown. Struktur wajib:\n" .
            "{\"analisis\": \"teks\", \"terpopuler\": \"nama kota/tujuan\", \"saran_efisiensi\": \"teks\"}";

        try {
            $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $promptAI]]]]
            ]);

            if ($response->failed()) {
                throw new \Exception("API Error: " . $response->body());
            }

            $resultText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            // Pembersihan string yang lebih ketat agar JSON valid
            $cleanJson = preg_replace('/^.*?({.*}).*$/s', '$1', $resultText);
            $cleanJson = trim(str_replace(['```json', '```'], '', $cleanJson));

            $dataAi = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("Gagal Parse JSON Gemini: " . $cleanJson);
                return redirect()->back()->with('error', 'Format jawaban AI tidak dapat dibaca.');
            }

            RekomendasiPerjalanan::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'analisis_rekomendasi' => $dataAi['analisis'] ?? 'Analisis tidak tersedia.',
                    'tujuan_terpopuler' => $dataAi['terpopuler'] ?? '-',
                    'saran_efisiensi' => $dataAi['saran_efisiensi'] ?? '-'
                ]
            );

            return redirect()->back()->with('success', 'Rekomendasi berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error generateRekomendasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}