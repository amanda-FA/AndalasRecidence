@extends('layouts.app')

@section('title', 'Dashboard Okupansi Pimpinan - Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-lg">
        <div class="flex items-center gap-space-sm px-space-md py-space-xs rounded-xl bg-surface-container-low text-on-surface">
            <span class="material-symbols-outlined text-secondary text-[20px]">calendar_month</span>
            <label class="sr-only" for="semester-select">Periode Akademik</label>
            <select class="bg-transparent font-label-md text-label-md text-on-surface outline-none cursor-pointer pr-space-xs" id="semester-select">
                <option>Semester Ganjil 2025/2026</option>
                <option>Semester Genap 2024/2025</option>
                <option>Semester Ganjil 2024/2025</option>
            </select>
        </div>
        <div class="hidden xl:flex items-center gap-space-xs px-space-md py-space-xs rounded-full bg-surface-container text-secondary font-caption-sm-medium text-caption-sm-medium">
            <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
            Sistem Terintegrasi Kampus
        </div>
    </div>
@endsection

@section('navbar-actions')
    <div class="flex items-center gap-space-xs">
        <button aria-label="Pemberitahuan" class="w-10 h-10 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors relative cursor-pointer" type="button">
            <span class="material-symbols-outlined text-[20px]">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-error"></span>
        </button>
        <button aria-label="Bantuan Eksekutif" class="w-10 h-10 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors cursor-pointer" type="button">
            <span class="material-symbols-outlined text-[20px]">help_outline</span>
        </button>
    </div>
@endsection

@section('content')
<div class="flex flex-col w-full">
    {{-- Top Executive Action Header & Controls --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-space-lg mb-space-xl">
        <div class="flex flex-col">
            <div class="flex items-center gap-space-xs text-secondary mb-space-xs">
                <span class="material-symbols-outlined text-[18px]">analytics</span>
                <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-widest text-secondary">Pengawasan Eksekutif Aset Kampus</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Dashboard Eksekutif &amp; Statistik Okupansi</h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mt-1">
                Ringkasan tingkat hunian real-time, evaluasi kapasitas bed residensial, dan proyeksi ketersediaan kuota Semester Ganjil 2025/2026 di seluruh {{ $kpi['total_gedung'] }} klaster asrama.
            </p>
        </div>

        {{-- Executive Quick Controls & Export Actions --}}
        <div class="flex flex-wrap items-center gap-space-sm">
            <form method="GET" action="{{ route('dashboard.okupansi') }}" class="flex items-center bg-surface-container-lowest shadow-sm rounded-xl px-space-md py-space-xs">
                <span class="material-symbols-outlined text-secondary text-[20px] mr-2">filter_alt</span>
                <label class="sr-only" for="filter-gedung-header">Filter Klaster Gedung</label>
                <select name="klaster" onchange="this.form.submit()" class="bg-transparent font-label-md text-label-md text-on-surface outline-none cursor-pointer pr-space-xs" id="filter-gedung-header">
                    <option value="all" {{ $klasterFilter === 'all' ? 'selected' : '' }}>Semua Klaster (9 Gedung)</option>
                    <option value="putra" {{ $klasterFilter === 'putra' ? 'selected' : '' }}>Klaster Putra (Menpera, Rusunawa, Pupera Putra)</option>
                    <option value="putri" {{ $klasterFilter === 'putri' ? 'selected' : '' }}>Klaster Putri (RPX, Pupera, Hijau, Orange)</option>
                    <option value="khusus" {{ $klasterFilter === 'khusus' ? 'selected' : '' }}>Klaster Khusus &amp; Tamu (Rosmainshev, Akes)</option>
                </select>
            </form>

            <div class="relative inline-block text-left">
                <button class="flex items-center gap-space-xs px-space-lg py-space-sm bg-secondary text-on-secondary rounded-xl font-label-md text-label-md shadow-sm hover:bg-secondary/90 transition-all duration-200 cursor-pointer" id="btn-export-exec" type="button">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Ekspor Laporan Eksekutif</span>
                    <span class="material-symbols-outlined text-[18px] ml-1">expand_more</span>
                </button>
                <div class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-surface-container-lowest shadow-xl z-30 py-2" id="export-dropdown">
                    <a class="flex items-center gap-2 px-4 py-2.5 font-label-md text-label-md text-on-surface hover:bg-surface-container-low transition-colors" href="#" onclick="window.print(); return false;">
                        <span class="material-symbols-outlined text-error text-[20px]">picture_as_pdf</span>
                        <span>Cetak / PDF (.PDF)</span>
                    </a>
                    <a class="flex items-center gap-2 px-4 py-2.5 font-label-md text-label-md text-on-surface hover:bg-surface-container-low transition-colors" href="#" onclick="alert('Fitur ekspor Excel akan tersedia setelah integrasi database.'); return false;">
                        <span class="material-symbols-outlined text-success text-[20px]">table_view</span>
                        <span>Dataset Spreadsheet (.Excel)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Hero Insight Bar --}}
    <div class="relative overflow-hidden rounded-2xl bg-primary-container text-surface-container-lowest p-space-lg mb-space-xl shadow-md">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-md">
                <div class="w-12 h-12 rounded-xl bg-secondary flex items-center justify-center text-on-secondary shadow-sm shrink-0">
                    <span class="material-symbols-outlined text-[28px]">verified</span>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-headline-sm text-headline-sm text-surface-container-lowest">Indeks Keterisian Optimal</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-[#2E9E5B]/20 text-[#2E9E5B] font-caption-sm-medium text-caption-sm-medium">Target Kampus Tercapai</span>
                    </div>
                    <p class="font-body-md text-body-md text-secondary-fixed-dim">
                        Tingkat hunian keseluruhan mencapai {{ $kpi['persentase_okupansi'] }}% (batas kelayakan 85.0%), dengan {{ number_format($kpi['total_terisi'], 0, ',', '.') }} tempat tidur terisi aktif.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-space-lg self-start md:self-auto pl-space-md md:pl-0 shrink-0">
                <div class="text-right">
                    <span class="font-caption-sm text-caption-sm text-secondary-fixed-dim uppercase tracking-wider block">Sinkronisasi Data</span>
                    <span class="font-label-md text-label-md text-surface-container-lowest flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-[#2E9E5B] animate-ping"></span> Real-time: {{ now()->format('d M Y, H:i') }} WIB
                    </span>
                </div>
            </div>
        </div>
        <div class="absolute -right-8 -bottom-12 w-64 h-64 rounded-full bg-secondary/20 blur-3xl pointer-events-none"></div>
    </div>

    {{-- Section 1: Executive KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-space-md mb-space-xl">
        {{-- KPI 1 --}}
        <div class="flex flex-col justify-between p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-space-sm">
                <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider text-on-surface-variant">Kapasitas Hunian</span>
                <div class="w-9 h-9 rounded-xl bg-surface-container-low flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[20px]">hotel</span>
                </div>
            </div>
            <div>
                <div class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ number_format($kpi['total_bed'], 0, ',', '.') }}</div>
                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">Total Unit Bed Resmi</p>
            </div>
            <div class="mt-space-md pt-space-xs flex items-center justify-between text-on-surface-variant font-caption-sm text-caption-sm">
                <span>Batas Plafon Alokasi</span>
                <span class="font-label-md text-label-md text-primary">100% Total</span>
            </div>
        </div>

        {{-- KPI 2 --}}
        <div class="flex flex-col justify-between p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-space-sm">
                <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider text-on-surface-variant">Okupansi Terisi</span>
                <div class="w-9 h-9 rounded-xl bg-success/15 flex items-center justify-center text-success">
                    <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ number_format($kpi['total_terisi'], 0, ',', '.') }}</span>
                    <span class="font-title-md text-title-md text-success">Bed</span>
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-success/10 text-success font-label-md text-label-md">
                        <span class="material-symbols-outlined text-[16px] mr-0.5">trending_up</span> {{ $kpi['persentase_okupansi'] }}%
                    </span>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tingkat Okupansi</span>
                </div>
            </div>
            <div class="mt-space-md pt-space-xs flex items-center justify-between text-on-surface-variant font-caption-sm text-caption-sm">
                <span>Keterisian Aktif</span>
                <span class="font-label-md text-label-md text-success">+4.2% vs Sem. Lalu</span>
            </div>
        </div>

        {{-- KPI 3 --}}
        <div class="flex flex-col justify-between p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-space-sm">
                <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider text-on-surface-variant">Kapasitas Tersedia</span>
                <div class="w-9 h-9 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-[20px]">event_available</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ number_format($kpi['total_tersedia'], 0, ',', '.') }}</span>
                    <span class="font-title-md text-title-md text-secondary">Bed</span>
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-md text-label-md">
                        {{ $kpi['persentase_tersedia'] }}%
                    </span>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">Siap Alokasi</span>
                </div>
            </div>
            <div class="mt-space-md pt-space-xs flex items-center justify-between text-on-surface-variant font-caption-sm text-caption-sm">
                <span>Cadangan Darurat</span>
                <span class="font-label-md text-label-md text-on-surface">50 Bed Aman</span>
            </div>
        </div>

        {{-- KPI 4 --}}
        <div class="flex flex-col justify-between p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-space-sm">
                <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider text-on-surface-variant">Pemeliharaan</span>
                <div class="w-9 h-9 rounded-xl bg-[#C97D1A]/15 flex items-center justify-center text-[#C97D1A]">
                    <span class="material-symbols-outlined text-[20px]">build_circle</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ $kpi['kamar_perbaikan'] }}</span>
                    <span class="font-title-md text-title-md text-[#C97D1A]">Kamar</span>
                </div>
                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">Dalam Perbaikan Sanitasi &amp; AC</p>
            </div>
            <div class="mt-space-md pt-space-xs flex items-center justify-between text-on-surface-variant font-caption-sm text-caption-sm">
                <span>Estimasi Selesai</span>
                <span class="font-label-md text-label-md text-[#C97D1A]">5 Hari Kerja</span>
            </div>
        </div>

        {{-- KPI 5 --}}
        <div class="flex flex-col justify-between p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-space-sm">
                <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider text-on-surface-variant">Klaster Aktif</span>
                <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-primary-container">
                    <span class="material-symbols-outlined text-[20px]">domain</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ $kpi['total_gedung'] }}</span>
                    <span class="font-title-md text-title-md text-on-surface-variant">Gedung</span>
                </div>
                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">Operasional Penuh 24/7</p>
            </div>
            <div class="mt-space-md pt-space-xs flex items-center justify-between text-on-surface-variant font-caption-sm text-caption-sm">
                <span>Taraf Hunian</span>
                <span class="font-label-md text-label-md text-primary">Standar SNI</span>
            </div>
        </div>
    </div>

    {{-- Section 2: Visualisasi Statistik & Grafik Okupansi --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg mb-space-xl">
        {{-- Distribusi Okupansi per Gedung --}}
        <div class="lg:col-span-8 flex flex-col p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs mb-space-lg">
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Distribusi Okupansi per Gedung</h2>
                    <p class="font-caption-sm text-caption-sm text-on-surface-variant">Urutan efektivitas utilisasi kapasitas bed pada {{ $kpi['total_gedung'] }} unit asrama aktif</p>
                </div>
                <div class="flex flex-wrap items-center gap-space-sm self-start sm:self-auto">
                    <span class="flex items-center gap-1 font-caption-sm text-caption-sm text-on-surface-variant">
                        <span class="w-3 h-3 rounded bg-success"></span> &gt;90% Optimal
                    </span>
                    <span class="flex items-center gap-1 font-caption-sm text-caption-sm text-on-surface-variant">
                        <span class="w-3 h-3 rounded bg-secondary"></span> 80-89% Ideal
                    </span>
                    <span class="flex items-center gap-1 font-caption-sm text-caption-sm text-on-surface-variant">
                        <span class="w-3 h-3 rounded bg-secondary-fixed-dim"></span> &lt;80% Tersedia
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-space-md">
                @foreach ($gedungList as $gedung)
                    @php
                        $barColor = $gedung['persentase'] >= 90
                            ? 'bg-success'
                            : ($gedung['persentase'] >= 80 ? 'bg-secondary' : 'bg-secondary-fixed-dim');
                        $textColor = $gedung['persentase'] >= 90
                            ? 'text-success'
                            : ($gedung['persentase'] >= 80 ? 'text-secondary' : 'text-on-secondary-fixed-variant');
                    @endphp
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between font-label-md text-label-md">
                            <div class="flex items-center gap-2">
                                <span class="w-28 text-on-surface font-title-md text-title-md truncate">{{ $gedung['nama_singkat'] }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-surface-container-low text-on-surface-variant font-caption-sm text-caption-sm">{{ $gedung['terisi'] }} / {{ $gedung['total_bed'] }} Bed</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-caption-sm text-caption-sm text-on-surface-variant hidden sm:inline">{{ $gedung['tersedia'] }} Tersedia</span>
                                <span class="font-label-md text-label-md {{ $textColor }} w-12 text-right">{{ number_format($gedung['persentase'], 1) }}%</span>
                            </div>
                        </div>
                        <div class="w-full h-3 rounded-full bg-surface-container-low overflow-hidden flex">
                            <div class="h-full {{ $barColor }} rounded-full transition-all duration-700" style="width: {{ $gedung['persentase'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Komposisi Kategori Penghuni --}}
        <div class="lg:col-span-4 flex flex-col justify-between p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm">
            <div>
                <div class="flex items-center justify-between mb-space-sm">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Komposisi Penghuni</h2>
                    <span class="w-8 h-8 rounded-full bg-surface-container-low flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[18px]">group</span>
                    </span>
                </div>
                <p class="font-caption-sm text-caption-sm text-on-surface-variant mb-space-lg">Proporsi demografi 2.512 residen berstatus aktif</p>

                <div class="flex items-center justify-center py-space-sm mb-space-md relative">
                    <svg class="w-44 h-44 transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-surface-container" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4.2"></path>
                        <path class="text-secondary" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="52, 100" stroke-linecap="round" stroke-width="4.2"></path>
                        <path class="text-[#2E9E5B]" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="36, 100" stroke-dashoffset="-52" stroke-linecap="round" stroke-width="4.2"></path>
                        <path class="text-secondary-fixed-dim" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="8, 100" stroke-dashoffset="-88" stroke-linecap="round" stroke-width="4.2"></path>
                        <path class="text-on-primary-fixed-variant" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="4, 100" stroke-dashoffset="-96" stroke-linecap="round" stroke-width="4.2"></path>
                    </svg>
                    <div class="absolute flex flex-col items-center justify-center pointer-events-none">
                        <span class="font-headline-md text-headline-md text-on-surface">2.512</span>
                        <span class="font-caption-sm text-caption-sm text-on-surface-variant">Penghuni</span>
                    </div>
                </div>

                <div class="flex flex-col gap-space-sm">
                    @foreach ($komposisi as $item)
                        <div class="flex items-center justify-between p-2 rounded-xl bg-surface-container-low">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full {{ $item['dot'] }}"></span>
                                <span class="font-label-md text-label-md text-on-surface">{{ $item['label'] }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-label-md text-label-md text-on-surface">{{ number_format($item['jumlah'], 0, ',', '.') }} Bed</span>
                                <span class="font-caption-sm text-caption-sm text-on-surface-variant block">{{ $item['persen'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-space-md pt-space-xs text-center">
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Subsidi hunian KIP-K terjamin melalui DIPA Rektorat</span>
            </div>
        </div>
    </div>

    {{-- Section 3: Tabel Detail Ringkasan Okupansi Gedung --}}
    <div class="flex flex-col bg-surface-container-lowest rounded-2xl shadow-sm mb-space-xl overflow-hidden">
        <div class="p-space-lg flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm bg-surface-container-lowest">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Rekapitulasi Ketersediaan &amp; Okupansi Gedung</h2>
                <p class="font-caption-sm text-caption-sm text-on-surface-variant">Data analitik komprehensif kapasitas ruang dan utilisasi ranjang per klaster</p>
            </div>
            <div class="flex items-center gap-space-xs">
                <div class="relative">
                    <input class="pl-9 pr-3 py-1.5 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md outline-none focus:ring-2 focus:ring-secondary/20" id="table-search" placeholder="Cari gedung..." type="text">
                    <span class="material-symbols-outlined absolute left-2.5 top-2 text-[18px] text-on-surface-variant">search</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider">
                        <th class="py-space-md px-space-lg">Nama Gedung</th>
                        <th class="py-space-md px-space-md">Klaster &amp; Gender</th>
                        <th class="py-space-md px-space-md text-center">Kamar</th>
                        <th class="py-space-md px-space-md text-right">Total Bed</th>
                        <th class="py-space-md px-space-md text-right">Terisi</th>
                        <th class="py-space-md px-space-md text-right">Tersedia</th>
                        <th class="py-space-md px-space-md w-48">Persentase Okupansi</th>
                        <th class="py-space-md px-space-md text-center">Status Kesiapan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container text-on-surface font-body-md text-body-md">
                    @foreach ($gedungList as $gedung)
                        @php
                            $barColor = $gedung['persentase'] >= 90
                                ? 'bg-success'
                                : ($gedung['persentase'] >= 80 ? 'bg-secondary' : 'bg-secondary-fixed-dim');
                            $textColor = $gedung['persentase'] >= 90
                                ? 'text-success'
                                : ($gedung['persentase'] >= 80 ? 'text-secondary' : 'text-on-surface-variant');
                        @endphp
                        <tr class="hover:bg-surface-container-low/60 transition-colors">
                            <td class="py-space-md px-space-lg">
                                <div class="flex items-center gap-space-sm">
                                    <div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center font-label-md text-label-md text-secondary">{{ $gedung['kode'] }}</div>
                                    <div class="flex flex-col">
                                        <span class="font-label-md text-label-md text-on-surface">{{ $gedung['nama'] }}</span>
                                        <span class="font-caption-sm text-caption-sm text-on-surface-variant">{{ $gedung['blok'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-space-md px-space-md">
                                <span class="px-2.5 py-1 rounded-full bg-surface-container {{ $gedung['klaster_color'] }} font-caption-sm-medium text-caption-sm-medium">{{ $gedung['klaster_label'] }}</span>
                            </td>
                            <td class="py-space-md px-space-md text-center font-label-md text-label-md">{{ $gedung['kamar'] }}</td>
                            <td class="py-space-md px-space-md text-right font-label-md text-label-md">{{ $gedung['total_bed'] }}</td>
                            <td class="py-space-md px-space-md text-right {{ $textColor }} font-label-md text-label-md">{{ $gedung['terisi'] }}</td>
                            <td class="py-space-md px-space-md text-right font-label-md text-label-md">{{ $gedung['tersedia'] }}</td>
                            <td class="py-space-md px-space-md">
                                <div class="flex items-center gap-2">
                                    <div class="w-full h-2 rounded-full bg-surface-container overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $gedung['persentase'] }}%"></div>
                                    </div>
                                    <span class="font-caption-sm-medium text-caption-sm-medium {{ $textColor }} w-10 text-right">{{ round($gedung['persentase']) }}%</span>
                                </div>
                            </td>
                            <td class="py-space-md px-space-md text-center">
                                <span class="px-3 py-1 rounded-full {{ $gedung['status_style'] }} font-caption-sm-medium text-caption-sm-medium">{{ $gedung['status'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-space-md bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm text-on-surface-variant font-caption-sm text-caption-sm">
            <div class="flex items-center gap-space-md">
                <span>Menampilkan <strong>{{ $kpi['total_gedung'] }}</strong> Gedung Aktif</span>
                <span>Akumulasi: <strong>{{ number_format($kpi['total_bed'], 0, ',', '.') }} Bed Total</strong></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-on-surface">Tingkat Utilisasi Rata-rata: <strong>{{ $kpi['persentase_okupansi'] }}%</strong></span>
            </div>
        </div>
    </div>

    {{-- Section 4: Widget Catatan Strategis Pimpinan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
        <div class="flex flex-col p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm">
            <div class="flex items-center gap-space-sm mb-space-md">
                <div class="w-10 h-10 rounded-xl bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                    <span class="material-symbols-outlined text-[22px]">lightbulb</span>
                </div>
                <div>
                    <h3 class="font-title-md text-title-md text-on-surface">Rekomendasi Alokasi</h3>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">Proyeksi Semester Genap</span>
                </div>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mb-space-md">
                Mengingat <strong>Gedung Menpera (96%)</strong> dan <strong>Pupera (94%)</strong> mendekati ambang batas jenuh, disarankan:
            </p>
            <ul class="flex flex-col gap-space-sm font-body-md text-body-md text-on-surface mb-auto">
                <li class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-secondary text-[18px] mt-0.5">check_circle</span>
                    <span>Alihkan gelombang pendaftar baru reguler ke <strong>Gedung Hijau (44 bed kosong)</strong> dan <strong>Gedung Orange (45 bed)</strong>.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-secondary text-[18px] mt-0.5">check_circle</span>
                    <span>Buka skema pemesanan grup pertukaran mahasiswa di <strong>Rosmainshev</strong> guna memaksimalkan sisa kapasitas 45 bed.</span>
                </li>
            </ul>
        </div>

        <div class="flex flex-col p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm">
            <div class="flex items-center gap-space-sm mb-space-md">
                <div class="w-10 h-10 rounded-xl bg-[#C97D1A]/15 flex items-center justify-center text-[#C97D1A]">
                    <span class="material-symbols-outlined text-[22px]">warning_amber</span>
                </div>
                <div>
                    <h3 class="font-title-md text-title-md text-on-surface">Peringatan Pemeliharaan</h3>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">24 Kamar Dalam Perbaikan</span>
                </div>
            </div>
            <div class="p-space-md rounded-xl bg-surface-container-low mb-space-md">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-label-md text-label-md text-on-surface">Progress Turn-Over Kamar</span>
                    <span class="font-label-md text-label-md text-[#C97D1A]">68% Rampung</span>
                </div>
                <div class="w-full h-2 rounded-full bg-surface-container-high overflow-hidden">
                    <div class="h-full bg-[#C97D1A] rounded-full" style="width: 68%"></div>
                </div>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant mt-2 block">16 dari 24 kamar ditargetkan kembali layak huni dalam 5 hari ke depan sebelum gelombang residen khusus tiba.</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mb-auto">
                Instruksi Direktur Fasilitas: Tunda penerimaan alokasi tamu pada Sayap C Rusunawa hingga audit kelistrikan mandiri tuntas disahkan.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const exportBtn = document.getElementById('btn-export-exec');
        const exportDropdown = document.getElementById('export-dropdown');

        if (exportBtn && exportDropdown) {
            exportBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                exportDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!exportDropdown.contains(e.target) && !exportBtn.contains(e.target)) {
                    exportDropdown.classList.add('hidden');
                }
            });
        }

        const searchInput = document.getElementById('table-search');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }
    })();
</script>
@endpush
