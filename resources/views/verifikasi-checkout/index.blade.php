
@extends('layouts.app')

@section('title', 'Verifikasi Check-out dan Bebas Asrama')

@section('navbar-left')
    <div class="flex flex-col">
        <span class="font-title-md text-title-md text-primary">
            Verifikasi Check-out
        </span>
        <span class="font-caption-sm text-caption-sm text-on-surface-variant">
            Pengelolaan pengajuan check-out dan bebas asrama
        </span>
    </div>
@endsection

@section('content')
<div class="flex flex-col gap-space-lg">

    {{-- Page Heading --}}
    <div class="flex flex-col gap-1">
        <h1 class="font-headline-lg text-headline-lg text-primary">
            Verifikasi Check-out dan Bebas Asrama
        </h1>
        <p class="text-on-surface-variant">
            Periksa pengajuan penghuni dan hasil pengecekan kamar sebelum
            menentukan keputusan verifikasi.
        </p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">

        <div class="bg-surface-container-lowest rounded-2xl p-space-lg border border-outline-variant/30">
            <div class="flex items-center justify-between">
                <span class="text-sm text-on-surface-variant">Total Pengajuan</span>
                <span class="material-symbols-outlined text-primary">assignment</span>
            </div>
            <p class="text-3xl font-bold text-primary mt-3">
                {{ $stats['total'] }}
            </p>
            <p class="text-xs text-on-surface-variant mt-1">Seluruh pengajuan</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl p-space-lg border border-outline-variant/30">
            <div class="flex items-center justify-between">
                <span class="text-sm text-on-surface-variant">Menunggu Verifikasi</span>
                <span class="material-symbols-outlined text-amber-600">pending_actions</span>
            </div>
            <p class="text-3xl font-bold text-amber-600 mt-3">
                {{ $stats['menunggu'] }}
            </p>
            <p class="text-xs text-on-surface-variant mt-1">Perlu diperiksa Fasilitator</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl p-space-lg border border-outline-variant/30">
            <div class="flex items-center justify-between">
                <span class="text-sm text-on-surface-variant">Disetujui</span>
                <span class="material-symbols-outlined text-green-600">check_circle</span>
            </div>
            <p class="text-3xl font-bold text-green-600 mt-3">
                {{ $stats['disetujui'] }}
            </p>
            <p class="text-xs text-on-surface-variant mt-1">Pengajuan disetujui</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl p-space-lg border border-outline-variant/30">
            <div class="flex items-center justify-between">
                <span class="text-sm text-on-surface-variant">Ditolak</span>
                <span class="material-symbols-outlined text-error">cancel</span>
            </div>
            <p class="text-3xl font-bold text-error mt-3">
                {{ $stats['ditolak'] }}
            </p>
            <p class="text-xs text-on-surface-variant mt-1">Pengajuan ditolak</p>
        </div>

    </div>

    {{-- Daftar Pengajuan --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-space-lg">

        {{-- Filter --}}
        <form action="{{ route('fasilitator.verifikasi-checkout.index') }}"
              method="GET"
              class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">

            <div>
                <h2 class="font-title-md text-title-md text-primary">
                    Daftar Pengajuan
                </h2>
                <p class="text-sm text-on-surface-variant mt-1">
                    Daftar pengajuan check-out dan bebas asrama penghuni.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-space-sm">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau NIM..."
                    class="rounded-xl border border-outline-variant px-space-md py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none"
                >

                <select
                    name="status"
                    class="rounded-xl border border-outline-variant px-space-md py-2.5 text-sm"
                >
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>
                        Diajukan
                    </option>
                    <option value="audit_go" {{ request('status') == 'audit_go' ? 'selected' : '' }}>
                        Sedang Diperiksa GO
                    </option>
                    <option value="menunggu_persetujuan" {{ request('status') == 'menunggu_persetujuan' ? 'selected' : '' }}>
                        Menunggu Verifikasi
                    </option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>
                        Disetujui
                    </option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                        Ditolak
                    </option>
                </select>

                <button type="submit"
                        class="px-space-md py-2.5 rounded-xl bg-primary text-on-primary text-sm font-semibold hover:opacity-90">
                    Cari
                </button>

                <a href="{{ route('fasilitator.verifikasi-checkout.index') }}"
                   class="px-space-md py-2.5 rounded-xl border border-outline-variant text-primary text-sm text-center">
                    Reset
                </a>
            </div>
        </form>

        {{-- Application Table --}}
        <div class="overflow-x-auto mt-space-lg">
            <table class="w-full text-sm text-left">

                <thead class="bg-surface-container text-on-surface-variant">
                    <tr>
                        <th class="px-space-md py-3 rounded-l-lg">Penghuni</th>
                        <th class="px-space-md py-3">Kamar</th>
                        <th class="px-space-md py-3">Tanggal Pengajuan</th>
                        <th class="px-space-md py-3">Rencana Keluar</th>
                        <th class="px-space-md py-3">Status</th>
                        <th class="px-space-md py-3 rounded-r-lg text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-outline-variant/30">

                    @forelse ($pengajuan as $item)

                        @php
                            $penempatan = $item->penempatanKamar;
                            $pendaftaran = $penempatan?->pendaftaranHunian;
                            $client = $pendaftaran?->client;
                            $pengguna = $client?->pengguna;
                            $kamar = $penempatan?->kamar;

                            $statusLabel = match ($item->status_pengajuan) {
                                'diajukan' => 'Diajukan',
                                'audit_go' => 'Sedang Diperiksa GO',
                                'menunggu_persetujuan' => 'Menunggu Verifikasi',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                default => ucfirst(str_replace('_', ' ', $item->status_pengajuan)),
                            };

                            $statusClass = match ($item->status_pengajuan) {
                                'diajukan', 'audit_go', 'menunggu_persetujuan'
                                    => 'bg-amber-100 text-amber-700',
                                'disetujui'
                                    => 'bg-green-100 text-green-700',
                                'ditolak'
                                    => 'bg-error-container text-error',
                                default
                                    => 'bg-surface-container text-on-surface-variant',
                            };
                        @endphp

                        <tr class="hover:bg-surface-container-low">

                            <td class="px-space-md py-4">
                                <p class="font-semibold text-on-surface">
                                    {{ $pengguna?->nama_lengkap ?? 'Nama tidak tersedia' }}
                                </p>
                                <p class="text-xs text-on-surface-variant">
                                    {{ $client?->nomor_identitas ?? '-' }}
                                </p>
                            </td>

                            <td class="px-space-md py-4">
                                {{ $kamar?->nomor_kamar ?? '-' }}
                                @if ($kamar?->gedung)
                                    <p class="text-xs text-on-surface-variant">
                                        {{ $kamar->gedung->nama_gedung ?? '' }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-space-md py-4">
                                {{ $item->tanggal_pengajuan
                                    ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y')
                                    : '-' }}
                            </td>

                            <td class="px-space-md py-4">
                                {{ $item->tanggal_rencanaKeluar
                                    ? \Carbon\Carbon::parse($item->tanggal_rencanaKeluar)->format('d M Y')
                                    : '-' }}
                            </td>

                            <td class="px-space-md py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td class="px-space-md py-4 text-center">
                                <a href="{{ route('fasilitator.verifikasi-checkout.show', $item->id_pengajuan) }}"
                                   class="inline-block px-3 py-2 rounded-lg bg-primary text-on-primary text-xs font-semibold hover:opacity-90">
                                    Lihat Detail
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-space-md py-10 text-center">
                                <span class="material-symbols-outlined text-4xl text-on-surface-variant">
                                    inbox
                                </span>
                                <p class="font-semibold text-on-surface mt-2">
                                    Belum ada pengajuan
                                </p>
                                <p class="text-sm text-on-surface-variant mt-1">
                                    Data pengajuan check-out belum tersedia.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-space-lg">
            {{ $pengajuan->links() }}
        </div>

        <div class="mt-space-md text-xs text-on-surface-variant">
            Menampilkan {{ $pengajuan->firstItem() ?? 0 }}
            sampai {{ $pengajuan->lastItem() ?? 0 }}
            dari {{ $pengajuan->total() }} pengajuan.
        </div>

    </div>

</div>
@endsection
