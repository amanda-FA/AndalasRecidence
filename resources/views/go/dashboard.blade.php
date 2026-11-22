@extends('layouts.app')

@section('title', 'Dashboard General Officer')

@section('navbar-left')
    <span class="material-symbols-outlined">shield_person</span>
    <span class="font-title-md">General Officer</span>
@endsection

@section('content')
<div class="bg-surface-container-lowest min-h-screen" style="padding: var(--space-lg);">

    {{-- Header --}}
    <div style="margin-bottom: var(--space-xl);">
        <h1 class="font-headline-lg text-primary" style="margin-bottom: var(--space-xs);">Dashboard General Officer</h1>
        <p class="font-body-md text-on-surface-variant">Pantau dan kelola seluruh pengecekan kamar asrama secara real-time.</p>
    </div>

    {{-- Stat Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--space-md); margin-bottom: var(--space-xl);">

        {{-- Menunggu Pengecekan --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">pending_actions</span>
                <span class="font-label-md text-on-surface-variant">Menunggu Pengecekan</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['menunggu'] ?? 0 }}</span>
        </div>

        {{-- Sedang Diperiksa --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">search</span>
                <span class="font-label-md text-on-surface-variant">Sedang Diperiksa</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['sedang_diperiksa'] ?? 0 }}</span>
        </div>

        {{-- Pengecekan Selesai --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">task_alt</span>
                <span class="font-label-md text-on-surface-variant">Pengecekan Selesai</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['selesai'] ?? 0 }}</span>
        </div>

        {{-- Kerusakan Ditemukan --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">report_problem</span>
                <span class="font-label-md text-on-surface-variant">Kerusakan Ditemukan</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['kerusakan'] ?? 0 }}</span>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div style="margin-bottom: var(--space-xl);">
        <h2 class="font-headline-sm text-primary" style="margin-bottom: var(--space-md);">Aksi Cepat</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: var(--space-md);">

            <a href="{{ route('go.pengecekan-kamar.index') }}" class="bg-surface-container-lowest" style="display: flex; align-items: center; gap: var(--space-md); padding: var(--space-lg); border-radius: 16px; border: 1px solid var(--outline-variant); text-decoration: none; transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
                <span class="material-symbols-outlined bg-secondary text-on-secondary" style="padding: var(--space-sm); border-radius: 12px; font-size: 28px;">door_open</span>
                <div>
                    <span class="font-title-md text-primary" style="display: block;">Pengecekan Kamar</span>
                    <span class="font-body-md text-on-surface-variant">Mulai inspeksi kamar check-out</span>
                </div>
            </a>

            <a href="{{ route('go.laporan-kerusakan.index') }}" class="bg-surface-container-lowest" style="display: flex; align-items: center; gap: var(--space-md); padding: var(--space-lg); border-radius: 16px; border: 1px solid var(--outline-variant); text-decoration: none; transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
                <span class="material-symbols-outlined bg-secondary text-on-secondary" style="padding: var(--space-sm); border-radius: 12px; font-size: 28px;">build</span>
                <div>
                    <span class="font-title-md text-primary" style="display: block;">Laporan Kerusakan</span>
                    <span class="font-body-md text-on-surface-variant">Kelola tiket kerusakan fasilitas</span>
                </div>
            </a>

            <a href="{{ route('go.riwayat-pengecekan.index') }}" class="bg-surface-container-lowest" style="display: flex; align-items: center; gap: var(--space-md); padding: var(--space-lg); border-radius: 16px; border: 1px solid var(--outline-variant); text-decoration: none; transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
                <span class="material-symbols-outlined bg-secondary text-on-secondary" style="padding: var(--space-sm); border-radius: 12px; font-size: 28px;">history</span>
                <div>
                    <span class="font-title-md text-primary" style="display: block;">Riwayat Pengecekan</span>
                    <span class="font-body-md text-on-surface-variant">Lihat histori audit kamar</span>
                </div>
            </a>
        </div>
    </div>

    {{-- Pengecekan Kamar Terbaru Table --}}
    <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); overflow: hidden;">
        <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant);">
            <h2 class="font-headline-sm text-primary">Pengecekan Kamar Terbaru</h2>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--outline-variant);">
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Pemohon</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Gedung / Kamar</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Tanggal Check-out</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Status</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengecekanTerbaru as $item)
                        <tr style="border-bottom: 1px solid var(--outline-variant);">
                            <td style="padding: var(--space-sm) var(--space-md);">
                                <div style="display: flex; align-items: center; gap: var(--space-sm);">
                                    <div class="bg-primary-container" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span class="font-label-md text-primary">{{ strtoupper(substr($item->pengguna->nama_lengkap ?? '-', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <span class="font-body-md text-primary" style="display: block;">{{ $item->pengguna->nama_lengkap ?? '-' }}</span>
                                        <span class="font-caption-sm text-on-surface-variant">{{ $item->pengguna->email ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="font-body-md text-primary" style="padding: var(--space-sm) var(--space-md);">
                                {{ $item->gedung->nama_gedung ?? '-' }} / {{ $item->kamar->nomor_kamar ?? '-' }}
                            </td>
                            <td class="font-body-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md);">
                                {{ \Carbon\Carbon::parse($item->tanggal_checkout)->format('d M Y') }}
                            </td>
                            <td style="padding: var(--space-sm) var(--space-md);">
                                @php
                                    $statusColors = [
                                        'menunggu' => 'background: var(--warning-container); color: var(--on-warning-container);',
                                        'sedang_diperiksa' => 'background: var(--info-container); color: var(--on-info-container);',
                                        'selesai' => 'background: var(--success-container); color: var(--on-success-container);',
                                    ];
                                    $statusLabels = [
                                        'menunggu' => 'Menunggu',
                                        'sedang_diperiksa' => 'Sedang Diperiksa',
                                        'selesai' => 'Selesai',
                                    ];
                                @endphp
                                <span class="font-label-md" style="{{ $statusColors[$item->status] ?? '' }} padding: 4px 12px; border-radius: 999px;">
                                    {{ $statusLabels[$item->status] ?? ucfirst($item->status) }}
                                </span>
                            </td>
                            <td style="padding: var(--space-sm) var(--space-md); text-align: center;">
                                @if ($item->status === 'menunggu')
                                    <form action="{{ route('go.pengecekan-kamar.mulai', $item->id_pengajuan) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="bg-secondary text-on-secondary font-label-md" style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 8px 16px; border-radius: 999px; border: none; cursor: pointer;">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">play_arrow</span>
                                            Mulai Periksa
                                        </button>
                                    </form>
                                @else
                                    <span class="font-caption-sm text-on-surface-variant">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: var(--space-2xl); text-align: center;">
                                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 48px; display: block; margin-bottom: var(--space-sm);">inbox</span>
                                <span class="font-body-md text-on-surface-variant">Belum ada data pengecekan kamar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
