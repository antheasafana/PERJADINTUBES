<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\Pegawai;
use App\Models\RealisasiDana;
use App\Models\TransaksiPengeluaran;
use App\Models\Verifikasi;

use App\Services\PerjadinDocumentService;

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

            $fileName =
                time() . '_' .
                uniqid() . '.' .
                $file->getClientOriginalExtension();

            $file->move(
                public_path('dokumen'),
                $fileName
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA PEGAWAI
        |--------------------------------------------------------------------------
        */

        $pegawai = Pegawai::find(1);

        if (!$pegawai) {

            return redirect()->back()
                ->with(
                    'error',
                    'Data pegawai tidak ditemukan'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengajuan = Pengajuan::create([

            'id_pegawai' => $pegawai->id,

            'jenis_pengajuan' =>
                $request->jenis_pengajuan,

            'id_pengajuan_parent' => null,

            'tujuan' => $request->tujuan,

            'tgl_berangkat' =>
                $request->tgl_berangkat,

            'tgl_kembali' =>
                $request->tgl_kembali,

            'estimasi_biaya' =>
                $request->estimasi_biaya,

            'dokumen' => $fileName,

            'status' => 'Diajukan'
        ]);

        /*
        |--------------------------------------------------------------------------
        | FLOW PENGAJUAN
        |--------------------------------------------------------------------------
        */

        if (
            $request->jenis_pengajuan
            == 'UANG_MUKA'
        ) {

            /*
            |--------------------------------------------------------------------------
            | MASUK KE VERIFIKASI DULU
            |--------------------------------------------------------------------------
            */

            Verifikasi::create([

                'id_pengajuan' =>
                    $pengajuan->id_pengajuan,

                'admin_id' => 1,

                'level' => 1,

                'status' => 'pending',

                'tanggal_verifikasi' => null,
            ]);

        } else {

            /*
            |--------------------------------------------------------------------------
            | REIMBURSEMENT / PENGEMBALIAN
            | PLACEHOLDER REALISASI — PEGAWAI ISI NOMINAL AKTUAL
            |--------------------------------------------------------------------------
            */

            RealisasiDana::create([
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'tgl_realisasi' => now(),
                'total_realisasi' => 0,
                'status' => 'PENDING',
            ]);

            $pengajuan->update([
                'status' => 'Menunggu Realisasi Dana',
            ]);
        }

        $pengajuan->load('pegawai');
        PerjadinDocumentService::sendPengajuanBerhasilEmail($pengajuan);

   auth()->user()->notifications()->create([

    'id' => \Str::uuid(),

    'type' => 'pengajuan',

    'data' => [
        'title' => 'Pengajuan Berhasil',
        'body'  => 'Pengajuan #' . $pengajuan->id_pengajuan . ' (' . $pengajuan->tujuan . ') berhasil diajukan. Silakan lanjut isi realisasi dana.',
    ],
]);

        return redirect()
            ->route('pengajuan.index')
            ->with(
                'success',
                'Pengajuan berhasil dikirim. Notifikasi email telah dikirim ke pegawai.'
            );
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
        $pengajuan = Pengajuan::with('realisasiDana')
            ->findOrFail($id);

        if (! in_array($pengajuan->jenis_pengajuan, ['REIMBURSEMENT', 'PENGEMBALIAN'], true)) {
            return redirect()
                ->route('pengajuan.index')
                ->with('error', 'Realisasi dana hanya untuk reimbursement atau pengembalian.');
        }

        if (
            ! $pengajuan->realisasiDana
            || $pengajuan->realisasiDana->status === 'TEREALISASI'
        ) {
            return redirect()
                ->route('realisasi.index')
                ->with('info', 'Realisasi dana sudah diinput.');
        }

        return view('pengajuan.realisasi', compact('pengajuan'));
    }

    public function realisasiStore(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('realisasiDana')
            ->findOrFail($id);

        if (! in_array($pengajuan->jenis_pengajuan, ['REIMBURSEMENT', 'PENGEMBALIAN'], true)) {
            return redirect()
                ->route('pengajuan.index')
                ->with('error', 'Realisasi dana hanya untuk reimbursement atau pengembalian.');
        }

        $request->validate([
            'total_realisasi' => 'required|numeric|min:1',
            'tgl_realisasi' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $realisasi = $pengajuan->realisasiDana;

        if (! $realisasi || $realisasi->status === 'TEREALISASI') {
            return redirect()
                ->route('realisasi.index')
                ->with('info', 'Realisasi dana sudah diinput.');
        }

        $realisasi->update([
            'total_realisasi' => $request->total_realisasi,
            'tgl_realisasi' => $request->tgl_realisasi,
            'catatan' => $request->catatan,
            'status' => 'TEREALISASI',
        ]);

        $pengajuan->update([
            'status' => 'Direalisasikan',
        ]);
    auth()->user()->notifications()->create([

    'id' => \Str::uuid(),

    'type' => 'realisasi',

    'data' => [
        'title' => 'Realisasi Dana',
        'body'  => 'Pengajuan #' . $pengajuan->id_pengajuan . ' sudah direalisasikan sebesar Rp ' . number_format($request->total_realisasi, 0, ',', '.'),
    ],

]);
        return redirect()
            ->route('realisasi.index')
            ->with(
                'success',
                'Realisasi dana berhasil disimpan. Silakan lanjut ke Transaksi Pengeluaran.'
            );
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

        return view(
            'pengajuan.show',
            compact('pengajuan')
        );
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
            return redirect()
                ->back()
                ->with('error', 'Belum ada data realisasi dana.');
        }

        $pdf = PerjadinDocumentService::realisasiDanaPdf($pengajuan->realisasiDana);

        return $pdf->download('realisasi-dana-' . $pengajuan->id_pengajuan . '.pdf');
    }

    public function edit($id)
    {
        $pengajuan = Pengajuan::with(
                'realisasiDana'
            )
            ->findOrFail($id);

        return view(
            'pengajuan.edit',
            compact('pengajuan')
        );
    }

    public function update(
        Request $request,
        $id
    ) {

        $request->validate([

            'tujuan' => 'required',

            'tgl_berangkat' =>
                'required|date',

            'tgl_kembali' =>
                'required|date|after_or_equal:tgl_berangkat',

            'estimasi_biaya' =>
                'nullable|numeric|min:1',

            'dokumen' =>
                'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048'
        ]);

        $pengajuan =
            Pengajuan::findOrFail($id);

        $dokumen =
            $pengajuan->dokumen;

        if ($request->hasFile('dokumen')) {

            $file = $request->file('dokumen');

            $fileName =
                time() . '_' .
                uniqid() . '.' .
                $file->getClientOriginalExtension();

            $file->move(
                public_path('dokumen'),
                $fileName
            );

            $dokumen = $fileName;
        }

        $pengajuan->update([

            'tujuan' =>
                $request->tujuan,

            'tgl_berangkat' =>
                $request->tgl_berangkat,

            'tgl_kembali' =>
                $request->tgl_kembali,

            'estimasi_biaya' =>
                $request->estimasi_biaya,

            'dokumen' => $dokumen,
        ]);

        return redirect()
            ->route('pengajuan.index')
            ->with(
                'success',
                'Pengajuan berhasil diperbarui!'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | HAPUS RELASI
        |--------------------------------------------------------------------------
        */

        Verifikasi::where(
            'id_pengajuan',
            $pengajuan->id_pengajuan
        )->delete();

        RealisasiDana::where(
            'id_pengajuan',
            $pengajuan->id_pengajuan
        )->delete();

        TransaksiPengeluaran::where(
            'id_pengajuan',
            $pengajuan->id_pengajuan
        )->delete();

        /*
        |--------------------------------------------------------------------------
        | HAPUS PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengajuan->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Data berhasil dihapus'
            );
    }

   public function batalkan($id)
{
    $pengajuan = \App\Models\Pengajuan::with('realisasiDana')
        ->findOrFail($id);

    if ($pengajuan->realisasiDana) {

        $pengajuan->realisasiDana->update([
            'status' => 'PENDING'
        ]);
    }

    $pengajuan->update([
        'status' => 'Menunggu Realisasi Dana'
    ]);

    return back()->with('success', 'Realisasi berhasil dibatalkan.');
}
}