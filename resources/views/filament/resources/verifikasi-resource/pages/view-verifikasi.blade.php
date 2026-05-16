<x-filament-panels::page>
    @php
        $v = $this->record;
        $pengajuan = $v->pengajuan;
        $transaksi = $v->transaksiPengeluaran;
        $semuaPengeluaran = $pengajuan?->transaksiPengeluaran ?? collect();
        $totalPengeluaran = $semuaPengeluaran->sum('nominal');
        $realisasi = $pengajuan?->realisasiDana?->total_realisasi ?? 0;
    @endphp

    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Ringkasan Verifikasi</x-slot>
            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                <div><dt class="text-gray-500">ID Verifikasi</dt><dd class="font-semibold">#{{ $v->id }}</dd></div>
                <div><dt class="text-gray-500">Jenis</dt><dd class="font-semibold">{{ $v->verification_type }}</dd></div>
                <div><dt class="text-gray-500">Status</dt><dd>
                    <x-filament::badge :color="match($v->status) { 'approve' => 'success', 'reject' => 'danger', default => 'warning' }">
                        {{ $v->statusLabel }}
                    </x-filament::badge>
                </dd></div>
                <div><dt class="text-gray-500">Pengajuan</dt><dd>#{{ $v->id_pengajuan }} — {{ $pengajuan->tujuan ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Jenis Pengajuan</dt><dd>{{ $pengajuan->jenis_pengajuan ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Pegawai</dt><dd>{{ $pengajuan->pegawai->nama ?? '-' }}</dd></div>
            </dl>
        </x-filament::section>

        @if($transaksi)
        <x-filament::section>
            <x-slot name="heading">Pengeluaran yang Diverifikasi</x-slot>
            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2 text-sm">
                <div><dt class="text-gray-500">ID Transaksi</dt><dd class="font-semibold">#{{ $transaksi->id_transaksi_pengeluaran }}</dd></div>
                <div><dt class="text-gray-500">Tanggal</dt><dd>{{ optional($transaksi->tanggal_pengeluaran)->format('d/m/Y') ?? '-' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-gray-500">Uraian</dt><dd class="font-semibold">{{ $transaksi->uraian }}</dd></div>
                <div><dt class="text-gray-500">Kategori</dt><dd>{{ $transaksi->kategoriBiaya->jenis_biaya ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Nominal</dt><dd class="font-semibold text-lg text-primary-600">Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}</dd></div>
                @if($transaksi->bukti)
                <div class="sm:col-span-2">
                    <dt class="text-gray-500 mb-1">Bukti</dt>
                    <dd>
                        <a href="{{ asset('bukti_pengeluaran/' . $transaksi->bukti) }}" target="_blank" class="text-primary-600 underline">
                            Lihat bukti pengeluaran
                        </a>
                    </dd>
                </div>
                @endif
            </dl>
        </x-filament::section>
        @endif

        @if($semuaPengeluaran->count())
        <x-filament::section>
            <x-slot name="heading">Semua Pengeluaran pada Pengajuan ini</x-slot>
            <p class="text-sm text-gray-500 mb-3">Admin dapat meninjau seluruh rincian sebelum menyetujui.</p>
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2">No</th>
                            <th class="px-3 py-2">Tanggal</th>
                            <th class="px-3 py-2">Uraian</th>
                            <th class="px-3 py-2">Kategori</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semuaPengeluaran as $row)
                        <tr class="@if($v->id_transaksi_pengeluaran == $row->id_transaksi_pengeluaran) bg-warning-50 dark:bg-warning-500/10 font-medium @endif border-t border-gray-100 dark:border-gray-700">
                            <td class="px-3 py-2">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2">{{ optional($row->tanggal_pengeluaran)->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $row->uraian }}</td>
                            <td class="px-3 py-2">{{ $row->kategoriBiaya->jenis_biaya ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $row->status }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-t-2 border-gray-300 font-semibold">
                            <td colspan="5" class="px-3 py-2 text-right">Total Pengeluaran</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        </tr>
                        @if($realisasi > 0)
                        <tr class="font-semibold">
                            <td colspan="5" class="px-3 py-2 text-right">Realisasi Dana</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($realisasi, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="font-semibold text-primary-600">
                            <td colspan="5" class="px-3 py-2 text-right">Sisa Dana</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format(max(0, $realisasi - $totalPengeluaran), 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
