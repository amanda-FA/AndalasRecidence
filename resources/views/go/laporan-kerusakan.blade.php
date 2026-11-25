@extends('layouts.app')

@section('title', 'Laporan Kerusakan')

@section('navbar-left')
    <span class="material-symbols-outlined">build</span>
    <span class="font-title-md">Laporan Kerusakan</span>
@endsection

@section('content')
<div class="bg-surface-container-lowest min-h-screen" style="padding: var(--space-lg);">

    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-md); margin-bottom: var(--space-xl);">
        <div>
            <h1 class="font-headline-lg text-primary" style="margin-bottom: var(--space-xs);">Laporan Kerusakan</h1>
            <p class="font-body-md text-on-surface-variant">Kelola seluruh tiket kerusakan fasilitas asrama.</p>
        </div>
        <button type="button" onclick="document.getElementById('modal-tambah-kerusakan').style.display='flex'" class="bg-secondary text-on-secondary font-label-md" style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 10px 20px; border-radius: 999px; text-decoration: none; border: none; cursor: pointer;">
            <span class="material-symbols-outlined" style="font-size: 20px;">add</span>
            Buat Laporan Kerusakan Baru
        </button>
    </div>

    {{-- Stat Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--space-md); margin-bottom: var(--space-xl);">

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">confirmation_number</span>
                <span class="font-label-md text-on-surface-variant">Total Kerusakan</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['total'] ?? 0 }}</span>
        </div>

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">hourglass_top</span>
                <span class="font-label-md text-on-surface-variant">Menunggu Disposisi</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['menunggu_disposisi'] ?? 0 }}</span>
        </div>

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">engineering</span>
                <span class="font-label-md text-on-surface-variant">Dalam Perbaikan Teknisi</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['dalam_perbaikan'] ?? 0 }}</span>
        </div>

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">check_circle</span>
                <span class="font-label-md text-on-surface-variant">Selesai Diperbaiki</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['selesai'] ?? 0 }}</span>
        </div>
    </div>

    {{-- Tiket Table --}}
    <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); overflow: hidden;">
        <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant);">
            <h2 class="font-headline-sm text-primary">Master Tiket Kerusakan</h2>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--outline-variant);">
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">No. Tiket</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Fasilitas</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Lokasi</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Tanggal</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Teknisi</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tikets as $tiket)
                        @php
                            $noTiket = is_array($tiket) ? ($tiket['nomor_tiket'] ?? '-') : ($tiket->no_tiket ?? '-');
                            $fasilitas = is_array($tiket) ? ($tiket['fasilitas'] ?? '-') : ($tiket->fasilitas ?? '-');
                            $lokasi = is_array($tiket) ? (($tiket['gedung'] ?? '-') . ' · ' . ($tiket['kamar'] ?? '-')) : (($tiket->gedung->nama_gedung ?? '-') . ' · ' . ($tiket->kamar->nomor_kamar ?? '-'));
                            $tgl = is_array($tiket) ? ($tiket['tanggal_lapor'] ?? '-') : (\Carbon\Carbon::parse($tiket->tanggal ?? now())->format('d M Y'));
                            $teknisi = is_array($tiket) ? ($tiket['teknisi'] ?? 'Belum ditugaskan') : ($tiket->teknisi->nama_lengkap ?? 'Belum ditugaskan');
                            $statusLabel = is_array($tiket) ? ($tiket['status_label'] ?? 'Menunggu Disposisi') : ($statusLabels[$tiket->status] ?? ucfirst($tiket->status ?? ''));
                            $statusColor = is_array($tiket) ? ($tiket['status_color'] ?? '') : ($statusColors[$tiket->status] ?? '');
                        @endphp
                        <tr style="border-bottom: 1px solid var(--outline-variant);">
                            <td class="font-body-md text-primary" style="padding: var(--space-sm) var(--space-md);">
                                <span style="font-family: monospace;">{{ $noTiket }}</span>
                            </td>
                            <td class="font-body-md text-primary" style="padding: var(--space-sm) var(--space-md);">{{ $fasilitas }}</td>
                            <td class="font-body-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md);">
                                {{ $lokasi }}
                            </td>
                            <td class="font-body-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md);">
                                {{ $tgl }}
                            </td>
                            <td class="font-body-md text-primary" style="padding: var(--space-sm) var(--space-md);">
                                {{ $teknisi }}
                            </td>
                            <td style="padding: var(--space-sm) var(--space-md);">
                                <span class="font-label-md {{ is_array($tiket) ? $statusColor : '' }}" style="{{ !is_array($tiket) ? $statusColor : '' }} padding: 4px 12px; border-radius: 999px;">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: var(--space-2xl); text-align: center;">
                                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 48px; display: block; margin-bottom: var(--space-sm);">inbox</span>
                                <span class="font-body-md text-on-surface-variant">Belum ada tiket kerusakan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
