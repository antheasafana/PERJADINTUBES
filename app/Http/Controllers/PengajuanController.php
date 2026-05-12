<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\RealisasiDana;
use App\Models\TransaksiPengeluaran;
use App\Models\Verifikasi;

class PengajuanController extends Controller
{
    public function dashboard()
    {
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

        return view('pengajuan.dashboard', compact(
            'pengajuanRealisasi',
            'pengeluaranTerbaru',
            'totalPengajuanRealisasi',
            'totalPengeluaran'
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
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'jenis_pengajuan' => 'required',
            'tujuan' => 'required',
            'tgl_berangkat' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_berangkat',
            'estimasi_biaya' => 'required|numeric|min:1',
            'dokumen' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048'
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD FILE
        |--------------------------------------------------------------------------
        */

        $fileName = null;

        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');

            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('dokumen'), $fileName);
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengajuan = Pengajuan::create([
            'id_pegawai' => 1,
            'jenis_pengajuan' => $request->jenis_pengajuan,
            'id_pengajuan_parent' => null,
            'tujuan' => $request->tujuan,
            'tgl_berangkat' => $request->tgl_berangkat,
            'tgl_kembali' => $request->tgl_kembali,
            'estimasi_biaya' => $request->estimasi_biaya,
            'dokumen' => $fileName,
            'status' => 'Diajukan'
        ]);

        /*
        |--------------------------------------------------------------------------
        | FLOW PENGAJUAN
        |--------------------------------------------------------------------------
        |
        | UANG_MUKA:
        | Masuk ke verifikasi dulu.
        |
        | REIMBURSEMENT / PENGEMBALIAN:
        | Langsung dibuat realisasi dana, sehingga tombol Input Pengeluaran
        | bisa muncul di halaman Pengajuan Saya.
        |
        */

        if ($request->jenis_pengajuan == 'UANG_MUKA') {
            Verifikasi::create([
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'admin_id' => 1,
                'level' => 1,
                'status' => 'pending',
                'tanggal_verifikasi' => null,
            ]);
        } else {
            RealisasiDana::create([
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'tgl_realisasi' => now(),
                'total_realisasi' => $request->estimasi_biaya,
            ]);

            $pengajuan->update([
                'status' => 'Direalisasikan'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()->route('pengajuan.index')
            ->with('success', 'Pengajuan berhasil dikirim!');
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran',
                'verifikasi',
                'detailBiaya'
            ])
            ->findOrFail($id);

        return view('pengajuan.show', compact('pengajuan'));
    }

    public function edit($id)
    {
        $pengajuan = Pengajuan::with('realisasiDana')
            ->findOrFail($id);

        return view('pengajuan.edit', compact('pengajuan'));
    }

    public function update(Request $request, $id)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'tujuan' => 'required',
            'tgl_berangkat' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_berangkat',
            'estimasi_biaya' => 'nullable|numeric|min:1',
            'dokumen' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048'
        ]);

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA
        |--------------------------------------------------------------------------
        */

        $pengajuan = Pengajuan::findOrFail($id);

        $dokumen = $pengajuan->dokumen;

        /*
        |--------------------------------------------------------------------------
        | UPLOAD FILE BARU
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');

            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('dokumen'), $fileName);

            $dokumen = $fileName;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE DATA
        |--------------------------------------------------------------------------
        */

        $pengajuan->update([
            'tujuan' => $request->tujuan,
            'tgl_berangkat' => $request->tgl_berangkat,
            'tgl_kembali' => $request->tgl_kembali,
            'estimasi_biaya' => $request->estimasi_biaya,
            'dokumen' => $dokumen,
        ]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()->route('pengajuan.index')
            ->with('success', 'Pengajuan berhasil diperbarui!');
    }
}