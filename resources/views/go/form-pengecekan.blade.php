@extends('layouts.app')

@section('title', 'Form Pengecekan Kamar - Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary text-[22px]">checklist</span>
        <span class="font-label-md text-label-md">Audit Fisik & Inventaris</span>
    </div>
@endsection

@section('content')
<div class="flex flex-col w-full max-w-[1200px] mx-auto gap-space-xl pb-space-2xl">

    {{-- Breadcrumb & Top Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
        <div class="flex items-center gap-space-sm flex-wrap">
            <a href="{{ route('go.pengecekan-kamar.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-primary transition-colors text-label-md font-medium">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali ke Antrean</span>
            </a>
            <span class="text-on-surface-variant/40">/</span>
            <span class="font-caption-sm text-caption-sm text-on-surface-variant">Pengecekan Kamar</span>
            <span class="text-on-surface-variant/40">/</span>
            <span class="font-caption-sm-medium text-caption-sm-medium text-secondary">Formulir Lapangan</span>
        </div>

        <div class="flex items-center gap-space-sm">
            <span class="px-3 py-1 rounded-full bg-[#C97D1A]/15 text-[#C97D1A] font-caption-sm-medium text-caption-sm-medium flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-[#C97D1A] animate-pulse"></span>
                Sedang Diperiksa / In Progress
            </span>
            <div class="flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-lowest border border-outline-variant/30 text-on-surface-variant font-caption-sm-medium text-caption-sm-medium">
                <span class="material-symbols-outlined text-[16px] text-secondary">timer</span>
                <span id="timer">00:00:00</span>
            </div>
        </div>
    </div>

    {{-- Header Title --}}
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-space-xs border-b border-surface-container pb-space-md">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Formulir Pengecekan Kamar</h1>
                <span class="px-2.5 py-0.5 rounded-lg bg-secondary/10 text-secondary font-mono text-[13px] font-semibold">#CHK-2026-0089</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Audit fisik inventaris kamar hunian dan identifikasi kerusakan sebelum checkout mahasiswa.</p>
        </div>
        <span class="font-caption-sm text-caption-sm text-on-surface-variant">Reg ID: {{ $nomorAudit }}</span>
    </div>

    {{-- Ringkasan Identitas Pengajuan & Alokasi Kamar --}}
    <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
        <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
            <h2 class="font-title-md text-title-md text-primary flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[20px]">person_pin_circle</span>
                Informasi Pemohon & Alokasi Hunian
            </h2>
            <span class="px-2.5 py-0.5 rounded-full bg-success/10 text-success text-[11px] font-medium">Status Pengajuan: Disetujui</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-lg">
            {{-- Mahasiswa --}}
            <div class="flex items-start gap-space-sm">
                <div class="w-11 h-11 rounded-xl bg-primary flex items-center justify-center text-on-primary font-bold text-base shrink-0 shadow-sm">
                    {{ strtoupper(substr($pengguna->nama_lengkap ?? 'M', 0, 1)) }}
                </div>
                <div>
                    <span class="text-[11px] uppercase tracking-wider font-medium text-on-surface-variant block">Mahasiswa</span>
                    <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ $pengguna->nama_lengkap ?? 'Muhammad Fadhil Ar-Rayyan' }}</span>
                    <span class="font-caption-sm text-caption-sm text-secondary font-mono">NIM: 2211522004</span>
                </div>
            </div>

            {{-- Alokasi Kamar --}}
            <div class="flex items-start gap-space-sm">
                <div class="w-11 h-11 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary shrink-0">
                    <span class="material-symbols-outlined text-[24px]">meeting_room</span>
                </div>
                <div>
                    <span class="text-[11px] uppercase tracking-wider font-medium text-on-surface-variant block">Alokasi Kamar</span>
                    <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ $gedung->nama_gedung ?? 'Gedung Menpora Putra' }}</span>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">Kamar {{ $kamar->nomor_kamar ?? '202' }} &mdash; Bed A</span>
                </div>
            </div>

            {{-- Jadwal Check-out --}}
            <div class="flex items-start gap-space-sm">
                <div class="w-11 h-11 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary shrink-0">
                    <span class="material-symbols-outlined text-[24px]">calendar_month</span>
                </div>
                <div>
                    <span class="text-[11px] uppercase tracking-wider font-medium text-on-surface-variant block">Rencana Check-out</span>
                    <span class="font-label-md text-label-md text-on-surface font-semibold block">
                        {{ $checkout->tanggal_rencanaKeluar ? \Carbon\Carbon::parse($checkout->tanggal_rencanaKeluar)->translatedFormat('d F Y') : now()->addDays(2)->translatedFormat('d F Y') }}
                    </span>
                    <span class="font-caption-sm text-caption-sm text-[#C97D1A]">Pukul 10:00 WIB</span>
                </div>
            </div>

            {{-- Petugas GO --}}
            <div class="flex items-start gap-space-sm">
                <div class="w-11 h-11 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary shrink-0">
                    <span class="material-symbols-outlined text-[24px]">badge</span>
                </div>
                <div>
                    <span class="text-[11px] uppercase tracking-wider font-medium text-on-surface-variant block">Petugas Pemeriksa</span>
                    <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ auth()->user()->nama_lengkap ?? 'Rahardian Pratama, A.Md.' }}</span>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">General Officer (GO)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Pemeriksaan Fisik & Fasilitas --}}
    <form id="form-audit" action="{{ route('go.pengecekan-kamar.selesai', $checkout->id_pengajuan) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-space-xl">
        @csrf

        {{-- Bagian A: Kondisi Fisik Ruangan & Bangunan --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <div class="flex items-center gap-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[20px]">door_front</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">Bagian A: Kondisi Fisik Ruangan & Bangunan</h2>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Pengecekan dinding, cat, plafon, pintu utama, jendela, dan ubin keramik.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant font-caption-sm-medium text-caption-sm-medium">3 Komponen</span>
            </div>

            <div class="flex flex-col divide-y divide-surface-container">
                @php
                    $itemsA = [
                        ['id' => 'dinding_plafon', 'num' => 1, 'nama' => 'Dinding, Cat, & Plafon Kamar', 'desc' => 'Dinding bersih tanpa coretan permanen, cat tidak mengelupas parah, plafon gypsum rapi tanpa bekas rembesan air', 'icon' => 'wallpaper', 'default' => 'baik'],
                        ['id' => 'pintu_handel', 'num' => 2, 'nama' => 'Pintu Utama, Kunci, & Handel', 'desc' => 'Daun pintu rapat, handel pintu kokoh, engsel tidak berderit, silinder kunci mekanis berfungsi sempurna', 'icon' => 'door_sliding', 'default' => 'baik'],
                        ['id' => 'jendela_lantai', 'num' => 3, 'nama' => 'Jendela Kaca & Ubin Keramik', 'desc' => 'Kaca jendela utuh tanpa retak, gerendel/kait jendela mengunci rapat, keramik lantai bersih & tidak pecah', 'icon' => 'grid_view', 'default' => 'baik'],
                    ];
                @endphp

                @foreach ($itemsA as $item)
                    <div class="py-space-md flex flex-col gap-space-sm checklist-row" data-id="{{ $item['id'] }}">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                            <div class="flex items-start gap-space-sm max-w-xl">
                                <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">{{ $item['icon'] }}</span>
                                <div>
                                    <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ $item['num'] }}. {{ $item['nama'] }}</span>
                                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">{{ $item['desc'] }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-space-md self-start md:self-center shrink-0">
                                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-on-surface">
                                    <input type="radio" name="checklist_a[{{ $item['id'] }}]" value="baik" class="accent-secondary audit-radio" {{ $item['default'] === 'baik' ? 'checked' : '' }} required>
                                    <span>Baik</span>
                                </label>
                                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-[#C97D1A]">
                                    <input type="radio" name="checklist_a[{{ $item['id'] }}]" value="rusak_ringan" class="accent-[#C97D1A] audit-radio">
                                    <span>Rusak / Bocor</span>
                                </label>
                                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                                    <input type="radio" name="checklist_a[{{ $item['id'] }}]" value="rusak_berat" class="accent-error audit-radio">
                                    <span>Rusak Berat</span>
                                </label>
                            </div>
                        </div>

                        {{-- Slot Unggah Foto Bukti & Tombol Ajukan Laporan Kerusakan --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm ml-7 pt-1">
                            <div class="flex items-center gap-space-sm">
                                <label class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 text-[12px] font-semibold text-primary cursor-pointer transition-colors">
                                    <span class="material-symbols-outlined text-[16px] text-secondary">add_photo_alternate</span>
                                    <span>+ Unggah Foto Bukti</span>
                                    <input type="file" name="foto_item[{{ $item['id'] }}]" accept="image/*" class="hidden file-input" onchange="previewThumb(this)">
                                </label>
                                <div class="thumb-container hidden items-center gap-1.5 px-2 py-0.5 rounded bg-success/10 text-success text-[11px] font-medium">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    <span class="file-name">Foto terlampir</span>
                                </div>
                            </div>

                            {{-- Tombol Ajukan Laporan Kerusakan (Aktif jika rusak) --}}
                            <div class="damage-action hidden items-center gap-2">
                                <a href="{{ route('go.laporan-kerusakan.create', ['pengajuan_id' => $checkout->id_pengajuan, 'komponen' => $item['id']]) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-error text-on-error hover:bg-error/90 text-[12px] font-bold shadow-xs transition-all">
                                    <span class="material-symbols-outlined text-[16px]">warning</span>
                                    <span>Ajukan Laporan Kerusakan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Bagian B: Inventaris Perabot & Mebel Hunian --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <div class="flex items-center gap-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[20px]">chair</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">Bagian B: Inventaris Perabot & Mebel Hunian</h2>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Audit tempat tidur & kasur spring bed, lemari pakaian 2 pintu & kunci loker, serta meja belajar & kursi.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant font-caption-sm-medium text-caption-sm-medium">3 Komponen</span>
            </div>

            <div class="flex flex-col divide-y divide-surface-container">
                @php
                    $itemsB = [
                        ['id' => 'tempat_tidur', 'num' => 4, 'nama' => 'Tempat Tidur & Kasur Spring Bed', 'desc' => 'Rangka besi bed kokoh, pegas kasur spring bed normal, sprei/matras bersih tanpa sobek', 'icon' => 'bed', 'default' => 'baik'],
                        ['id' => 'lemari_pakaian', 'num' => 5, 'nama' => 'Lemari Pakaian 2 Pintu & Kunci Loker', 'desc' => 'Engsel pintu lemari, rel laci bawah, gantungan baju, dan kunci loker berfungsi', 'icon' => 'dresser', 'default' => 'baik'],
                        ['id' => 'meja_kursi', 'num' => 6, 'nama' => 'Meja Belajar & Kursi Mahasiswa', 'desc' => 'Permukaan meja belajar bersih dari goresan dalam, struktur kursi kokoh & stabil', 'icon' => 'table_restaurant', 'default' => 'baik'],
                    ];
                @endphp

                @foreach ($itemsB as $item)
                    <div class="py-space-md flex flex-col gap-space-sm checklist-row" data-id="{{ $item['id'] }}">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                            <div class="flex items-start gap-space-sm max-w-xl">
                                <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">{{ $item['icon'] }}</span>
                                <div>
                                    <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ $item['num'] }}. {{ $item['nama'] }}</span>
                                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">{{ $item['desc'] }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-space-md self-start md:self-center shrink-0">
                                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-on-surface">
                                    <input type="radio" name="checklist_b[{{ $item['id'] }}]" value="baik" class="accent-secondary audit-radio" {{ $item['default'] === 'baik' ? 'checked' : '' }} required>
                                    <span>Baik</span>
                                </label>
                                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-[#C97D1A]">
                                    <input type="radio" name="checklist_b[{{ $item['id'] }}]" value="rusak_ringan" class="accent-[#C97D1A] audit-radio">
                                    <span>Rusak / Bocor</span>
                                </label>
                                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                                    <input type="radio" name="checklist_b[{{ $item['id'] }}]" value="rusak_berat" class="accent-error audit-radio">
                                    <span>Rusak Berat</span>
                                </label>
                            </div>
                        </div>

                        {{-- Slot Unggah Foto Bukti & Tombol Ajukan Laporan Kerusakan --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm ml-7 pt-1">
                            <div class="flex items-center gap-space-sm">
                                <label class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 text-[12px] font-semibold text-primary cursor-pointer transition-colors">
                                    <span class="material-symbols-outlined text-[16px] text-secondary">add_photo_alternate</span>
                                    <span>+ Unggah Foto Bukti</span>
                                    <input type="file" name="foto_item[{{ $item['id'] }}]" accept="image/*" class="hidden file-input" onchange="previewThumb(this)">
                                </label>
                                <div class="thumb-container hidden items-center gap-1.5 px-2 py-0.5 rounded bg-success/10 text-success text-[11px] font-medium">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    <span class="file-name">Foto terlampir</span>
                                </div>
                            </div>

                            <div class="damage-action hidden items-center gap-2">
                                <a href="{{ route('go.laporan-kerusakan.create', ['pengajuan_id' => $checkout->id_pengajuan, 'komponen' => $item['id']]) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-error text-on-error hover:bg-error/90 text-[12px] font-bold shadow-xs transition-all">
                                    <span class="material-symbols-outlined text-[16px]">warning</span>
                                    <span>Ajukan Laporan Kerusakan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Bagian C: Kelistrikan, Pendingin Ruangan & Sanitasi (MEP) --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <div class="flex items-center gap-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[20px]">electrical_services</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">Bagian C: MEP Sanitasi & Pendingin Ruangan</h2>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Pengecekan unit AC split & remote, sakelar/stop kontak/MCB, dan instalasi kamar mandi.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant font-caption-sm-medium text-caption-sm-medium">3 Komponen</span>
            </div>

            <div class="flex flex-col divide-y divide-surface-container">
                {{-- 7. AC Unit dengan Status Rusak/Bocor & Tombol Mencolok Ajukan Laporan Kerusakan --}}
                <div class="py-space-md flex flex-col gap-space-sm checklist-row" data-id="ac_split">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                        <div class="flex items-start gap-space-sm max-w-xl">
                            <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">ac_unit</span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-label-md text-label-md text-on-surface font-semibold">7. Unit AC Split & Remote AC</span>
                                    <span class="px-2 py-0.5 rounded-full bg-error/10 text-error text-[10px] font-semibold uppercase">Temuan Kendala</span>
                                </div>
                                <span class="font-caption-sm text-caption-sm text-on-surface-variant block mt-0.5">Daya hembus dingin, swing louvre, indikator indoor, serta respon sensor remote AC.</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-space-md self-start md:self-center shrink-0">
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-on-surface">
                                <input type="radio" name="checklist_c[ac_split]" value="baik" class="accent-secondary audit-radio">
                                <span>Baik</span>
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 bg-[#C97D1A]/10 cursor-pointer transition-colors text-label-md text-[#C97D1A] font-semibold">
                                <input type="radio" name="checklist_c[ac_split]" value="rusak_ringan" class="accent-[#C97D1A] audit-radio" checked required>
                                <span>Rusak / Bocor</span>
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                                <input type="radio" name="checklist_c[ac_split]" value="rusak_berat" class="accent-error audit-radio">
                                <span>Rusak Berat</span>
                            </label>
                        </div>
                    </div>

                    {{-- Auto-Ticket Info Box & Tombol Interaktif Ajukan Laporan Kerusakan --}}
                    <div class="rounded-xl bg-[#fff8ed] border border-[#C97D1A]/40 p-space-md flex flex-col sm:flex-row sm:items-center justify-between gap-space-md ml-7">
                        <div class="flex items-start gap-space-sm">
                            <span class="material-symbols-outlined text-[#C97D1A] text-[24px] mt-0.5">report_problem</span>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-label-md text-label-md text-[#8c5208] font-bold">Terhubung ke Tiket: #TKT-0142</span>
                                    <span class="px-2 py-0.5 rounded bg-error/20 text-error text-[10px] font-bold">Rusak / Bocor Air Indoor</span>
                                </div>
                                <p class="font-caption-sm text-caption-sm text-[#8c5208]/90 mt-0.5">
                                    Ditemukan rembesan tetesan air kondensasi pada casing indoor AC split Kamar 202. Diperlukan tindakan perbaikan oleh teknisi pendingin.
                                </p>
                            </div>
                        </div>

                        {{-- Tombol Interaktif Ajukan Laporan Kerusakan --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('go.laporan-kerusakan.create', ['pengajuan_id' => $checkout->id_pengajuan, 'komponen' => 'ac_split']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-error hover:bg-error/90 text-on-error font-label-md text-label-md font-bold shadow-md transition-all whitespace-nowrap">
                                <span class="material-symbols-outlined text-[18px]">warning</span>
                                <span>Ajukan Laporan Kerusakan</span>
                                <span class="px-1.5 py-0.5 rounded bg-white/20 text-[11px] font-mono">#TKT-0142</span>
                            </a>
                        </div>
                    </div>

                    {{-- Slot Unggah Foto Bukti AC --}}
                    <div class="flex items-center gap-space-sm ml-7 pt-1">
                        <label class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 text-[12px] font-semibold text-primary cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-[16px] text-secondary">add_photo_alternate</span>
                            <span>+ Unggah Foto Bukti AC</span>
                            <input type="file" name="foto_item[ac_split]" accept="image/*" class="hidden file-input" onchange="previewThumb(this)">
                        </label>
                        <div class="thumb-container flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-[#C97D1A]/15 text-[#8c5208] text-[11px] font-semibold">
                            <span class="material-symbols-outlined text-[14px] text-[#C97D1A]">photo</span>
                            <span class="file-name">tetesan_indoor_ac.jpg (Terlampir)</span>
                        </div>
                    </div>
                </div>

                {{-- 8. Sakelar Lampu & MCB --}}
                <div class="py-space-md flex flex-col gap-space-sm checklist-row" data-id="kelistrikan">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                        <div class="flex items-start gap-space-sm max-w-xl">
                            <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">electrical_services</span>
                            <div>
                                <span class="font-label-md text-label-md text-on-surface font-semibold block">8. Sakelar Lampu / Stop Kontak / MCB</span>
                                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Lampu utama plafon hidup normal, sakelar klik presisi, stop kontak meja bertegangan aman 220V.</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-space-md self-start md:self-center shrink-0">
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-on-surface">
                                <input type="radio" name="checklist_c[kelistrikan]" value="baik" class="accent-secondary audit-radio" checked required>
                                <span>Baik</span>
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-[#C97D1A]">
                                <input type="radio" name="checklist_c[kelistrikan]" value="rusak_ringan" class="accent-[#C97D1A] audit-radio">
                                <span>Rusak / Bocor</span>
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                                <input type="radio" name="checklist_c[kelistrikan]" value="rusak_berat" class="accent-error audit-radio">
                                <span>Rusak Berat</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm ml-7 pt-1">
                        <div class="flex items-center gap-space-sm">
                            <label class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 text-[12px] font-semibold text-primary cursor-pointer transition-colors">
                                <span class="material-symbols-outlined text-[16px] text-secondary">add_photo_alternate</span>
                                <span>+ Unggah Foto Bukti</span>
                                <input type="file" name="foto_item[kelistrikan]" accept="image/*" class="hidden file-input" onchange="previewThumb(this)">
                            </label>
                            <div class="thumb-container hidden items-center gap-1.5 px-2 py-0.5 rounded bg-success/10 text-success text-[11px] font-medium">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                <span class="file-name">Foto terlampir</span>
                            </div>
                        </div>

                        <div class="damage-action hidden items-center gap-2">
                            <a href="{{ route('go.laporan-kerusakan.create', ['pengajuan_id' => $checkout->id_pengajuan, 'komponen' => 'kelistrikan']) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-error text-on-error hover:bg-error/90 text-[12px] font-bold shadow-xs transition-all">
                                <span class="material-symbols-outlined text-[16px]">warning</span>
                                <span>Ajukan Laporan Kerusakan</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 9. Instalasi Kamar Mandi & Sanitasi --}}
                <div class="py-space-md flex flex-col gap-space-sm checklist-row" data-id="sanitasi">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                        <div class="flex items-start gap-space-sm max-w-xl">
                            <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">shower</span>
                            <div>
                                <span class="font-label-md text-label-md text-on-surface font-semibold block">9. Instalasi Kamar Mandi & Sanitasi</span>
                                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Kran shower mengalir deras, flush kloset duduk normal tanpa rembes, floor drain lancar tanpa genangan.</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-space-md self-start md:self-center shrink-0">
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-on-surface">
                                <input type="radio" name="checklist_c[sanitasi]" value="baik" class="accent-secondary audit-radio" checked required>
                                <span>Baik</span>
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-[#C97D1A]">
                                <input type="radio" name="checklist_c[sanitasi]" value="rusak_ringan" class="accent-[#C97D1A] audit-radio">
                                <span>Rusak / Bocor</span>
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                                <input type="radio" name="checklist_c[sanitasi]" value="rusak_berat" class="accent-error audit-radio">
                                <span>Rusak Berat</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm ml-7 pt-1">
                        <div class="flex items-center gap-space-sm">
                            <label class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 text-[12px] font-semibold text-primary cursor-pointer transition-colors">
                                <span class="material-symbols-outlined text-[16px] text-secondary">add_photo_alternate</span>
                                <span>+ Unggah Foto Bukti</span>
                                <input type="file" name="foto_item[sanitasi]" accept="image/*" class="hidden file-input" onchange="previewThumb(this)">
                            </label>
                            <div class="thumb-container hidden items-center gap-1.5 px-2 py-0.5 rounded bg-success/10 text-success text-[11px] font-medium">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                <span class="file-name">Foto terlampir</span>
                            </div>
                        </div>

                        <div class="damage-action hidden items-center gap-2">
                            <a href="{{ route('go.laporan-kerusakan.create', ['pengajuan_id' => $checkout->id_pengajuan, 'komponen' => 'sanitasi']) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-error text-on-error hover:bg-error/90 text-[12px] font-bold shadow-xs transition-all">
                                <span class="material-symbols-outlined text-[16px]">warning</span>
                                <span>Ajukan Laporan Kerusakan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan Hasil Audit Petugas Lapangan --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <h2 class="font-headline-sm text-headline-sm text-primary mb-1">Catatan Hasil Audit Petugas Lapangan</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant mb-space-md">Tuliskan keterangan detail mengenai temuan kondisi fisik kamar atau catatan bagi Fasilitator.</p>
            <textarea name="catatan" rows="3" placeholder="Tuliskan catatan kondisi fisik kamar di sini..." class="w-full rounded-xl bg-surface-container-low p-space-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-secondary resize-y">Kamar bersih dan terawat. Ditemukan rembesan tetesan air pada unit AC split indoor (terhubung ke tiket #TKT-0142). Telah diajukan pelaporan kerusakan fasilitas terkait.</textarea>
            <input type="hidden" name="kondisi_kamar" id="input-kondisi-kamar" value="rusak_ringan">
        </div>

        {{-- Bilah Tindakan Final di Bagian Bawah --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-space-md pt-space-xs border-t border-surface-container">
            <button type="button" onclick="document.getElementById('form-audit').action='{{ route('go.pengecekan-kamar.draft', $checkout->id_pengajuan) }}'; document.getElementById('form-audit').submit();" class="inline-flex items-center gap-2 px-space-lg py-3 rounded-xl border border-outline-variant/40 bg-surface-container-lowest hover:bg-surface-container-low text-on-surface-variant font-label-md text-label-md transition-colors cursor-pointer w-full sm:w-auto justify-center">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Batal & Simpan Draf</span>
            </button>

            <div class="flex flex-col sm:flex-row items-center gap-space-sm w-full sm:w-auto">
                <button type="submit" class="inline-flex items-center gap-2 px-space-xl py-3 rounded-xl bg-secondary hover:bg-secondary/90 text-on-secondary font-label-md text-label-md font-semibold shadow-brand-action transition-all cursor-pointer w-full sm:w-auto justify-center">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>Selesaikan Pengecekan Kamar</span>
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Script Interactive Actions & Timer --}}
@push('scripts')
<script>
    function previewThumb(input) {
        if (input.files && input.files[0]) {
            const row = input.closest('.checklist-row');
            const thumbContainer = row ? row.querySelector('.thumb-container') : null;
            if (thumbContainer) {
                thumbContainer.classList.remove('hidden');
                thumbContainer.classList.add('flex');
                const nameEl = thumbContainer.querySelector('.file-name');
                if (nameEl) nameEl.textContent = input.files[0].name;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('.checklist-row');

        function updateRowStatus(row) {
            const checkedRadio = row.querySelector('.audit-radio:checked');
            const damageAction = row.querySelector('.damage-action');

            if (checkedRadio && (checkedRadio.value === 'rusak_ringan' || checkedRadio.value === 'rusak_berat')) {
                if (damageAction) {
                    damageAction.classList.remove('hidden');
                    damageAction.classList.add('flex');
                }
            } else {
                if (damageAction) {
                    damageAction.classList.remove('flex');
                    damageAction.classList.add('hidden');
                }
            }
        }

        rows.forEach(row => {
            const radios = row.querySelectorAll('.audit-radio');
            radios.forEach(r => r.addEventListener('change', () => updateRowStatus(row)));
            updateRowStatus(row);
        });

        // Running Timer
        let seconds = 0;
        const timerEl = document.getElementById('timer');
        setInterval(() => {
            seconds++;
            const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
            const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
            const s = String(seconds % 60).padStart(2, '0');
            if (timerEl) timerEl.textContent = `${h}:${m}:${s}`;
        }, 1000);
    });
</script>
@endpush
@endsection