<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\akun;
use App\Models\KategoriBiaya;
use App\Models\Pengajuan;
use App\Models\TransaksiPengeluaran;

class TransaksiPengeluaranController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    | Menampilkan:
    | 1. Pengajuan yang sudah direalisasi dana
    | 2. Daftar transaksi pengeluaran yang sudah diinput
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

        return view('pengeluaran.index', compact(
            'pengajuanRealisasi',
            'pengeluaran'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    | Form input pengeluaran berdasarkan pengajuan yang sudah realisasi dana.
    */

    public function create($id_pengajuan)
    {
        $pengajuan = Pengajuan::with([
                'realisasiDana',
                'transaksiPengeluaran'
            ])
            ->findOrFail($id_pengajuan);

        if (!$pengajuan->realisasiDana) {
            return redirect()->route('pengeluaran.index')
                ->with('error', 'Pengajuan belum direalisasi dana, belum bisa input pengeluaran.');
        }

        $kategori = KategoriBiaya::orderBy('jenis_biaya', 'asc')->get();
        $akun = akun::orderBy('nama_akun', 'asc')->get();

        return view('pengeluaran.create', compact(
            'pengajuan',
            'kategori',
            'akun'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    | Simpan banyak pengeluaran sekaligus.
    | 1 pengajuan bisa memiliki banyak transaksi pengeluaran.
    */

    public function store(Request $request, $id_pengajuan)
    {
        $pengajuan = Pengajuan::with('realisasiDana')
            ->findOrFail($id_pengajuan);

        if (!$pengajuan->realisasiDana) {
            return redirect()->route('pengeluaran.index')
                ->with('error', 'Pengajuan belum direalisasi dana.');
        }

        $request->validate([
            'tanggal_pengeluaran' => 'required|date',

            'id_kategori' => 'required|array',
            'id_kategori.*' => 'required|exists:KategoriBiaya,id_kategori',

            'id_akun' => 'nullable|array',
            'id_akun.*' => 'nullable|exists:akuns,id',

            'uraian' => 'required|array',
            'uraian.*' => 'required|string|max:255',

            'nominal' => 'required|array',
            'nominal.*' => 'required|numeric|min:1',

            'bukti' => 'nullable|array',
            'bukti.*' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048',
        ]);

        foreach ($request->uraian as $index => $uraian) {
            $bukti = null;

            if ($request->hasFile("bukti.$index")) {
                $file = $request->file("bukti.$index");

                $bukti = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('bukti_pengeluaran'), $bukti);
            }

            TransaksiPengeluaran::create([
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'id_kategori' => $request->id_kategori[$index],
                'id_akun' => $request->id_akun[$index] ?? null,
                'jenis_pengeluaran' => $pengajuan->jenis_pengajuan,
                'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
                'uraian' => $uraian,
                'nominal' => $request->nominal[$index],
                'bukti' => $bukti,
                'status' => 'verifikasi_pengeluaran',
                'catatan_verifikasi' => null,
                'tanggal_verifikasi' => null,
                'tanggal_pembayaran' => null,
                'tanggal_tercatat' => null,
            ]);
        }

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Semua pengeluaran berhasil diinput dan menunggu verifikasi admin.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    | Detail transaksi pengeluaran.
    */

    public function show($id)
    {
        $pengeluaran = TransaksiPengeluaran::with([
                'pengajuan',
                'kategoriBiaya',
                'akun'
            ])
            ->findOrFail($id);

        return view('pengeluaran.show', compact('pengeluaran'));
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFIKASI
    |--------------------------------------------------------------------------
    | Verifikasi pengeluaran.
    | Jika disetujui, lanjut ke pembayaran.
    | Jika ditolak, status menjadi ditolak.
    */

    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'aksi' => 'required|in:setuju,tolak',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $pengeluaran = TransaksiPengeluaran::findOrFail($id);

        if ($request->aksi === 'tolak') {
            $pengeluaran->update([
                'status' => 'ditolak',
                'catatan_verifikasi' => $request->catatan_verifikasi,
                'tanggal_verifikasi' => now(),
            ]);

            return redirect()->route('pengeluaran.show', $pengeluaran->id_transaksi_pengeluaran)
                ->with('success', 'Transaksi pengeluaran ditolak.');
        }

        $pengeluaran->update([
            'status' => 'pembayaran',
            'catatan_verifikasi' => $request->catatan_verifikasi,
            'tanggal_verifikasi' => now(),
        ]);

        return redirect()->route('pengeluaran.show', $pengeluaran->id_transaksi_pengeluaran)
            ->with('success', 'Transaksi pengeluaran berhasil diverifikasi dan masuk tahap pembayaran.');
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBAYARAN
    |--------------------------------------------------------------------------
    | Setelah pembayaran selesai, status menjadi transaksi tercatat.
    */

    public function pembayaran($id)
    {
        $pengeluaran = TransaksiPengeluaran::findOrFail($id);

        if ($pengeluaran->status !== 'pembayaran') {
            return redirect()->route('pengeluaran.show', $pengeluaran->id_transaksi_pengeluaran)
                ->with('error', 'Transaksi belum bisa dibayar karena belum selesai verifikasi.');
        }

        $pengeluaran->update([
            'status' => 'transaksi_tercatat',
            'tanggal_pembayaran' => now(),
            'tanggal_tercatat' => now(),
        ]);

        return redirect()->route('pengeluaran.show', $pengeluaran->id_transaksi_pengeluaran)
            ->with('success', 'Pembayaran selesai. Transaksi pengeluaran sudah tercatat.');
    }
}