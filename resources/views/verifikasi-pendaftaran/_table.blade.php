<section id="verification-{{ $tableId }}" class="bg-white border border-outline-variant/20 rounded-2xl shadow-sm overflow-hidden scroll-mt-24" aria-labelledby="{{ $tableId }}-title" data-verification-table="{{ $tableId }}">
    <header class="px-5 py-5 border-b border-surface-container flex flex-wrap justify-between items-center gap-3">
        <div><h2 id="{{ $tableId }}-title" class="font-title-md text-title-md text-primary">{{ $title }}</h2><p class="text-caption-sm text-on-surface-variant mt-1">{{ $description }}</p></div>
        <span class="rounded-full px-3 py-1 bg-surface-container-low text-secondary text-caption-sm font-semibold">{{ number_format($records->total(),0,',','.') }} pendaftaran</span>
    </header>
    <div class="overflow-x-auto"><table class="w-full text-left min-w-[1000px]">
        <thead class="bg-surface-container-low text-caption-sm uppercase text-on-surface-variant"><tr>
            @foreach (['Tanggal & Jam', 'Nama Client', 'Gedung / Kamar', 'Jalur', 'Status', 'Aksi'] as $label)<th scope="col" class="px-5 py-4 font-semibold {{ $loop->last ? 'text-center w-20' : '' }}">{{ $label }}</th>@endforeach
        </tr></thead>
        <tbody>@forelse ($records as $record)
            @php
                $placement = $record->penempatanKamars->sortByDesc('id_penempatan')->first();
                $adminPlaced = $record->client?->requiresAdminPlacement();
                $room = $adminPlaced ? $placement?->kamar : ($record->kamar ?? $placement?->kamar);
                $timestamp = $isHistory ? ($record->tanggal_verifikasi ?? $record->tanggal_daftar) : $record->tanggal_daftar;
            @endphp
            <tr class="border-b border-surface-container last:border-b-0 hover:bg-surface-container-low/40">
                <td class="px-5 py-5 whitespace-nowrap"><p class="font-medium">{{ $timestamp->format('d M Y') }}</p><p class="text-caption-sm text-on-surface-variant mt-1">{{ $timestamp->format('H:i:s') }} WIB</p></td>
                <td class="px-5 py-5"><p class="font-semibold text-primary">{{ $record->client?->pengguna?->nama_lengkap }}</p><p class="text-caption-sm text-on-surface-variant mt-1">{{ $record->client?->nomor_identitas ?? '-' }}</p></td>
                <td class="px-5 py-5">@if ($room)<p class="font-medium text-primary">{{ $room->gedung?->nama_gedung }}</p><p class="text-caption-sm text-on-surface-variant mt-1">Kamar {{ $room->nomor_kamar }}@if($adminPlaced && $placement?->nomor_bed) / Bed {{ chr(64+$placement->nomor_bed) }}@endif</p>@elseif ($adminPlaced)<span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-[11px] font-semibold text-gray-500 whitespace-nowrap"><span class="material-symbols-outlined text-[15px]" aria-hidden="true">lock</span>Ditempatkan Admin</span>@else<p class="text-caption-sm text-on-surface-variant">Pilihan kamar belum tercatat</p>@endif</td>
                <td class="px-5 py-5"><span class="text-caption-sm font-semibold text-secondary">{{ $record->client?->jenisClient?->jenis }}</span></td>
                <td class="px-5 py-5">@include('verifikasi-pendaftaran._status', ['status' => $record->status_pendaftaran])</td>
                <td class="px-5 py-5 text-center"><a href="{{ route('verifikasi-pendaftaran.show',$record) }}" aria-label="Lihat detail pendaftaran {{ $record->client?->pengguna?->nama_lengkap }}" title="Lihat detail" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[#4B4FB8] hover:bg-[#4B4FB8]/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#4B4FB8] transition-colors"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><circle cx="8" cy="12" r="1.35" fill="currentColor"/><circle cx="12" cy="12" r="1.35" fill="currentColor"/><circle cx="16" cy="12" r="1.35" fill="currentColor"/></svg></a></td>
            </tr>
        @empty<tr><td colspan="6" class="p-10 text-center text-on-surface-variant">{{ $isHistory ? 'Belum ada riwayat verifikasi yang sesuai filter.' : 'Tidak ada pendaftaran yang menunggu verifikasi sesuai filter.' }}</td></tr>@endforelse</tbody>
    </table></div>
    <footer class="px-5 py-4 border-t border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <p class="text-caption-sm text-on-surface-variant">
            Menampilkan {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} dari {{ number_format($records->total(), 0, ',', '.') }} pendaftaran
        </p>
        <nav class="flex items-center gap-3" aria-label="Halaman {{ $title }}">
            @if ($records->onFirstPage())
                <button type="button" disabled aria-label="Halaman sebelumnya {{ strtolower($title) }}" class="w-9 h-9 rounded-xl border border-outline-variant/30 bg-surface-container-low text-outline/50 inline-flex items-center justify-center cursor-not-allowed">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">chevron_left</span>
                </button>
            @else
                <a href="{{ $records->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya {{ strtolower($title) }}" title="Halaman sebelumnya" class="w-9 h-9 rounded-xl border border-secondary/20 bg-surface-container-low text-secondary inline-flex items-center justify-center hover:bg-surface-container focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary transition-colors">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">chevron_left</span>
                </a>
            @endif
            <span class="text-caption-sm text-on-surface-variant whitespace-nowrap" aria-current="page">Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() }}</span>
            @if ($records->hasMorePages())
                <a href="{{ $records->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya {{ strtolower($title) }}" title="Halaman berikutnya" class="w-9 h-9 rounded-xl border border-secondary/20 bg-surface-container-low text-secondary inline-flex items-center justify-center hover:bg-surface-container focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary transition-colors">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">chevron_right</span>
                </a>
            @else
                <button type="button" disabled aria-label="Halaman berikutnya {{ strtolower($title) }}" class="w-9 h-9 rounded-xl border border-outline-variant/30 bg-surface-container-low text-outline/50 inline-flex items-center justify-center cursor-not-allowed">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">chevron_right</span>
                </button>
            @endif
        </nav>
    </footer>
</section>
