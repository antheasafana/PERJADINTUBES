<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran::with([

                'pengajuan',
                'transaksiPengeluaran'

            ])
            ->latest()
            ->get();

        return view(
            'pembayaran.index',
            compact('pembayaran')
        );
    }
}