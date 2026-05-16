@extends('layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between mb-3">

        <h4>
            Rekapan Pembayaran
        </h4>

    </div>

    <div class="card">

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Jenis</th>
                        <th>Tujuan</th>
                        <th>Nominal</th>
                        <th>No. Rekening</th>
                        <th>Status</th>
                        <th>Arah</th>
                        <th>Tanggal</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($pembayaran as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item->jenis_pembayaran }}
                        </td>

                        <td>
                            {{ $item->pengajuan->tujuan ?? '-' }}
                        </td>

                        <td>
                            Rp {{ number_format($item->nominal) }}
                        </td>

                        <td>
                            {{ $item->no_rekening_pegawai ?? '-' }}
                        </td>

                        <td>

                            @if($item->status == 'dibayar')
                                <span class="badge bg-success">Dibayar</span>
                            @elseif($item->status == 'pending')
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            @else
                                <span class="badge bg-secondary">{{ $item->status }}</span>
                            @endif

                        </td>

                        <td>
                            {{ $item->arah_transaksi }}
                        </td>

                        <td>
                            {{ $item->tanggal_pembayaran }}
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="8" class="text-center">

                            Belum ada pembayaran

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection