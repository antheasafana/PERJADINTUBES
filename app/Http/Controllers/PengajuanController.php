<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\Verifikasi;
use App\Models\RealisasiDana;

class PengajuanController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function index()
    {
        $data = Pengajuan::all();

        return view('pengajuan.index', compact('data'));
    }

    public function create()
    {
        return view('pengajuan.create');
    }

    public function store(Request $request)
    {
        // VALIDASI
        $request->validate([
            'jenis_pengajuan' => 'required',
            'tujuan'          => 'required',
            'tgl_berangkat'   => 'required|date',
            'tgl_kembali'     => 'required|date',
            'estimasi_biaya'  => 'required|numeric',
            'dokumen'         => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048'
        ]);

        // DEFAULT FILE
        $fileName = null;

        // UPLOAD FILE
        if ($request->hasFile('dokumen')) {

            $file = $request->file('dokumen');

            $fileName = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('dokumen'), $fileName);
        }

        // SIMPAN PENGAJUAN
        $pengajuan = Pengajuan::create([
            'id_pegawai'          => auth()->id(),
            'jenis_pengajuan'     => $request->jenis_pengajuan,
            'id_pengajuan_parent' => null,
            'tujuan'              => $request->tujuan,
            'tgl_berangkat'       => $request->tgl_berangkat,
            'tgl_kembali'         => $request->tgl_kembali,
            'estimasi_biaya'      => $request->estimasi_biaya,
            'dokumen'             => $fileName,
            'status'              => 'Diajukan'
        ]);

        /*
        |--------------------------------------------------------------------------
        | FLOW LOGIC
        |--------------------------------------------------------------------------
        | UANG_MUKA
        | -> wajib verifikasi admin
        |
        | REIMBURSEMENT & PENGEMBALIAN
        | -> langsung masuk realisasi dana
        |--------------------------------------------------------------------------
        */

        if ($request->jenis_pengajuan == 'UANG_MUKA') {

            // MASUK VERIFIKASI ADMIN
            Verifikasi::create([
                'id_pengajuan'       => $pengajuan->id_pengajuan,
                'admin_id'           => 1,
                'level'              => 1,
                'status'             => 'pending',
                'tanggal_verifikasi' => null,
            ]);

        } else {

            // LANGSUNG REALISASI DANA
            RealisasiDana::create([
                'id_pengajuan'       => $pengajuan->id_pengajuan,
                'id_jenis_transaksi' => 1,
                'tgl_realisasi'      => now(),
                'total_realisasi'    => $request->estimasi_biaya,
            ]);
        }

        // REDIRECT
        return redirect('/pengajuan')
            ->with('success', 'Pengajuan berhasil dikirim!');
    }
}