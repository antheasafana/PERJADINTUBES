<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;

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
        $request->validate([
            'jenis_pengajuan' => 'required',
            'tujuan' => 'required',
            'tgl_berangkat' => 'required',
            'tgl_kembali' => 'required',
            'estimasi_biaya' => 'required'
        ]);

        $fileName = null;

        if ($request->hasFile('dokumen')) {
            $fileName = time().'.'.$request->dokumen->extension();
            $request->dokumen->move(public_path('dokumen'), $fileName);
        }

    Pengajuan::create([
    'id_pegawai' => 1,
    'jenis_pengajuan' => $request->jenis_pengajuan,
    'id_pengajuan_parent' => null,
    'tujuan' => $request->tujuan,
    'tgl_berangkat' => $request->tgl_berangkat,
    'tgl_kembali' => $request->tgl_kembali,
    'estimasi_biaya' => $request->estimasi_biaya,
    'dokumen' => null,
    'status' => 'Diajukan'
]);

        return redirect('/pengajuan')
            ->with('success', 'Pengajuan berhasil dikirim!');
    }
}