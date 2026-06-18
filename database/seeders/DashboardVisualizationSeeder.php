<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardVisualizationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categoryIds = [];

        foreach ([
            'Transportasi',
            'Akomodasi',
            'Konsumsi',
            'Uang Harian',
        ] as $category) {
            $categoryIds[$category] = $this->ensureCategory($category, $now);
        }

        foreach ($this->transactions($categoryIds) as $transaction) {
            $this->ensureTransaction($transaction, $now);
        }
    }

    private function ensureCategory(string $name, $now): int
    {
        $id = DB::table('KategoriBiaya')
            ->where('jenis_biaya', $name)
            ->value('id_kategori');

        if ($id) {
            DB::table('KategoriBiaya')
                ->where('id_kategori', $id)
                ->update([
                    'updated_at' => $now,
                ]);

            return (int) $id;
        }

        return (int) DB::table('KategoriBiaya')->insertGetId([
            'jenis_biaya' => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function ensureTransaction(array $transaction, $now): void
    {
        $exists = DB::table('transaksi_pengeluaran')
            ->where('uraian', $transaction['uraian'])
            ->exists();

        if ($exists) {
            DB::table('transaksi_pengeluaran')
                ->where('uraian', $transaction['uraian'])
                ->update([
                    ...$transaction,
                    'updated_at' => $now,
                ]);

            return;
        }

        DB::table('transaksi_pengeluaran')->insert([
            ...$transaction,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function transactions(array $categoryIds): array
    {
        return [
            $this->transaction(
                $categoryIds['Transportasi'],
                '2026-01-12',
                'Seed Dashboard - Tiket pesawat Jakarta ke Surabaya',
                1250000
            ),
            $this->transaction(
                $categoryIds['Konsumsi'],
                '2026-01-13',
                'Seed Dashboard - Konsumsi perjalanan Surabaya',
                350000
            ),
            $this->transaction(
                $categoryIds['Akomodasi'],
                '2026-02-08',
                'Seed Dashboard - Hotel dinas Bandung',
                1450000
            ),
            $this->transaction(
                $categoryIds['Transportasi'],
                '2026-02-09',
                'Seed Dashboard - Transport lokal Bandung',
                475000
            ),
            $this->transaction(
                $categoryIds['Uang Harian'],
                '2026-03-16',
                'Seed Dashboard - Uang harian perjalanan Medan',
                900000
            ),
            $this->transaction(
                $categoryIds['Konsumsi'],
                '2026-03-17',
                'Seed Dashboard - Konsumsi perjalanan Medan',
                425000
            ),
            $this->transaction(
                $categoryIds['Akomodasi'],
                '2026-04-21',
                'Seed Dashboard - Hotel dinas Yogyakarta',
                1100000
            ),
            $this->transaction(
                $categoryIds['Transportasi'],
                '2026-04-22',
                'Seed Dashboard - Transport dinas Yogyakarta',
                625000
            ),
            $this->transaction(
                $categoryIds['Uang Harian'],
                '2026-05-15',
                'Seed Dashboard - Uang harian perjalanan Semarang',
                850000
            ),
            $this->transaction(
                $categoryIds['Konsumsi'],
                '2026-05-16',
                'Seed Dashboard - Konsumsi perjalanan Semarang',
                375000
            ),
        ];
    }

    private function transaction(
        int $categoryId,
        string $date,
        string $description,
        int $nominal
    ): array {
        return [
            'id_pengajuan' => null,
            'id_kategori' => $categoryId,
            'id_akun' => null,
            'jenis_pengeluaran' => 'REIMBURSEMENT',
            'tanggal_pengeluaran' => $date,
            'uraian' => $description,
            'nominal' => $nominal,
            'bukti' => null,
            'status' => 'transaksi_tercatat',
            'catatan_verifikasi' => null,
            'tanggal_verifikasi' => "{$date} 10:00:00",
            'tanggal_pembayaran' => "{$date} 11:00:00",
            'tanggal_tercatat' => "{$date} 12:00:00",
        ];
    }
}
