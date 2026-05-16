<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\akun;
use App\Models\KategoriBiaya;
use App\Models\Pengajuan;
use App\Models\TransaksiPengeluaran;
use App\Models\Verifikasi;

class TransaksiPengeluaranController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX PEGAWAI
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $pengajuanSiapPengeluaran = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran',
            ])
            ->whereIn('jenis_pengajuan', ['REIMBURSEMENT', 'PENGEMBALIAN'])
            ->whereHas('realisasiDana', function ($query) {
                $query->where('status', 'TEREALISASI')
                    ->where('total_realisasi', '>', 0);
            })
            ->latest()
            ->get();

        $pengeluaran = TransaksiPengeluaran::with([
                'pengajuan',
                'kategoriBiaya',
                'akun',
            ])
            ->latest()
            ->get();

        return view(
            'pengeluaran.index',
            compact('pengajuanSiapPengeluaran', 'pengeluaran')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create($id_pengajuan)
    {
        $pengajuan = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran'
            ])
            ->findOrFail($id_pengajuan);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI REALISASI
        |--------------------------------------------------------------------------
        */

        if (! $pengajuan->realisasiDana) {
            return redirect()
                ->route('realisasi.index')
                ->with('error', 'Silakan input realisasi dana terlebih dahulu.');
        }

        if (
            $pengajuan->realisasiDana->status !== 'TEREALISASI'
            || $pengajuan->realisasiDana->total_realisasi <= 0
        ) {
            return redirect()
                ->route('pengajuan.realisasi', $id_pengajuan)
                ->with('error', 'Silakan lengkapi realisasi dana sebelum input pengeluaran.');
        }

        if ($pengajuan->status == 'Transaksi Tercatat') {
            return redirect()
                ->route('pengeluaran.index')
                ->with('error', 'Pengajuan sudah selesai.');
        }

        $kategori = KategoriBiaya::orderBy(
            'jenis_biaya',
            'asc'
        )->get();

        $akun = akun::orderBy(
            'nama_akun',
            'asc'
        )->get();

        return view(
            'pengeluaran.create',
            compact(
                'pengajuan',
                'kategori',
                'akun'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, $id_pengajuan)
    {
        $pengajuan = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran'
            ])
            ->findOrFail($id_pengajuan);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI REALISASI
        |--------------------------------------------------------------------------
        */

        if (! $pengajuan->realisasiDana) {
            return redirect()
                ->route('realisasi.index')
                ->with('error', 'Silakan input realisasi dana terlebih dahulu.');
        }

        if (
            $pengajuan->realisasiDana->status !== 'TEREALISASI'
            || $pengajuan->realisasiDana->total_realisasi <= 0
        ) {
            return redirect()
                ->route('pengajuan.realisasi', $id_pengajuan)
                ->with('error', 'Silakan lengkapi realisasi dana sebelum input pengeluaran.');
        }

        $request->validate([

            'tanggal_pengeluaran' =>
                'required|date',

            'id_kategori' =>
                'required|array',

            /*
            |--------------------------------------------------------------------------
            | PENTING
            |--------------------------------------------------------------------------
            | GANTI nama tabel sesuai database asli
            | Kalau tabel kamu "KategoriBiaya"
            | maka gunakan:
            |
            | exists:KategoriBiaya,id_kategori
            |--------------------------------------------------------------------------
            */

            'id_kategori.*' =>
                'required|exists:KategoriBiaya,id_kategori',

            'id_akun' =>
                'nullable|array',

            'id_akun.*' =>
                'nullable|exists:akuns,id',

            'uraian' =>
                'required|array',

            'uraian.*' =>
                'required|string|min:3|max:255',

            'nominal' =>
                'required|array',

            'nominal.*' =>
                'required|numeric|min:1',

            'bukti' =>
                'nullable|array',

            'bukti.*' =>
                'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI TOTAL
        |--------------------------------------------------------------------------
        */

        $totalInput = array_sum($request->nominal);

        $totalRealisasi =
            $pengajuan->realisasiDana->total_realisasi;

        $totalSebelumnya =
            $pengajuan->transaksiPengeluaran
                ->sum('nominal');

        $sisaDana =
            $totalRealisasi - $totalSebelumnya;

        if ($totalInput > $sisaDana) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Total pengeluaran melebihi sisa realisasi dana.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN TRANSAKSI
        |--------------------------------------------------------------------------
        */

        foreach ($request->uraian as $index => $uraian) {

            $bukti = null;

            /*
            |--------------------------------------------------------------------------
            | UPLOAD BUKTI
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile("bukti.$index")) {

                $file = $request->file("bukti.$index");

                $bukti = time()
                    . '_'
                    . uniqid()
                    . '.'
                    . $file->getClientOriginalExtension();

                $file->move(
                    public_path('bukti_pengeluaran'),
                    $bukti
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE PENGELUARAN
            |--------------------------------------------------------------------------
            */

            $pengeluaran = TransaksiPengeluaran::create([

                'id_pengajuan' =>
                    $pengajuan->id_pengajuan,

                'id_kategori' =>
                    $request->id_kategori[$index],

                'id_akun' =>
                    $request->id_akun[$index] ?? null,

                'jenis_pengeluaran' =>
                    $pengajuan->jenis_pengajuan,

                'tanggal_pengeluaran' =>
                    $request->tanggal_pengeluaran,

                'uraian' =>
                    $uraian,

                'nominal' =>
                    $request->nominal[$index],

                'bukti' =>
                    $bukti,

                'status' =>
                    'verifikasi_pengeluaran',

                'catatan_verifikasi' =>
                    null,

                'tanggal_verifikasi' =>
                    null,

                'tanggal_pembayaran' =>
                    null,

                'tanggal_tercatat' =>
                    null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | CREATE VERIFIKASI
            |--------------------------------------------------------------------------
            */

            Verifikasi::create([

                'id_pengajuan' =>
                    $pengajuan->id_pengajuan,

                'id_transaksi_pengeluaran' =>
                    $pengeluaran->id_transaksi_pengeluaran,

                'admin_id' =>
                    1,

                'level' =>
                    1,

                'status' =>
                    'pending',

                'catatan' =>
                    null,

                'alasan_reject' =>
                    null,

                'tanggal_verifikasi' =>
                    null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        $pengajuan->update([

            'status' =>
                'Verifikasi Pengeluaran'
        ]);

        return redirect()
            ->route('pengeluaran.index')
            ->with(
                'success',
                'Pengeluaran berhasil diinput dan menunggu verifikasi admin.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW PEGAWAI
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $pengeluaran = TransaksiPengeluaran::with([
                'pengajuan',
                'kategoriBiaya',
                'akun'
            ])
            ->findOrFail($id);

        return view(
            'pengeluaran.show',
            compact('pengeluaran')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW ADMIN
    |--------------------------------------------------------------------------
    */

    public function showAdmin($id)
    {
        $pengeluaran = TransaksiPengeluaran::with([
                'pengajuan.pegawai',
                'kategoriBiaya',
                'akun',
                'pembayaran'
            ])
            ->findOrFail($id);

        return view(
            'admin.pengeluaran.show',
            compact('pengeluaran')
        );
    }
}