<x-filament::section>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">
            Jurnal Umum
        </h2>

        <input
            type="month"
            wire:model.live="periode"
            class="border rounded px-3 py-2"
        >
    </div>

    @php
        $data = $this->getData();
    @endphp

    <div class="text-center mb-5">
        <h3 class="font-bold text-lg">
            Jurnal Umum
        </h3>

        <p>
            Periode
            {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border text-sm">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Tanggal</th>
                    <th class="p-2 border">Keterangan</th>
                    <th class="p-2 border">Akun</th>
                    <th class="p-2 border">Debit</th>
                    <th class="p-2 border">Kredit</th>
                </tr>
            </thead>

            <tbody>
                @foreach($data['details'] as $detail)
                    <tr>
                        <td class="border p-2">
                            {{ $detail->id_jurnal }}
                        </td>

                        <td class="border p-2">
                            {{ \Carbon\Carbon::parse($detail->jurnal->tanggal)->format('d/m/Y') }}
                        </td>

                        <td class="border p-2">
                            {{ $detail->jurnal->keterangan }}
                        </td>

                        <td class="border p-2">
                            {{ $detail->akun->nama_akun ?? '-' }}
                        </td>

                        <td class="border p-2 text-right">
                            {{ $detail->debit > 0 ? 'Rp ' . number_format($detail->debit,0,',','.') : '-' }}
                        </td>

                        <td class="border p-2 text-right">
                            {{ $detail->kredit > 0 ? 'Rp ' . number_format($detail->kredit,0,',','.') : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="font-bold">
                    <td colspan="4" class="border p-2 text-right">
                        TOTAL
                    </td>

                    <td class="border p-2 text-right">
                        Rp {{ number_format($data['totalDebit'],0,',','.') }}
                    </td>

                    <td class="border p-2 text-right">
                        Rp {{ number_format($data['totalKredit'],0,',','.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</x-filament::section>