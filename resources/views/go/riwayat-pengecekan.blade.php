@extends('layouts.app')

@section('title', 'Riwayat Pengecekan')

@section('navbar-left')
    <span class="material-symbols-outlined">history</span>
    <span class="font-title-md">Riwayat Pengecekan</span>
@endsection

@section('content')
<div class="bg-surface-container-lowest min-h-screen" style="padding: var(--space-lg);">

    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-md); margin-bottom: var(--space-xl);">
        <div>
            <h1 class="font-headline-lg text-primary" style="margin-bottom: var(--space-xs);">Riwayat Pengecekan</h1>
            <p class="font-body-md text-on-surface-variant">Arsip lengkap seluruh audit pengecekan kamar yang telah diselesaikan.</p>
        </div>
        <button onclick="window.print()" class="bg-secondary text-on-secondary font-label-md" style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 10px 20px; border-radius: 999px; border: none; cursor: pointer;">
            <span class="material-symbols-outlined" style="font-size: 20px;">download</span>
            Export Laporan
        </button>
    </div>

    {{-- Metric Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--space-md); margin-bottom: var(--space-xl);">

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">meeting_room</span>
                <span class="font-label-md text-on-surface-variant">Total Kamar Diperiksa</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['total_diperiksa'] ?? 0 }}</span>
        </div>

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">verified</span>
                <span class="font-label-md text-on-surface-variant">Tingkat Kelayakan</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['tingkat_kelayakan'] ?? 0 }}%</span>
        </div>

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">report_problem</span>
                <span class="font-label-md text-on-surface-variant">Temuan Kerusakan</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['temuan_kerusakan'] ?? 0 }}</span>
        </div>

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 24px;">timer</span>
                <span class="font-label-md text-on-surface-variant">Rata-rata Audit</span>
            </div>
            <span class="font-headline-lg text-primary">{{ $stats['rata_rata_audit'] ?? 0 }} <span class="font-body-md">menit</span></span>
        </div>
    </div>

    {{-- Riwayat Table --}}
    <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); overflow: hidden;">
        <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant);">
            <h2 class="font-headline-sm text-primary">Arsip Pengecekan</h2>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--outline-variant);">
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Mahasiswa</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Gedung / Kamar</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Tanggal Audit</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: center;">Kondisi</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: center;">Kunci Fisik</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: center;">RFID</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: center;">Validasi</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $item)
                        <tr style="border-bottom: 1px solid var(--outline-variant);">
                            <td style="padding: var(--space-sm) var(--space-md);">
                                <div style="display: flex; align-items: center; gap: var(--space-sm);">
                                    <div class="bg-primary-container" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span class="font-label-md text-primary">{{ strtoupper(substr($item->pengguna->nama_lengkap ?? '-', 0, 1)) }}</span>
                                    </div>
                                    <span class="font-body-md text-primary">{{ $item->pengguna->nama_lengkap ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="font-body-md text-primary" style="padding: var(--space-sm) var(--space-md);">
                                {{ $item->gedung->nama_gedung ?? '-' }} / {{ $item->kamar->nomor_kamar ?? '-' }}
                            </td>
                            <td class="font-body-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md);">
                                {{ \Carbon\Carbon::parse($item->tanggal_audit)->format('d M Y') }}
                            </td>
                            <td style="padding: var(--space-sm) var(--space-md); text-align: center;">
                                @if ($item->kondisi === 'baik')
                                    <span class="font-label-md" style="background: var(--success-container); color: var(--on-success-container); padding: 4px 12px; border-radius: 999px;">Baik</span>
                                @elseif ($item->kondisi === 'rusak_ringan')
                                    <span class="font-label-md" style="background: var(--warning-container); color: var(--on-warning-container); padding: 4px 12px; border-radius: 999px;">Rusak Ringan</span>
                                @else
                                    <span class="font-label-md" style="background: var(--error-container); color: var(--on-error-container); padding: 4px 12px; border-radius: 999px;">Rusak Berat</span>
                                @endif
                            </td>
                            <td style="padding: var(--space-sm) var(--space-md); text-align: center;">
                                @if ($item->kunci_fisik)
                                    <span class="material-symbols-outlined" style="color: var(--success); font-size: 22px;">check_circle</span>
                                @else
                                    <span class="material-symbols-outlined" style="color: var(--error); font-size: 22px;">cancel</span>
                                @endif
                            </td>
                            <td style="padding: var(--space-sm) var(--space-md); text-align: center;">
                                @if ($item->rfid_valid)
                                    <span class="material-symbols-outlined" style="color: var(--success); font-size: 22px;">check_circle</span>
                                @else
                                    <span class="material-symbols-outlined" style="color: var(--error); font-size: 22px;">cancel</span>
                                @endif
                            </td>
                            <td style="padding: var(--space-sm) var(--space-md); text-align: center;">
                                @if ($item->validasi)
                                    <span class="font-label-md" style="background: var(--success-container); color: var(--on-success-container); padding: 4px 12px; border-radius: 999px;">Valid</span>
                                @else
                                    <span class="font-label-md" style="background: var(--warning-container); color: var(--on-warning-container); padding: 4px 12px; border-radius: 999px;">Pending</span>
                                @endif
                            </td>
                            <td class="font-body-md text-primary" style="padding: var(--space-sm) var(--space-md);">
                                {{ $item->petugas->nama_lengkap ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: var(--space-2xl); text-align: center;">
                                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 48px; display: block; margin-bottom: var(--space-sm);">inbox</span>
                                <span class="font-body-md text-on-surface-variant">Belum ada riwayat pengecekan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div style="padding: var(--space-md) var(--space-lg); border-top: 1px solid var(--outline-variant); display: flex; justify-content: space-between; align-items: center;">
            <span class="font-caption-sm text-on-surface-variant">
                Menampilkan {{ $riwayat->count() }} data pengecekan
            </span>
            @if (method_exists($riwayat, 'links'))
                {{ $riwayat->links() }}
            @endif
        </div>
    </div>

</div>
@endsection
