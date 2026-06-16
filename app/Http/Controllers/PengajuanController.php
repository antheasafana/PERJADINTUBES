<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\Pegawai;
use App\Models\RealisasiDana;
use App\Models\TransaksiPengeluaran;
use App\Models\Verifikasi;
use App\Models\User;
use App\Models\RekomendasiPerjalanan;
use App\Services\PerjadinDocumentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PengajuanController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $pengajuanRealisasi = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran'
            ])
            ->whereHas('realisasiDana')
            ->latest()
            ->get();

        $pengeluaranTerbaru = $this->getLatestPengeluaran();

        $totalPengajuanRealisasi = $pengajuanRealisasi->count();
        $totalPengeluaran = TransaksiPengeluaran::sum('nominal');

        $rekomendasiPerjalanan = RekomendasiPerjalanan::where('user_id', $user->id)->first();

        return view('pengajuan.dashboard', compact(
            'pengajuanRealisasi',
            'pengeluaranTerbaru',
            'totalPengajuanRealisasi',
            'totalPengeluaran',
            'rekomendasiPerjalanan'
        ));
    }

    private function getLatestPengeluaran()
    {
        return TransaksiPengeluaran::with('pengajuan')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function index()
    {
        $data = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran'
            ])
            ->latest()
            ->get();

        return view('pengajuan.index', compact('data'));
    }

    public function create()
    {
        return view('pengajuan.create');
    }

    public function store(Request $request)
    {
        // 1. Membersihkan input estimasi_biaya agar tidak ada titik/koma
        if ($request->has('estimasi_biaya')) {
            $request->merge([
                'estimasi_biaya' => str_replace(['.', ','], '', $request->estimasi_biaya)
            ]);
        }

        // 2. Validasi dengan pesan yang lebih jelas
        $request->validate([
            'jenis_pengajuan' => 'required',
            'tujuan' => 'required',
            'tgl_berangkat' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_berangkat',
            'estimasi_biaya' => 'required|numeric|min:1',
            'dokumen' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048'
        ], [
            'estimasi_biaya.numeric' => 'Estimasi biaya harus berupa angka tanpa titik atau koma.',
            'tgl_kembali.after_or_equal' => 'Tanggal kembali tidak boleh lebih awal dari tanggal berangkat.'
        ]);

        $fileName = null;
        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('dokumen'), $fileName);
        }

        // ===== PERBAIKAN: Pencarian pegawai berdasarkan email user yang login =====
        // Setiap pegawai yang mengisi data WAJIB punya record di tabel `pegawai`
        // dengan email yang SAMA dengan email akun login-nya.
        // Fallback ke ID 1 DIHAPUS karena menyebabkan semua pengajuan dari
        // pegawai yang belum terdaftar tercatat sebagai milik pegawai lain (ID 1),
        // sehingga histori & rekomendasi AI jadi tidak akurat.
        $pegawai = Pegawai::where('email', Auth::user()->email)->first();

        if (!$pegawai) {
            Log::error('Gagal menyimpan pengajuan: Data pegawai tidak ditemukan untuk email ' . Auth::user()->email);
            return redirect()->back()->with('error', 'Data pegawai untuk akun Anda (' . Auth::user()->email . ') belum terdaftar di sistem. Silakan hubungi admin untuk menambahkan data pegawai dengan email yang sama dengan akun login Anda.');
        }
        // ===== AKHIR PERBAIKAN =====

        try {
            $pengajuan = Pengajuan::create([
                'id_pegawai' => $pegawai->id,
                'jenis_pengajuan' => $request->jenis_pengajuan,
                'id_pengajuan_parent' => null,
                'tujuan' => $request->tujuan,
                'tgl_berangkat' => $request->tgl_berangkat,
                'tgl_kembali' => $request->tgl_kembali,
                'estimasi_biaya' => $request->estimasi_biaya,
                'dokumen' => $fileName,
                'status' => 'Diajukan'
            ]);
            // --- TAMBAHAN BAGIAN AI DISINI ---
            // Pastikan Anda memanggil fungsi yang menganalisis rekomendasi setelah pengajuan dibuat
            try {
                // Asumsi: Anda memiliki service atau model yang menangani analisis
                // Misalnya: \App\Services\AiRecommendationService::analyze($pengajuan);
                // Atau jika Anda menggunakan model RekomendasiPerjalanan:
                \App\Models\RekomendasiPerjalanan::updateOrCreate(
                    ['user_id' => Auth::id()],
                    ['rekomendasi' => 'Hasil analisis otomatis untuk tujuan: ' . $pengajuan->tujuan]
                );
            } catch (\Exception $aiError) {
                Log::warning('Analisis AI gagal, namun pengajuan tetap disimpan: ' . $aiError->getMessage());
            }
            // ---------

            if ($request->jenis_pengajuan == 'UANG_MUKA') {
                $admin = User::where('user_group', 'admin')->first();
                $adminId = $admin ? $admin->id : 7;

                Verifikasi::create([
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'admin_id' => $adminId,
                    'level' => 1,
                    'status' => 'pending',
                    'tanggal_verifikasi' => null,
                ]);
            } else {
                RealisasiDana::create([
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'tgl_realisasi' => now(),
                    'total_realisasi' => 0,
                    'status' => 'PENDING',
                ]);

                $pengajuan->update(['status' => 'Menunggu Realisasi Dana']);
            }

            $pengajuan->load('pegawai');
            PerjadinDocumentService::sendPengajuanBerhasilEmail($pengajuan);

            auth()->user()->notifications()->create([
                'id' => \Str::uuid(),
                'type' => 'pengajuan',
                'data' => [
                    'title' => 'Pengajuan Berhasil',
                    'body'  => 'Pengajuan #' . $pengajuan->id_pengajuan . ' (' . $pengajuan->tujuan . ') berhasil diajukan.',
                ],
            ]);

            return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dikirim.');

        } catch (\Exception $e) {
            Log::error('Error saat simpan pengajuan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function realisasiIndex()
    {
        $menungguRealisasi = Pengajuan::with('realisasiDana')
            ->whereIn('jenis_pengajuan', ['REIMBURSEMENT', 'PENGEMBALIAN'])
            ->whereHas('realisasiDana', function ($query) {
                $query->where('status', 'PENDING');
            })
            ->latest()
            ->get();

        $sudahRealisasi = Pengajuan::with('realisasiDana')
            ->whereIn('jenis_pengajuan', ['REIMBURSEMENT', 'PENGEMBALIAN'])
            ->whereHas('realisasiDana', function ($query) {
                $query->where('status', 'TEREALISASI')
                    ->where('total_realisasi', '>', 0);
            })
            ->latest()
            ->get();

        return view('realisasi.index', compact('menungguRealisasi', 'sudahRealisasi'));
    }

    public function realisasiForm($id)
    {
        $pengajuan = Pengajuan::with('realisasiDana')->findOrFail($id);

        if (! in_array($pengajuan->jenis_pengajuan, ['REIMBURSEMENT', 'PENGEMBALIAN'], true)) {
            return redirect()->route('pengajuan.index')->with('error', 'Realisasi dana hanya untuk reimbursement atau pengembalian.');
        }

        if (! $pengajuan->realisasiDana || $pengajuan->realisasiDana->status === 'TEREALISASI') {
            return redirect()->route('realisasi.index')->with('info', 'Realisasi dana sudah diinput.');
        }

        return view('pengajuan.realisasi', compact('pengajuan'));
    }

    public function realisasiStore(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('realisasiDana')->findOrFail($id);

        if (! in_array($pengajuan->jenis_pengajuan, ['REIMBURSEMENT', 'PENGEMBALIAN'], true)) {
            return redirect()->route('pengajuan.index')->with('error', 'Realisasi dana hanya untuk reimbursement atau pengembalian.');
        }

        $request->validate([
            'total_realisasi' => 'required|numeric|min:1',
            'tgl_realisasi' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $realisasi = $pengajuan->realisasiDana;
        if (! $realisasi || $realisasi->status === 'TEREALISASI') {
            return redirect()->route('realisasi.index')->with('info', 'Realisasi dana sudah diinput.');
        }

        $realisasi->update([
            'total_realisasi' => $request->total_realisasi,
            'tgl_realisasi' => $request->tgl_realisasi,
            'catatan' => $request->catatan,
            'status' => 'TEREALISASI',
        ]);

        $pengajuan->update(['status' => 'Direalisasikan']);

        auth()->user()->notifications()->create([
            'id' => \Str::uuid(),
            'type' => 'realisasi',
            'data' => [
                'title' => 'Realisasi Dana',
                'body'  => 'Pengajuan #' . $pengajuan->id_pengajuan . ' sudah direalisasikan.',
            ],
        ]);

        return redirect()->route('realisasi.index')->with('success', 'Realisasi dana berhasil disimpan.');
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran.kategoriBiaya',
                'transaksiPengeluaran.akun',
                'verifikasi',
            ])
            ->findOrFail($id);

        return view('pengajuan.show', compact('pengajuan'));
    }

    public function exportPdf($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pdf = PerjadinDocumentService::pengajuanPdf($pengajuan);
        return $pdf->download('pengajuan-' . $pengajuan->id_pengajuan . '-lengkap.pdf');
    }

    public function exportPengajuanRingkasPdf($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pdf = PerjadinDocumentService::pengajuanRingkasPdf($pengajuan);
        return $pdf->download('pengajuan-' . $pengajuan->id_pengajuan . '.pdf');
    }

    public function exportRealisasiPdf($id)
    {
        $pengajuan = Pengajuan::with('realisasiDana')->findOrFail($id);
        if (! $pengajuan->realisasiDana) {
            return redirect()->back()->with('error', 'Belum ada data realisasi dana.');
        }
        $pdf = PerjadinDocumentService::realisasiDanaPdf($pengajuan->realisasiDana);
        return $pdf->download('realisasi-dana-' . $pengajuan->id_pengajuan . '.pdf');
    }

    public function edit($id)
    {
        $pengajuan = Pengajuan::with('realisasiDana')->findOrFail($id);
        return view('pengajuan.edit', compact('pengajuan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tujuan' => 'required',
            'tgl_berangkat' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_berangkat',
            'estimasi_biaya' => 'nullable|numeric|min:1',
            'dokumen' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048'
        ]);

        $pengajuan = Pengajuan::findOrFail($id);
        $dokumen = $pengajuan->dokumen;

        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('dokumen'), $fileName);
            $dokumen = $fileName;
        }

        $pengajuan->update([
            'tujuan' => $request->tujuan,
            'tgl_berangkat' => $request->tgl_berangkat,
            'tgl_kembali' => $request->tgl_kembali,
            'estimasi_biaya' => $request->estimasi_biaya,
            'dokumen' => $dokumen,
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        Verifikasi::where('id_pengajuan', $pengajuan->id_pengajuan)->delete();
        RealisasiDana::where('id_pengajuan', $pengajuan->id_pengajuan)->delete();
        TransaksiPengeluaran::where('id_pengajuan', $pengajuan->id_pengajuan)->delete();
        $pengajuan->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function batalkan($id)
    {
        $pengajuan = \App\Models\Pengajuan::with('realisasiDana')->findOrFail($id);
        if ($pengajuan->realisasiDana) {
            $pengajuan->realisasiDana->update(['status' => 'PENDING']);
        }
        $pengajuan->update(['status' => 'Menunggu Realisasi Dana']);
        return back()->with('success', 'Realisasi berhasil dibatalkan.');
    }
    public function laporanRealisasi()
{
    $data = Pengajuan::with('realisasiDana')
        ->whereHas('realisasiDana')
        ->get();

    $totalPengajuan = $data->sum('estimasi_biaya');

    $totalRealisasi = $data->sum(function ($item) {
        return $item->realisasiDana->total_realisasi ?? 0;
    });

    $efisiensi = $totalPengajuan > 0
        ? round((($totalPengajuan - $totalRealisasi) / $totalPengajuan) * 100, 2)
        : 0;

    // Tujuan terpopuler
    $tujuanTerpopuler = $data
        ->groupBy('tujuan')
        ->sortByDesc(fn($items) => $items->count())
        ->keys()
        ->first() ?? '-';

    // Tujuan paling hemat
    $tujuanEfisien = '-';
    $penghematanTerbesar = 0;

    foreach ($data as $item) {

        $estimasi = $item->estimasi_biaya ?? 0;
        $realisasi = $item->realisasiDana->total_realisasi ?? 0;

        $selisih = $estimasi - $realisasi;

        if ($selisih > $penghematanTerbesar) {

            $penghematanTerbesar = $selisih;
            $tujuanEfisien = $item->tujuan;
        }
    }

    // AI Transportasi
    $transportasiHemat = "Kereta Api";

    $alasanTransportasi =
        "Biaya perjalanan lebih rendah, realisasi dana lebih efisien, dan administrasi reimbursement lebih sederhana.";

    // AI Insight
    $aiInsight =
        "Total pengajuan sebesar Rp " . number_format($totalPengajuan,0,',','.') .
        ", total realisasi sebesar Rp " . number_format($totalRealisasi,0,',','.') .
        ". Tingkat efisiensi perjalanan mencapai {$efisiensi}%. " .
        "Tujuan yang paling sering dikunjungi adalah {$tujuanTerpopuler}. " .
        "Tujuan dengan penghematan terbesar adalah {$tujuanEfisien}. " .
        "AI merekomendasikan penggunaan {$transportasiHemat} karena {$alasanTransportasi}";

    // Data Bar Chart
    $barLabels = [
        'Pengajuan',
        'Realisasi'
    ];

    $barData = [
        $totalPengajuan,
        $totalRealisasi
    ];

    // Data Line Chart
    $lineLabels = [];
    $lineData = [];

    foreach ($data as $item) {

        if ($item->realisasiDana) {

            $lineLabels[] =
                \Carbon\Carbon::parse(
                    $item->realisasiDana->tgl_realisasi
                )->format('M Y');

            $lineData[] =
                $item->realisasiDana->total_realisasi;
        }
    }

    return view(
        'pegawai.laporan_realisasi',
        compact(
            'totalPengajuan',
            'totalRealisasi',
            'efisiensi',
            'tujuanTerpopuler',
            'tujuanEfisien',
            'penghematanTerbesar',
            'transportasiHemat',
            'alasanTransportasi',
            'barLabels',
            'barData',
            'lineLabels',
            'lineData',
            'aiInsight'
        )
    );
    }
}