@extends('layouts.app')

@section('title', 'Manajemen Gedung - Andalas Residence')

@section('content')
<div class="flex flex-col gap-space-xl">
    {{-- Top Action & Title Bar --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-1 min-w-0">
            <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Manajemen Gedung</h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                Kelola informasi master data gedung hunian, lantai, fasilitas bersama, dan status operasional real-time di lingkungan asrama Andalas.
            </p>
        </div>
        <div class="flex items-center gap-space-sm shrink-0 flex-wrap">
            <a href="{{ route('kamar.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md shadow-sm hover:bg-surface-container hover:text-secondary transition-all">
                <span class="material-symbols-outlined text-[18px]">bed</span>
                <span>Kelola Kamar</span>
            </a>
            <a href="{{ route('gedung.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-secondary text-on-secondary font-label-md text-label-md shadow-brand-action-lg hover:bg-on-secondary-fixed-variant transition-all transform active:translate-y-0.5">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Tambah Gedung Baru</span>
            </a>
        </div>
    </div>

    {{-- 4 Key Metrics Overview --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Total Gedung</span>
            <div class="mt-space-sm flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $metrics['total_gedung'] }}</span>
                <span class="font-title-md text-title-md text-on-surface-variant font-medium">Unit</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Total Lantai</span>
            <div class="mt-space-sm flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $metrics['total_lantai'] }}</span>
                <span class="font-title-md text-title-md text-on-surface-variant font-medium">Lantai</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Total Kamar</span>
            <div class="mt-space-sm flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $metrics['total_kamar'] }}</span>
                <span class="font-title-md text-title-md text-on-surface-variant font-medium">Unit</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Rata-rata Okupansi</span>
            <div class="mt-space-sm">
                <div class="flex items-baseline justify-between gap-2">
                    <span class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $metrics['okupansi_persen'] }}%</span>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">{{ number_format($metrics['bed_terisi'], 0, ',', '.') }}/{{ number_format($metrics['total_bed'], 0, ',', '.') }} Bed</span>
                </div>
                <div class="w-full bg-surface-container h-2 rounded-full mt-2 overflow-hidden">
                    <div class="bg-secondary h-full rounded-full" style="width: {{ min(100, $metrics['okupansi_persen']) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <form method="GET" action="{{ route('gedung.index') }}" class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-space-md">
        <div class="relative flex-1 max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <input name="q" value="{{ request('q') }}" class="w-full pl-10 pr-space-md py-2.5 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari nama gedung, kode, atau lokasi..." type="text">
        </div>
        <div class="flex items-center gap-space-sm flex-wrap">
            <div class="relative">
                <select name="peruntukan" onchange="this.form.submit()" class="appearance-none bg-surface-container-low px-4 py-2 pr-9 rounded-lg font-body-md-medium text-body-md-medium text-on-surface outline-none cursor-pointer hover:bg-surface-container transition-colors">
                    <option value="">Semua Peruntukan</option>
                    <option value="putra" {{ request('peruntukan') === 'putra' ? 'selected' : '' }}>Khusus Putra</option>
                    <option value="putri" {{ request('peruntukan') === 'putri' ? 'selected' : '' }}>Khusus Putri</option>
                    <option value="campuran" {{ request('peruntukan') === 'campuran' ? 'selected' : '' }}>Campuran / Pascasarjana</option>
                    <option value="tamu" {{ request('peruntukan') === 'tamu' ? 'selected' : '' }}>Guest House / Tamu</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-outline pointer-events-none">expand_more</span>
            </div>

            <div class="relative">
                <select name="status" onchange="this.form.submit()" class="appearance-none bg-surface-container-low px-4 py-2 pr-9 rounded-lg font-body-md-medium text-body-md-medium text-on-surface outline-none cursor-pointer hover:bg-surface-container transition-colors">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif Operasional</option>
                    <option value="persiapan" {{ request('status') === 'persiapan' ? 'selected' : '' }}>Persiapan / Renovasi</option>
                    <option value="draf" {{ request('status') === 'draf' ? 'selected' : '' }}>Draf Tersimpan</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-outline pointer-events-none">expand_more</span>
            </div>

            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary font-label-md text-label-md cursor-pointer">
                Terapkan
            </button>
            @if (request()->hasAny(['q', 'peruntukan', 'status']))
                <a href="{{ route('gedung.index') }}" class="px-3 py-2 rounded-lg bg-surface-container text-on-surface font-caption-sm-medium text-caption-sm-medium hover:bg-surface-container-high">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Building Cards Grid --}}
    @if ($gedungs->isEmpty())
        <div class="bg-surface-container-lowest rounded-xl p-space-2xl text-center shadow-sm">
            <span class="material-symbols-outlined text-[48px] text-outline mb-2">domain_disabled</span>
            <h3 class="font-title-md text-title-md text-primary">Belum ada data gedung</h3>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1 mb-space-md">Tambahkan gedung hunian baru atau sesuaikan filter pencarian Anda.</p>
            <a href="{{ route('gedung.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-secondary text-on-secondary font-label-md text-label-md">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Gedung Baru
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
            @foreach ($gedungs as $gedung)
                @php
                    $totalBed = $gedung->kamars->sum('kapasitas_bed') ?: $gedung->kapasitas_bed;
                    $terisi = $gedung->kamars->sum('bed_terisi');
                    $persen = $totalBed > 0 ? round(($terisi / $totalBed) * 100, 1) : 0;
                    $jumlahKamar = $gedung->kamars->count() ?: $gedung->estimasi_kamar;
                    $kamarTersedia = $gedung->kamars->where('status', 'tersedia')->count();

                    $peruntukanIcon = match($gedung->peruntukan) {
                        'putra' => 'male',
                        'putri' => 'female',
                        default => 'public',
                    };
                    $statusBg = match($gedung->status) {
                        'aktif' => 'bg-success/90',
                        'persiapan' => 'bg-[#C97D1A]/90',
                        default => 'bg-outline/90',
                    };
                    $barColor = $persen >= 90 ? 'bg-[#C97D1A]' : 'bg-secondary';
                @endphp

                <div class="bg-surface-container-lowest rounded-xl shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col group">
                    <div class="relative h-48 w-full overflow-hidden bg-primary-container">
                        @if ($gedung->foto)
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $gedung->foto_url }}" alt="Foto {{ $gedung->nama }}" loading="lazy">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary-container via-primary to-secondary/80 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[64px] text-on-primary/20">apartment</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/85 via-primary/30 to-transparent"></div>

                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-full bg-surface-container-lowest/90 backdrop-blur-md text-primary font-caption-sm-medium text-caption-sm-medium flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-[14px]">{{ $peruntukanIcon }}</span>
                                {{ $gedung->peruntukan_label }}
                            </span>
                        </div>

                        <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between text-on-primary gap-2">
                            <div class="min-w-0">
                                <h2 class="font-headline-sm text-headline-sm text-on-primary truncate">{{ $gedung->nama }}</h2>
                                <p class="font-caption-sm text-caption-sm text-surface-variant flex items-center gap-1 mt-0.5 truncate">
                                    <span class="material-symbols-outlined text-[14px] shrink-0">location_on</span>
                                    <span class="truncate">{{ $gedung->klaster_wilayah }}</span>
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-caption-sm text-caption-sm text-surface-variant block">Kamar Tersedia</span>
                                <span class="font-title-md text-title-md font-bold text-on-primary">{{ $kamarTersedia }} Unit</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-space-lg flex-1 flex flex-col justify-between gap-space-md">
                        <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2">
                            {{ $gedung->deskripsi ?: 'Belum ada deskripsi khusus untuk gedung ini.' }}
                        </p>

                        <div class="grid grid-cols-3 gap-2 py-3 px-4 rounded-xl bg-surface-container-low text-center">
                            <div class="flex flex-col">
                                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tingkat Lantai</span>
                                <span class="font-title-md text-title-md text-primary font-bold">{{ $gedung->jumlah_lantai }} Lantai</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Jumlah Kamar</span>
                                <span class="font-title-md text-title-md text-primary font-bold">{{ $jumlahKamar }} Kamar</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Kapasitas Maks</span>
                                <span class="font-title-md text-title-md text-primary font-bold">{{ $totalBed }} Bed</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <div class="flex justify-between items-center text-caption-sm font-caption-sm text-on-surface-variant">
                                <span>Tingkat Keterisian Bed</span>
                                <span class="font-medium text-primary">{{ $terisi }} / {{ $totalBed }} Bed ({{ $persen }}%)</span>
                            </div>
                            <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                                <div class="{{ $barColor }} h-full rounded-full" style="width: {{ min(100, $persen) }}%"></div>
                            </div>
                        </div>

                        <div class="pt-space-xs flex items-center justify-between gap-2">
                            <a href="{{ route('kamar.index', ['gedung_id' => $gedung->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-surface-container-low text-secondary hover:bg-surface-container font-label-md text-label-md transition-colors">
                                <span class="material-symbols-outlined text-[18px]">bed</span>
                                <span>Kelola Kamar</span>
                            </a>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('gedung.edit', $gedung) }}" class="w-9 h-9 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-secondary hover:bg-surface-container-low transition-colors" title="Edit Gedung">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form method="POST" action="{{ route('gedung.destroy', $gedung) }}" data-confirm-title="Hapus Data Gedung" data-confirm="Apakah Anda yakin ingin menghapus {{ $gedung->nama }} beserta seluruh kamarnya?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-9 h-9 rounded-lg flex items-center justify-center text-outline hover:text-error hover:bg-error-container/40 transition-colors cursor-pointer" title="Hapus Gedung" type="submit">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
