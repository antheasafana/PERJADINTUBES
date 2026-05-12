@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        Verifikasi
    </h2>

    {{-- ====================================================== --}}
    {{-- VERIFIKASI PENGAJUAN --}}
    {{-- ====================================================== --}}

    <div class="card mb-5">

        <div class="card-header">
            <h4 class="mb-0">
                Verifikasi Pengajuan
            </h4>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tujuan</th>
                        <th>Jenis</th>
                        <th>Estimasi</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($pengajuan as $item)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->tujuan }}
                            </td>

                            <td>
                                {{ $item->jenis_pengajuan }}
                            </td>

                            <td>
                                Rp {{ number_format($item->estimasi_biaya,0,',','.') }}
                            </td>

                            <td>
                                {{ $item->status }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center">
                                Tidak ada verifikasi pengajuan
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- ====================================================== --}}
    {{-- VERIFIKASI PENGELUARAN --}}
    {{-- ====================================================== --}}

    <div class="card">

        <div class="card-header">
            <h4 class="mb-0">
                Verifikasi Pengeluaran
            </h4>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tujuan</th>
                        <th>Uraian</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($pengeluaran as $item)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->pengajuan->tujuan }}
                            </td>

                            <td>
                                {{ $item->uraian }}
                            </td>

                            <td>
                                Rp {{ number_format($item->nominal,0,',','.') }}
                            </td>

                            <td>
                                {{ $item->status }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center">
                                Tidak ada verifikasi pengeluaran
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection