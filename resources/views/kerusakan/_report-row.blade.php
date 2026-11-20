@php
    $fasilitasLaporan = $laporan ? $facilities[$laporan['id_fasilitas']] : [];
    $statusLaporan = $laporan ? $statuses[$laporan['status_perbaikan']] : ['warna' => '', 'label' => ''];
@endphp
<tr data-report-id="{{ $laporan['id_laporan'] ?? '' }}" class="hover:bg-surface-container-low/50 transition-colors">
    <td class="py-5 pl-space-lg pr-space-md max-w-80"><p data-report-field="description" class="text-label-md font-semibold text-primary line-clamp-2">{{ $laporan['deskripsi'] ?? null }}</p><p data-report-field="facility" class="text-[11px] text-on-surface-variant mt-1.5">{{ $fasilitasLaporan['nama_fasilitas'] ?? '' }}</p></td>
    <td class="p-space-md"><p data-report-field="location" class="text-label-md font-medium text-primary">{{ $fasilitasLaporan['lokasi'] ?? '' }}</p><p data-report-field="building" class="text-[11px] text-on-surface-variant mt-1">{{ $fasilitasLaporan['gedung'] ?? '' }}</p></td>
    <td data-report-field="date" class="p-space-md text-caption-sm whitespace-nowrap">{{ $formatDate($laporan['tanggal_lapor'] ?? null) }}</td>
    <td class="p-space-md"><span data-report-field="status" class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-medium whitespace-nowrap {{ $statusLaporan['warna'] }}">{{ $statusLaporan['label'] }}</span></td>
    <td data-report-field="completed" class="p-space-md text-caption-sm whitespace-nowrap">{{ $formatDate($laporan['tanggal_selesai'] ?? null) }}</td>
    <td class="py-5 pl-space-md pr-space-lg text-right">
        <button type="button" data-open-report="{{ $laporan['id_laporan'] ?? '' }}" aria-label="Lihat detail laporan {{ $laporan['id_laporan'] ?? '' }}" title="Lihat detail laporan" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[#4B4FB8] hover:bg-[#4B4FB8]/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#4B4FB8] transition-colors cursor-pointer">
            @include('partials.detail-icon')
        </button>
    </td>
</tr>
