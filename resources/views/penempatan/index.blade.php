@extends('layouts.app')

@section('title', 'Penempatan Kamar - Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary text-[22px]" aria-hidden="true">meeting_room</span>
        <span class="font-label-md text-label-md">Penempatan Kamar</span>
    </div>
@endsection

@section('content')
@php
    $tahunList = $riwayat->pluck('tahun_akademik')->unique()->values();
    $statuses = [
        'aktif' => ['label' => 'Aktif', 'warna' => 'bg-success-container text-success', 'ikon' => 'check_circle'],
        'dipindahkan' => ['label' => 'Dipindahkan', 'warna' => 'bg-surface-container-low text-secondary', 'ikon' => 'sync_alt'],
        'selesai' => ['label' => 'Selesai', 'warna' => 'bg-surface-container-low text-on-surface-variant', 'ikon' => 'task_alt'],
    ];
    foreach ($riwayat->pluck('status_penempatan')->unique() as $value) {
        $statuses[$value] ??= ['label' => \Illuminate\Support\Str::headline($value), 'warna' => 'bg-surface-container-low text-on-surface-variant', 'ikon' => 'info'];
    }
    $tanggal = fn (?string $value) => $value ? \Illuminate\Support\Carbon::parse($value)->locale('id')->translatedFormat('d M Y') : '—';
    $kodePenempatan = fn (int $id) => 'PK-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    $namaPenghuni = auth()->user()?->nama_lengkap ?? 'Client';
@endphp

<div id="penempatan-kamar-page" class="w-full max-w-[1440px] mx-auto flex flex-col gap-space-lg">
    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md pt-1 pb-1">
        <div>
            <h1 class="font-headline-md text-headline-md lg:text-[30px] text-primary">Penempatan Kamar</h1>
            <p class="mt-2 text-body-md text-on-surface-variant">Informasi kamar yang kamu tempati saat ini dan riwayat hunian sebelumnya.</p>
        </div>
    </header>

    @if ($aktif)
        <section data-current-placement="{{ $aktif['id_penempatan'] }}" aria-labelledby="current-placement-title" class="rounded-2xl bg-surface-container-lowest border border-outline-variant/10 shadow-sm overflow-hidden min-w-0">
            <div class="relative isolate overflow-hidden bg-primary-container p-space-lg sm:p-space-xl text-on-primary">
                <span class="material-symbols-outlined absolute -right-4 -bottom-8 text-[180px] text-white/[0.045] -z-10 pointer-events-none" aria-hidden="true">apartment</span>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 id="current-placement-title" class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.1em] text-primary-fixed">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">home_pin</span>
                        Penempatan Saat Ini
                    </h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-success-container px-3 py-1 text-[11px] font-semibold text-on-success-container">
                        <span class="w-1.5 h-1.5 rounded-full bg-current" aria-hidden="true"></span>
                        Sedang ditempati
                    </span>
                </div>
                <div class="flex items-center gap-4 sm:gap-5 mt-6">
                    <span class="hidden sm:flex w-[76px] h-[76px] items-center justify-center rounded-2xl border border-white/10 bg-white/[0.07] shrink-0"><span class="material-symbols-outlined text-[40px] text-primary-fixed" aria-hidden="true">meeting_room</span></span>
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.14em] text-primary-fixed-dim">Nomor Kamar</p>
                        <p class="font-headline-xl text-[40px] sm:text-[46px] leading-tight tracking-tight mt-1">{{ $aktif['nomor_kamar'] }}</p>
                        <p class="text-body-md text-primary-fixed mt-1">{{ $aktif['nama_gedung'] }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 mt-5">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-white/[0.08] px-2.5 py-1.5 text-[11px] text-primary-fixed"><span class="material-symbols-outlined text-[15px]" aria-hidden="true">stairs</span>Lantai {{ $aktif['lantai'] }}</span>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-white/[0.08] px-2.5 py-1.5 text-[11px] text-primary-fixed"><span class="material-symbols-outlined text-[15px]" aria-hidden="true">apartment</span>{{ $aktif['jenis_penghuni'] }}</span>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-white/[0.08] px-2.5 py-1.5 text-[11px] text-primary-fixed"><span class="material-symbols-outlined text-[15px]" aria-hidden="true">calendar_month</span>{{ $aktif['tahun_akademik'] }}</span>
                </div>
            </div>

            <div class="p-space-lg sm:p-space-xl">
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-6">
                    @if ($aktif['nomor_bed'])<div><dt class="text-caption-sm text-on-surface-variant">Bed</dt><dd class="text-label-md font-semibold text-primary">Bed {{ chr(64 + $aktif['nomor_bed']) }}</dd></div>@endif
                    @if ($aktif['rencana_keluar'])<div><dt class="text-caption-sm text-on-surface-variant">Rencana keluar</dt><dd class="text-label-md font-semibold text-primary">{{ $aktif['rencana_keluar'] }}</dd></div>@endif
                    @if ($aktif['jadwal_kunci'])<div class="sm:col-span-2"><dt class="text-caption-sm text-on-surface-variant">Pengambilan kunci</dt><dd class="text-label-md text-primary">{{ $aktif['jadwal_kunci'] }} WIB - {{ $aktif['lokasi_kunci'] }}<br>{{ $aktif['penanggung_jawab'] }} / {{ $aktif['kontak_penanggung_jawab'] }}</dd></div>@endif
                    <div><dt class="text-caption-sm text-on-surface-variant mb-1.5">Tipe kamar</dt><dd class="text-label-md font-semibold text-primary">{{ $aktif['nama_tipe'] }}</dd></div>
                    <div><dt class="text-caption-sm text-on-surface-variant mb-1.5">Kapasitas kamar</dt><dd class="text-label-md font-semibold text-primary">{{ $aktif['kapasitas'] }} orang / kamar</dd></div>
                    <div><dt class="text-caption-sm text-on-surface-variant mb-1.5">Tanggal masuk</dt><dd class="text-label-md font-semibold text-primary">{{ $tanggal($aktif['tanggal_masuk']) }}</dd></div>
                    <div><dt class="text-caption-sm text-on-surface-variant mb-1.5">ID penempatan</dt><dd class="text-label-md font-semibold text-primary">{{ $kodePenempatan($aktif['id_penempatan']) }}</dd></div>
                    <div><dt class="text-caption-sm text-on-surface-variant mb-1.5">ID pendaftaran</dt><dd class="text-label-md font-semibold text-primary">#{{ $aktif['id_pendaftaran'] }}</dd></div>
                    <div><dt class="text-caption-sm text-on-surface-variant mb-1.5">Ditempatkan oleh</dt><dd class="text-label-md font-semibold text-primary">{{ $aktif['admin'] }}</dd></div>
                </dl>
                <div class="flex items-start gap-2.5 mt-6 pt-5 border-t border-surface-container text-caption-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-secondary text-[18px] shrink-0 mt-0.5" aria-hidden="true">person</span>
                    <p>Penempatan untuk <strong class="font-semibold text-primary">{{ $namaPenghuni }}</strong> pada periode <span class="font-medium">{{ $aktif['nama_periode'] }}</span>.</p>
                </div>
            </div>
        </section>

    @else
        <section class="rounded-2xl bg-white border border-outline-variant/20 p-space-xl text-center"><span class="material-symbols-outlined text-secondary text-[40px]" aria-hidden="true">meeting_room</span><h2 class="text-title-lg text-primary mt-3">Belum ada penempatan kamar aktif</h2><p class="text-body-md text-on-surface-variant mt-2">Penempatan kamar akan tampil setelah ditetapkan oleh Admin Layanan.</p></section>
    @endif

    <section class="rounded-2xl bg-surface-container-lowest border border-outline-variant/10 shadow-sm overflow-hidden" aria-labelledby="placement-history-title">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-space-lg pb-5">
            <div>
                <h2 id="placement-history-title" class="font-headline-sm text-headline-sm text-primary">Riwayat Penempatan Kamar</h2>
                <p class="text-caption-sm text-on-surface-variant mt-1.5">Catatan hunian saat ini, perpindahan kamar, dan penempatan pada tahun sebelumnya.</p>
            </div>
            <span class="self-start sm:self-center inline-flex items-center gap-1.5 rounded-lg bg-surface-container-low text-secondary text-caption-sm px-3 py-1.5 whitespace-nowrap"><span class="material-symbols-outlined text-[16px]" aria-hidden="true">history</span>{{ $riwayat->count() }} catatan penempatan</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-space-lg pb-space-lg">
            <div class="grid grid-cols-1 sm:grid-cols-[auto_auto] gap-3 w-full sm:w-auto">
                <div><label for="placement-period-filter" class="sr-only">Filter tahun akademik</label><select id="placement-period-filter" class="w-full sm:w-60 h-11 rounded-xl bg-surface-container-low px-3 text-label-md outline-none focus:ring-2 focus:ring-secondary cursor-pointer"><option value="semua">Semua Tahun Akademik</option>@foreach ($tahunList as $tahun)<option value="{{ $tahun }}">{{ $tahun }}</option>@endforeach</select></div>
                <div><label for="placement-status-filter" class="sr-only">Filter status penempatan</label><select id="placement-status-filter" class="w-full sm:w-44 h-11 rounded-xl bg-surface-container-low px-3 text-label-md outline-none focus:ring-2 focus:ring-secondary cursor-pointer"><option value="semua">Semua Status</option>@foreach ($statuses as $value => $status)<option value="{{ $value }}">{{ $status['label'] }}</option>@endforeach</select></div>
            </div>
            <button id="placement-reset-filter" type="button" class="self-start sm:self-center inline-flex items-center gap-1.5 text-caption-sm font-medium text-secondary py-2 cursor-pointer hover:underline"><span class="material-symbols-outlined text-[17px]" aria-hidden="true">restart_alt</span>Reset filter</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1060px] text-left">
                <caption class="sr-only">Riwayat penempatan kamar Client, disusun dari penempatan terbaru. Hanya satu penempatan berstatus aktif.</caption>
                <thead class="bg-surface-container-low text-on-surface-variant font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider">
                    <tr><th scope="col" class="py-4 pl-space-lg pr-space-md">ID Penempatan</th><th scope="col" class="p-space-md">Kamar &amp; Gedung</th><th scope="col" class="p-space-md">Periode</th><th scope="col" class="p-space-md">Tanggal Masuk</th><th scope="col" class="p-space-md">Tanggal Keluar</th><th scope="col" class="p-space-md">Status</th><th scope="col" class="py-4 pl-space-md pr-space-lg">Admin Penempat</th></tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @foreach ($riwayat as $penempatan)
                        @php $status = $statuses[$penempatan['status_penempatan']]; @endphp
                        <tr data-placement-row data-placement-id="{{ $penempatan['id_penempatan'] }}" data-period="{{ $penempatan['tahun_akademik'] }}" data-status="{{ $penempatan['status_penempatan'] }}" data-start="{{ $penempatan['tanggal_masuk'] }}" data-end="{{ $penempatan['tanggal_keluar'] }}" class="{{ $penempatan['status_penempatan'] === 'aktif' ? 'bg-success-container' : 'hover:bg-surface-container-low/50' }} transition-colors">
                            <td class="py-5 pl-space-lg pr-space-md"><p class="text-label-md font-semibold text-secondary whitespace-nowrap">{{ $kodePenempatan($penempatan['id_penempatan']) }}</p><p class="text-[11px] text-on-surface-variant mt-1">Pendaftaran #{{ $penempatan['id_pendaftaran'] }}</p></td>
                            <td class="p-space-md"><div class="flex items-center gap-2.5"><span class="w-9 h-9 rounded-lg bg-surface-container-low text-secondary shrink-0 flex items-center justify-center"><span class="material-symbols-outlined text-[19px]" aria-hidden="true">meeting_room</span></span><div><p class="text-label-md font-semibold text-primary">{{ $penempatan['nomor_kamar'] }} <span class="font-normal text-[11px] text-on-surface-variant">· Lantai {{ $penempatan['lantai'] }}</span></p><p class="text-[11px] text-on-surface-variant mt-1">{{ $penempatan['nama_singkat'] }}</p></div></div></td>
                            <td class="p-space-md"><p class="text-label-md font-medium text-primary">{{ $penempatan['tahun_akademik'] }}</p><p class="text-[11px] leading-4 text-on-surface-variant mt-1 max-w-40">{{ $penempatan['nama_periode'] }}</p></td>
                            <td class="p-space-md text-label-md whitespace-nowrap">{{ $tanggal($penempatan['tanggal_masuk']) }}</td>
                            <td class="p-space-md"><p class="text-label-md whitespace-nowrap">{{ $tanggal($penempatan['tanggal_keluar']) }}</p>@if ($penempatan['status_penempatan'] === 'aktif')<p class="text-[11px] text-success mt-1">Masih ditempati</p>@endif</td>
                            <td class="p-space-md"><span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-medium whitespace-nowrap {{ $status['warna'] }}"><span class="material-symbols-outlined text-[14px]" aria-hidden="true">{{ $status['ikon'] }}</span>{{ $status['label'] }}</span></td>
                            <td class="py-5 pl-space-md pr-space-lg text-label-md text-on-surface-variant max-w-44">{{ $penempatan['admin'] }}</td>
                        </tr>
                    @endforeach
                    <tr id="placement-history-empty" hidden><td colspan="7" class="text-center px-space-lg py-12"><span class="material-symbols-outlined text-[32px] text-secondary mb-3" aria-hidden="true">history_toggle_off</span><p class="font-title-md text-title-md text-primary">Riwayat tidak ditemukan</p><p class="text-caption-sm text-on-surface-variant mt-1">{{ $riwayat->isEmpty() ? 'Belum ada riwayat penempatan kamar yang tercatat.' : 'Sesuaikan filter tahun akademik atau status penempatan.' }}</p></td></tr>
                </tbody>
            </table>
        </div>
        <footer class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-surface-container px-space-lg py-4"><p id="placement-history-count" role="status" aria-live="polite" class="text-caption-sm text-on-surface-variant">Menampilkan {{ $riwayat->count() }} dari {{ $riwayat->count() }} catatan penempatan</p><span class="text-[11px] text-outline">Diurutkan dari penempatan terbaru</span></footer>
    </section>

</div>
@endsection
