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
        $data = Pengajuan::latest()->get();

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
            'tgl_berangkat' => 'required|date',
            'tgl_kembali' => 'required|date',
            'estimasi_biaya' => 'required|numeric',

            'dokumen' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048'
        ]);

        $fileName = null;

        // upload file
        if ($request->hasFile('dokumen')) {

            $file = $request->file('dokumen');

            $fileName = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('dokumen'), $fileName);
        }

        // simpan pengajuan
        Pengajuan::create([

            'id_pegawai' => 1,

            'jenis_pengajuan' => $request->jenis_pengajuan,

            'id_pengajuan_parent' => null,

            'tujuan' => $request->tujuan,

            'tgl_berangkat' => $request->tgl_berangkat,

            'tgl_kembali' => $request->tgl_kembali,

            'estimasi_biaya' => $request->estimasi_biaya,

            // simpan sebagai JSON
            'dokumen' => [
                'file' => $fileName
            ],

            'status' => 'Diajukan'
        ]);

        return redirect('/pengajuan')
            ->with('success', 'Pengajuan berhasil dikirim!');
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        return view('pengajuan.show', compact('pengajuan'));
    }

    public function edit($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        return view('pengajuan.edit', compact('pengajuan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tujuan' => 'required',
            'tgl_berangkat' => 'required|date',
            'tgl_kembali' => 'required|date',
            'estimasi_biaya' => 'nullable|numeric',

            'dokumen' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048'
        ]);

        $pengajuan = Pengajuan::findOrFail($id);

        $dokumen = $pengajuan->dokumen ?? [];

        // upload file baru
        if ($request->hasFile('dokumen')) {

            $file = $request->file('dokumen');

            $fileName = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('dokumen'), $fileName);

            $dokumen['file'] = $fileName;
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