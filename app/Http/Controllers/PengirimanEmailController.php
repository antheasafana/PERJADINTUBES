<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// tambahan untuk akses ke model
use App\Models\PengirimanEmail;
use Filament\Notifications\Notification;

// tambahan library
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanEmailController extends Controller
{
    public static function kirim_email_realisasi(){

        // 1. Query data realisasi dana
        $data = DB::table('realisasi_dana')

                ->join(
                    'pengajuan',
                    'realisasi_dana.id_pengajuan',
                    '=',
                    'pengajuan.id_pengajuan'
                )

                ->where(
                    'realisasi_dana.status',
                    'TEREALISASI'
                )

                ->whereNotIn(
                    'realisasi_dana.id_realisasi',
                    function ($query) {

                        $query->select('id_realisasi')
                            ->from('pengiriman_emails');
                    }
                )

                ->select(
                    'realisasi_dana.id_realisasi',
                    'pengajuan.tujuan',
                    'pengajuan.jenis_pengajuan',
                    'realisasi_dana.total_realisasi',
                    'realisasi_dana.tgl_realisasi'
                )

                ->first();

        // 2. Jika ada data
                if ($data) {

                    $id = $data->id_realisasi;

                    $tujuan = $data->tujuan;

                    $jenis_pengajuan = $data->jenis_pengajuan;

                    $total_realisasi = $data->total_realisasi;

                    $tgl_realisasi = $data->tgl_realisasi;

                    // generate pdf
                    $pdf = Pdf::loadView('pdf.realisasidana', [

                        'realisasi' => $data,

                    ]);

                    // data atribut
                    $dataAtributPelanggan = [

                        'tujuan' => $tujuan,

                        'jenis_pengajuan' => $jenis_pengajuan,

                        'total_realisasi' => $total_realisasi,

                        'tgl_realisasi' => $tgl_realisasi,
                    ];

                    // =========================
                    // EMAIL (TETAP SEPERTI ASLI)
                    // =========================
                    try {

                        Mail::to('test@gmail.com')->send(
                            new InvoiceMail(
                                $dataAtributPelanggan,
                                $pdf->output()
                            )
                        );

                    } catch (\Exception $e) {

                        dd('ERROR: ' . $e->getMessage());
                    }

                    // =========================
                    // NOTIFIKASI FILAMENT (BARU)
                    // =========================
                    
                    $user = User::find(1); // admin kamu
                    \Filament\Notifications\Notification::make()
                        ->title('Realisasi Dana Terkirim')
                        ->body('Tujuan: ' . $tujuan . ' | Total: ' . $total_realisasi)
                        ->success()
                        ->sendToDatabase($user);

                    // simpan log email
                    PengirimanEmail::create([

                        'id_realisasi' => $id,

                        'email' => 'test@gmail.com',

                        'status_kirim' => true,
                            ]);
                        }

        // autorefresh
        return view('autorefresh_email');
    }
}