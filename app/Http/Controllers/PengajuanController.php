<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\Pengajuan;
use App\Models\Pegawai;
use App\Models\RealisasiDana;
use App\Models\TransaksiPengeluaran;
use App\Models\Verifikasi;

use App\Mail\TesMail;

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
        | AMBIL DATA PEGAWAI
        |--------------------------------------------------------------------------
        */
        $pegawai = Pegawai::find(1);

        if (!$pegawai) {
            return redirect()->back()
                ->with('error', 'Data pegawai tidak ditemukan');
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN PENGAJUAN
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | KIRIM EMAIL OTOMATIS
        |--------------------------------------------------------------------------
        */
        try {
            if ($pegawai->email) {
                Mail::to($pegawai->email)
                    ->send(new TesMail($pengajuan));
            }
        } catch (\Exception $e) {
            Log::error('Email gagal dikirim: ' . $e->getMessage());
        }

        /*
        |--------------------------------------------------------------------------
        | FLOW PENGAJUAN
        |--------------------------------------------------------------------------
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
            ->with('success', 'Pengajuan berhasil dikirim & email berhasil dikirim!');
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran',
                'verifikasi',
        
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

        return redirect()->route('pengajuan.index')
            ->with('success', 'Pengajuan berhasil diperbarui!');
    }
}