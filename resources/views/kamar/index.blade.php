@extends('layouts.app')

@section('title', 'Manajemen Kamar - Andalas Residence')

@section('content')
<div class="space-y-space-xl">
    {{-- Top Action & Title Bar --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-space-lg bg-surface-container-lowest p-space-lg rounded-xl shadow-[0_2px_8px_rgba(18,32,86,0.04)]">
        <div class="flex flex-col space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-secondary font-caption-sm-medium text-caption-sm-medium tracking-wide">PMS Inventaris</span>
                <span class="text-outline-variant">•</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Real-time Database</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Manajemen Kamar</h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                Kelola seluruh inventaris kamar, kapasitas bed, fasilitas unit, dan status ketersediaan secara real-time.
            </p>
        </div>
        <div class="flex items-center flex-wrap gap-space-sm">
            <a href="{{ route('gedung.index') }}" class="flex items-center gap-1.5 px-space-md py-2.5 rounded-lg bg-surface-container-low text-primary font-label-md text-label-md hover:bg-surface-container transition-all">
                <span class="material-symbols-outlined text-[18px]">domain</span>
                <span>Manajemen Gedung</span>
            </a>
            <a href="{{ route('kamar.create', ['gedung_id' => request('gedung_id')]) }}" class="flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-secondary text-on-secondary font-label-md text-label-md hover:bg-on-secondary-fixed-variant active:translate-y-px transition-all shadow-[0_2px_8px_rgba(71,80,199,0.25)]">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Tambah Kamar Baru</span>
            </a>
        </div>
    </div>

    {{-- 4 Stats Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-[0_2px_8px_rgba(18,32,86,0.04)] flex flex-col justify-between">
            <div class="flex items-center justify-between mb-space-sm">
                <span class="font-label-md text-label-md text-on-surface-variant">Total Kamar</span>
                <div class="w-10 h-10 rounded-lg bg-surface-container-high text-secondary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">meeting_room</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ $stats['total_kamar'] }}</span>
                <span class="font-body-md text-body-md text-on-surface-variant">Unit Kamar</span>
            </div>
            <div class="mt-space-md pt-space-xs flex items-center justify-between text-caption-sm font-caption-sm text-on-surface-variant">
                <span>Kapasitas: {{ number_format($stats['total_bed'], 0, ',', '.') }} Bed</span>
                <span class="text-secondary font-medium">Terpetakan</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-[0_2px_8px_rgba(18,32,86,0.04)] flex flex-col justify-between">
            <div class="flex items-center justify-between mb-space-sm">
                <span class="font-label-md text-label-md text-on-surface-variant">Kamar Berpenghuni</span>
                <div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">group</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ $stats['kamar_terisi'] }}</span>
                <span class="font-body-md text-body-md text-on-surface-variant">Unit ({{ $stats['kamar_terisi_persen'] }}%)</span>
            </div>
            <div class="mt-space-md pt-space-xs flex items-center gap-2">
                <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden">
                    <div class="bg-secondary h-full rounded-full" style="width: {{ min(100, $stats['kamar_terisi_persen']) }}%;"></div>
                </div>
                <span class="font-caption-sm-medium text-caption-sm-medium text-secondary whitespace-nowrap">{{ number_format($stats['bed_terisi'], 0, ',', '.') }} Penghuni</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-[0_2px_8px_rgba(18,32,86,0.04)] flex flex-col justify-between">
            <div class="flex items-center justify-between mb-space-sm">
                <span class="font-label-md text-label-md text-on-surface-variant">Kamar Tersedia</span>
                <div class="w-10 h-10 rounded-lg bg-surface-container-low text-secondary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">check_circle</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ $stats['kamar_tersedia'] }}</span>
                <span class="font-body-md text-body-md text-on-surface-variant">Siap Reservasi</span>
            </div>
            <div class="mt-space-md pt-space-xs flex items-center justify-between font-caption-sm text-caption-sm text-on-surface-variant">
                <span class="inline-flex items-center gap-1 text-[#2E9E5B] font-medium">
                    <span class="w-2 h-2 rounded-full bg-[#2E9E5B]"></span> {{ number_format($stats['bed_kosong'], 0, ',', '.') }} Bed Kosong
                </span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-[0_2px_8px_rgba(18,32,86,0.04)] flex flex-col justify-between">
            <div class="flex items-center justify-between mb-space-sm">
                <span class="font-label-md text-label-md text-on-surface-variant">Dalam Perbaikan</span>
                <div class="w-10 h-10 rounded-lg bg-error-container text-on-error-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">handyman</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ $stats['kamar_perbaikan'] }}</span>
                <span class="font-body-md text-body-md text-on-surface-variant">Unit Dinonaktifkan</span>
            </div>
            <div class="mt-space-md pt-space-xs flex items-center justify-between font-caption-sm text-caption-sm text-on-surface-variant">
                <span class="text-[#C97D1A] font-medium">Status Maintenance</span>
            </div>
        </div>
    </div>

    {{-- Horizontal Filter Bar --}}
    <form method="GET" action="{{ route('kamar.index') }}" class="bg-surface-container-lowest p-space-md rounded-xl shadow-[0_2px_8px_rgba(18,32,86,0.04)]">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-space-sm items-center">
            <div class="lg:col-span-2 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input name="q" value="{{ request('q') }}" class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari nomor kamar (contoh: 204)..." type="text">
            </div>

            <div>
                <select name="gedung_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none cursor-pointer">
                    <option value="">Semua Gedung</option>
                    @foreach ($gedungs as $g)
                        <option value="{{ $g->id }}" {{ (string) request('gedung_id') === (string) $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="lantai" onchange="this.form.submit()" class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none cursor-pointer">
                    <option value="">Semua Lantai</option>
                    @for ($l = 1; $l <= 5; $l++)
                        <option value="{{ $l }}" {{ (string) request('lantai') === (string) $l ? 'selected' : '' }}>Lantai {{ $l }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <select name="tipe_kamar" onchange="this.form.submit()" class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none cursor-pointer">
                    <option value="">Semua Tipe</option>
                    @foreach (['Reguler', 'Single Deluxe', 'Double Shared', 'Triple Standard', 'Quad Standard', 'Wisma'] as $tipe)
                        <option value="{{ $tipe }}" {{ request('tipe_kamar') === $tipe ? 'selected' : '' }}>{{ $tipe }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="penuh" {{ request('status') === 'penuh' ? 'selected' : '' }}>Penuh</option>
                    <option value="perbaikan" {{ request('status') === 'perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                </select>
                <a href="{{ route('kamar.index') }}" class="p-2 rounded-lg bg-surface-container-low text-on-surface-variant hover:text-error hover:bg-error-container transition-colors shrink-0" title="Reset Filter">
                    <span class="material-symbols-outlined text-[20px]">restart_alt</span>
                </a>
            </div>
        </div>
    </form>

    {{-- Room Management Data Table --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_8px_rgba(18,32,86,0.04)] overflow-hidden">
        <div class="px-space-lg py-space-md flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="font-title-md text-title-md text-primary font-semibold">Daftar Inventaris Unit Kamar</span>
                <span class="px-2 py-0.5 rounded-full bg-surface-container-low text-secondary font-caption-sm-medium text-caption-sm-medium">{{ $kamars->count() }} Ditampilkan</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/70 text-on-surface-variant font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider">
                        <th class="py-3.5 px-space-md">Unit</th>
                        <th class="py-3.5 px-space-md">Nomor Kamar</th>
                        <th class="py-3.5 px-space-md">Gedung &amp; Lantai</th>
                        <th class="py-3.5 px-space-md">Tipe Kamar</th>
                        <th class="py-3.5 px-space-md">Kapasitas &amp; Okupansi</th>
                        <th class="py-3.5 px-space-md">Fasilitas</th>
                        <th class="py-3.5 px-space-md">Status</th>
                        <th class="py-3.5 px-space-md text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low font-body-md text-body-md">
                    @forelse ($kamars as $kamar)
                        @php
                            $persen = $kamar->okupansi_persen;
                            $barColor = match($kamar->status) {
                                'perbaikan' => 'bg-[#C97D1A]',
                                'penuh' => 'bg-error',
                                default => $persen > 0 ? 'bg-secondary' : 'bg-[#2E9E5B]',
                            };
                            $statusBadge = match($kamar->status) {
                                'penuh' => ['bg' => 'bg-error-container/60 text-error', 'dot' => 'bg-error', 'label' => 'Penuh'],
                                'perbaikan' => ['bg' => 'bg-[#C97D1A]/15 text-[#C97D1A]', 'dot' => 'bg-[#C97D1A]', 'label' => 'Dalam Perbaikan'],
                                default => ['bg' => 'bg-[#2E9E5B]/15 text-[#2E9E5B]', 'dot' => 'bg-[#2E9E5B]', 'label' => 'Tersedia'],
                            };
                        @endphp
                        <tr class="hover:bg-surface-container-low/40 transition-colors {{ $kamar->status === 'perbaikan' ? 'bg-error-container/10' : '' }}">
                            <td class="py-4 px-space-md">
                                <div class="w-16 h-12 rounded-lg overflow-hidden bg-surface-container relative shadow-sm shrink-0 flex items-center justify-center">
                                    @if ($kamar->foto)
                                        <img class="w-full h-full object-cover" src="{{ asset('storage/' . $kamar->foto) }}" alt="Kamar {{ $kamar->nomor_kamar }}">
                                    @else
                                        <span class="material-symbols-outlined text-secondary/50 text-[24px]">bed</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-space-md">
                                <div class="flex flex-col">
                                    <span class="font-headline-sm text-headline-sm text-primary">#{{ $kamar->nomor_kamar }}</span>
                                    @if ($kamar->tarif > 0)
                                        <span class="font-caption-sm text-caption-sm text-on-surface-variant">Rp {{ number_format($kamar->tarif, 0, ',', '.') }}/bln</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-space-md">
                                <div class="flex flex-col">
                                    <span class="font-body-md-medium text-body-md-medium text-primary">{{ $kamar->gedung?->nama }}</span>
                                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">Lantai {{ $kamar->lantai }}{{ $kamar->sayap ? ' • ' . $kamar->sayap : '' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-space-md">
                                <span class="px-2.5 py-1 rounded-md bg-surface-container-high text-primary font-caption-sm-medium text-caption-sm-medium">{{ $kamar->tipe_kamar }}</span>
                            </td>
                            <td class="py-4 px-space-md">
                                <div class="flex flex-col gap-1 w-36">
                                    <div class="flex justify-between items-center text-caption-sm font-caption-sm">
                                        <span class="text-primary font-medium">{{ $kamar->bed_terisi }} / {{ $kamar->kapasitas_bed }} Bed</span>
                                        <span class="font-medium">{{ round($persen) }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-surface-container-low rounded-full overflow-hidden">
                                        <div class="{{ $barColor }} h-full rounded-full" style="width: {{ min(100, $persen) }}%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-space-md">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @forelse (array_slice($kamar->fasilitas ?? [], 0, 3) as $fas)
                                        <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded bg-surface-container-low text-on-surface-variant">{{ $fas }}</span>
                                    @empty
                                        <span class="font-caption-sm text-caption-sm text-outline">Standar</span>
                                    @endforelse
                                    @if (count($kamar->fasilitas ?? []) > 3)
                                        <span class="text-[11px] px-2 py-0.5 rounded bg-surface-container text-secondary">+{{ count($kamar->fasilitas) - 3 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-space-md">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-caption-sm-medium font-caption-sm-medium {{ $statusBadge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span> {{ $statusBadge['label'] }}
                                </span>
                            </td>
                            <td class="py-4 px-space-md text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('kamar.edit', $kamar) }}" class="p-1.5 rounded-md text-on-surface-variant hover:text-primary hover:bg-surface-container-low transition-colors" title="Edit Kamar">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('kamar.destroy', $kamar) }}" onsubmit="return confirm('Hapus kamar #{{ $kamar->nomor_kamar }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-md text-outline hover:text-error hover:bg-error-container/50 transition-colors cursor-pointer" title="Hapus Kamar">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-space-2xl text-center text-on-surface-variant">
                                Belum ada data kamar yang cocok. Klik <strong>Tambah Kamar Baru</strong> untuk menambahkan unit kamar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
