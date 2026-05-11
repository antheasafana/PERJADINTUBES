<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\akun;
use App\Models\KategoriBiaya;
use App\Models\Pengajuan;
use App\Models\TransaksiPengeluaran;
use App\Models\Pembayaran;

class TransaksiPengeluaranController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    | Menampilkan:
    | 1. Pengajuan yang sudah direalisasi dana
    | 2. Daftar transaksi pengeluaran
    */

    public function index()
    {
        $pengajuanRealisasi = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran'
            ])
            ->whereHas('realisasiDana')
            ->latest()
            ->get();

        $pengeluaran = TransaksiPengeluaran::with([
                'pengajuan',
                'kategoriBiaya',
                'akun'
            ])
            ->latest()
            ->get();

        return view(
            'pengeluaran.index',
            compact(
                'pengajuanRealisasi',
                'pengeluaran'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    | Form input pengeluaran
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
        | VALIDASI REALISASI DANA
        |--------------------------------------------------------------------------
        */

        if (!$pengajuan->realisasiDana) {

            return redirect()
                ->route('pengeluaran.index')
                ->with(
                    'error',
                    'Pengajuan belum direalisasi dana.'
                );
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
    | Pegawai input pengeluaran
    | Otomatis masuk verifikasi admin
    */

    public function store(Request $request, $id_pengajuan)
    {
        $pengajuan = Pengajuan::with(
                'realisasiDana'
            )
            ->findOrFail($id_pengajuan);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI REALISASI
        |--------------------------------------------------------------------------
        */

        if (!$pengajuan->realisasiDana) {

            return redirect()
                ->route('pengeluaran.index')
                ->with(
                    'error',
                    'Pengajuan belum direalisasi dana.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI INPUT
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'tanggal_pengeluaran' =>
                'required|date',

            'id_kategori' =>
                'required|array',

            'id_kategori.*' =>
                'required|exists:KategoriBiaya,id_kategori',

            'id_akun' =>
                'nullable|array',

            'id_akun.*' =>
                'nullable|exists:akuns,id',

            'uraian' =>
                'required|array',

            'uraian.*' =>
                'required|string|max:255',

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
        | SIMPAN PENGELUARAN
        |--------------------------------------------------------------------------
        */

        foreach ($request->uraian as $index => $uraian) {

            $bukti = null;

            /*
            |--------------------------------------------------------------------------
            | UPLOAD FILE
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
            | CREATE TRANSAKSI
            |--------------------------------------------------------------------------
            */

            TransaksiPengeluaran::create([

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

                /*
                |--------------------------------------------------------------------------
                | STATUS AWAL
                |--------------------------------------------------------------------------
                */

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
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS PENGAJUAN
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
    | SHOW
    |--------------------------------------------------------------------------
    | Detail transaksi pengeluaran
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
    | VERIFIKASI
    |--------------------------------------------------------------------------
    | POV ADMIN
    | Admin verifikasi pengeluaran
    */

    public function verifikasi(
        Request $request,
        $id
    ) {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'aksi' =>
                'required|in:setuju,tolak',

            'catatan_verifikasi' =>
                'nullable|string',
        ]);

        $pengeluaran =
            TransaksiPengeluaran::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $pengeluaran->status !=
            'verifikasi_pengeluaran'
        ) {

            return back()->with(
                'error',
                'Pengeluaran tidak dalam tahap verifikasi.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA DITOLAK
        |--------------------------------------------------------------------------
        */

        if ($request->aksi === 'tolak') {

            $pengeluaran->update([

                'status' =>
                    'ditolak',

                'catatan_verifikasi' =>
                    $request->catatan_verifikasi,

                'tanggal_verifikasi' =>
                    now(),
            ]);

            return redirect()
                ->route(
                    'pengeluaran.show',
                    $pengeluaran->id_transaksi_pengeluaran
                )
                ->with(
                    'success',
                    'Pengeluaran berhasil ditolak.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA DISETUJUI
        |--------------------------------------------------------------------------
        */

        $pengeluaran->update([

            'status' =>
                'pembayaran',

            'catatan_verifikasi' =>
                $request->catatan_verifikasi,

            'tanggal_verifikasi' =>
                now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengeluaran->pengajuan->update([

            'status' =>
                'Pembayaran'
        ]);

        return redirect()
            ->route(
                'pengeluaran.show',
                $pengeluaran->id_transaksi_pengeluaran
            )
            ->with(
                'success',
                'Pengeluaran berhasil diverifikasi admin dan masuk tahap pembayaran.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBAYARAN
    |--------------------------------------------------------------------------
    | POV ADMIN
    | Admin melakukan pembayaran
    */

    public function pembayaran($id)
    {
        $pengeluaran =
            TransaksiPengeluaran::with('pengajuan')
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $pengeluaran->status !=
            'pembayaran'
        ) {

            return redirect()
                ->route(
                    'pengeluaran.show',
                    $pengeluaran->id_transaksi_pengeluaran
                )
                ->with(
                    'error',
                    'Transaksi belum masuk tahap pembayaran.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KE TABEL PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        Pembayaran::create([

            'id_pengajuan' =>
                $pengeluaran->id_pengajuan,

            'id_transaksi_pengeluaran' =>
                $pengeluaran->id_transaksi_pengeluaran,

            'jenis_pembayaran' =>
                'reimbursement',

            'arah_transaksi' =>
                'admin_ke_pegawai',

            'nominal' =>
                $pengeluaran->nominal,

            'status' =>
                'dibayar',

            'tanggal_pembayaran' =>
                now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS TRANSAKSI
        |--------------------------------------------------------------------------
        */

        $pengeluaran->update([

            'status' =>
                'transaksi_tercatat',

            'tanggal_pembayaran' =>
                now(),

            'tanggal_tercatat' =>
                now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengeluaran->pengajuan->update([

            'status' =>
                'Transaksi Tercatat'
        ]);

        return redirect()
            ->route(
                'pengeluaran.show',
                $pengeluaran->id_transaksi_pengeluaran
            )
            ->with(
                'success',
                'Pembayaran berhasil dilakukan admin.'
            );
    }
}