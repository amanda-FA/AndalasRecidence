@extends('layouts.app')

@section('title', 'Form Pengecekan Kamar - Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary text-[22px]">checklist</span>
        <span class="font-label-md text-label-md">Audit Fisik & Kunci</span>
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
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Audit fisik inventaris kamar, serah terima kunci RFID, dan identifikasi kerusakan sebelum checkout.</p>
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
                <div class="w-11 h-11 rounded-xl bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed shrink-0">
                    <span class="material-symbols-outlined text-[24px]">badge</span>
                </div>
                <div>
                    <span class="text-[11px] uppercase tracking-wider font-medium text-on-surface-variant block">Petugas Pemeriksa (GO)</span>
                    <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ $petugas->nama_lengkap ?? 'Rahardian Pratama, A.Md.' }}</span>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">NIP: 199408152022031002</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Inspection Form --}}
    <form id="form-audit" action="{{ route('go.pengecekan-kamar.selesai', $checkout->id_pengajuan) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-space-xl">
        @csrf

        {{-- Bagian A: Kondisi Fisik Ruangan & Bangunan --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <div class="flex items-center gap-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[20px]">home_repair_service</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">Bagian A: Kondisi Fisik Ruangan & Bangunan</h2>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Audit dinding, cat, plafon, pintu utama, handel, jendela kaca, dan ubin keramik.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant font-caption-sm-medium text-caption-sm-medium">5 Komponen</span>
            </div>

            <div class="flex flex-col divide-y divide-surface-container">
                @php
                    $itemsA = [
                        ['id' => 'dinding_cat', 'nama' => 'Dinding & Pengecatan', 'desc' => 'Kebersihan permukaan dinding, bekas coretan, paku, dan kondisi cat', 'icon' => 'format_paint', 'default' => 'baik'],
                        ['id' => 'plafon', 'nama' => 'Plafon & Langit-langit', 'desc' => 'Keutuhan plafon gypsum/triplek, tidak ada rembesan air atau jebol', 'icon' => 'roofing', 'default' => 'baik'],
                        ['id' => 'pintu_utama', 'nama' => 'Pintu Utama & Handel Kunci', 'desc' => 'Engsel kuat, daun pintu rapat, silinder kunci mekanis berfungsi normal', 'icon' => 'door_front', 'default' => 'baik'],
                        ['id' => 'jendela_kaca', 'nama' => 'Jendela Kaca & Slot Pengunci', 'desc' => 'Kaca utuh tanpa retak, engsel swing lancar, slot grendel terkunci presisi', 'icon' => 'window', 'default' => 'baik'],
                        ['id' => 'ubin_keramik', 'nama' => 'Ubin & Keramik Lantai', 'desc' => 'Keramik lantai kamar tidur utuh, tidak gompal/pecah, plint dinding rapi', 'icon' => 'grid_on', 'default' => 'baik'],
                    ];
                @endphp

                @foreach ($itemsA as $index => $item)
                    <div class="py-space-md flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                        <div class="flex items-start gap-space-sm max-w-xl">
                            <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">{{ $item['icon'] }}</span>
                            <div>
                                <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ $item['nama'] }}</span>
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
                                <span>Rusak Ringan</span>
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                                <input type="radio" name="checklist_a[{{ $item['id'] }}]" value="rusak_berat" class="accent-error audit-radio">
                                <span>Rusak Berat</span>
                            </label>
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
                        ['id' => 'tempat_tidur', 'nama' => 'Tempat Tidur & Kasur Spring Bed', 'desc' => 'Rangka besi bed kokoh, pegas kasur spring bed normal, sprei/matras bersih tanpa sobek', 'icon' => 'bed', 'default' => 'baik'],
                        ['id' => 'lemari_pakaian', 'nama' => 'Lemari Pakaian 2 Pintu & Kunci Loker', 'desc' => 'Engsel pintu lemari, rel laci bawah, gantungan baju, dan kunci loker berfungsi', 'icon' => 'dresser', 'default' => 'baik'],
                        ['id' => 'meja_kursi', 'nama' => 'Meja Belajar & Kursi Mahasiswa', 'desc' => 'Permukaan meja belajar bersih dari goresan dalam, struktur kursi kokoh & stabil', 'icon' => 'table_restaurant', 'default' => 'baik'],
                    ];
                @endphp

                @foreach ($itemsB as $index => $item)
                    <div class="py-space-md flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                        <div class="flex items-start gap-space-sm max-w-xl">
                            <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">{{ $item['icon'] }}</span>
                            <div>
                                <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ $item['nama'] }}</span>
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
                                <span>Rusak Ringan</span>
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                                <input type="radio" name="checklist_b[{{ $item['id'] }}]" value="rusak_berat" class="accent-error audit-radio">
                                <span>Rusak Berat</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Bagian C: Kelistrikan, Pendingin Ruangan & Sanitasi --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <div class="flex items-center gap-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[20px]">electrical_services</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">Bagian C: Kelistrikan, Pendingin Ruangan & Sanitasi</h2>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Pengecekan unit AC split & remote, sakelar/stop kontak/MCB, dan instalasi kamar mandi.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant font-caption-sm-medium text-caption-sm-medium">3 Komponen</span>
            </div>

            <div class="flex flex-col divide-y divide-surface-container">
                {{-- AC Unit dengan Auto-Ticket TKT-2025-0142 --}}
                <div class="py-space-md flex flex-col gap-space-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                        <div class="flex items-start gap-space-sm max-w-xl">
                            <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">ac_unit</span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-label-md text-label-md text-on-surface font-semibold">Unit AC Split & Remote AC</span>
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
                                <span>Rusak Ringan</span>
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                                <input type="radio" name="checklist_c[ac_split]" value="rusak_berat" class="accent-error audit-radio">
                                <span>Rusak Berat</span>
                            </label>
                        </div>
                    </div>

                    {{-- Auto-Ticket Info Box --}}
                    <div class="rounded-xl bg-[#fff8ed] border border-[#C97D1A]/30 p-space-md flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm ml-7">
                        <div class="flex items-start gap-space-sm">
                            <span class="material-symbols-outlined text-[#C97D1A] text-[20px] mt-0.5">report_problem</span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-label-md text-label-md text-[#8c5208] font-bold">Terhubung ke Auto-Ticket: TKT-2025-0142</span>
                                    <span class="px-2 py-0.2 rounded bg-[#C97D1A]/20 text-[#8c5208] text-[10px] font-semibold">Tetesan Indoor AC</span>
                                </div>
                                <p class="font-caption-sm text-caption-sm text-[#8c5208]/90 mt-0.5">
                                    Ditemukan rembesan tetesan air kondensasi pada casing indoor AC split Kamar 202. Tiket pemeliharaan otomatis diajukan ke teknisi pendingin ruangan.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('go.laporan-kerusakan.index') }}" class="inline-flex items-center gap-1 text-[12px] font-semibold text-secondary hover:underline whitespace-nowrap self-start sm:self-center">
                            Lihat Tiket
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                        </a>
                    </div>
                </div>

                {{-- Sakelar Lampu & MCB --}}
                <div class="py-space-md flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                    <div class="flex items-start gap-space-sm max-w-xl">
                        <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">electrical_services</span>
                        <div>
                            <span class="font-label-md text-label-md text-on-surface font-semibold block">Sakelar Lampu / Stop Kontak / MCB</span>
                            <span class="font-caption-sm text-caption-sm text-on-surface-variant">Lampu utama plafon hidup normal, sakelar klik presisi, stop kontak meja bertegangan aman 220V.</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-space-md self-start md:self-center shrink-0">
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-on-surface">
                            <input type="radio" name="checklist_c[sakelar_mcb]" value="baik" class="accent-secondary audit-radio" checked required>
                            <span>Baik</span>
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-[#C97D1A]">
                            <input type="radio" name="checklist_c[sakelar_mcb]" value="rusak_ringan" class="accent-[#C97D1A] audit-radio">
                            <span>Rusak Ringan</span>
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                            <input type="radio" name="checklist_c[sakelar_mcb]" value="rusak_berat" class="accent-error audit-radio">
                            <span>Rusak Berat</span>
                        </label>
                    </div>
                </div>

                {{-- Instalasi Kamar Mandi --}}
                <div class="py-space-md flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                    <div class="flex items-start gap-space-sm max-w-xl">
                        <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5 shrink-0">bathroom</span>
                        <div>
                            <span class="font-label-md text-label-md text-on-surface font-semibold block">Instalasi Kamar Mandi Dalam</span>
                            <span class="font-caption-sm text-caption-sm text-on-surface-variant">Kran air mengalir lancar, kloset flush berfungsi baik, shower & floor drain bersih tanpa sumbatan.</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-space-md self-start md:self-center shrink-0">
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-on-surface">
                            <input type="radio" name="checklist_c[sanitasi]" value="baik" class="accent-secondary audit-radio" checked required>
                            <span>Baik</span>
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-[#C97D1A]">
                            <input type="radio" name="checklist_c[sanitasi]" value="rusak_ringan" class="accent-[#C97D1A] audit-radio">
                            <span>Rusak Ringan</span>
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors text-label-md text-error">
                            <input type="radio" name="checklist_c[sanitasi]" value="rusak_berat" class="accent-error audit-radio">
                            <span>Rusak Berat</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Verifikasi Serah Terima Akses & Kunci RFID --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <div class="flex items-center gap-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[20px]">contactless</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">Verifikasi Serah Terima Akses & Kunci RFID</h2>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Validasi fisik & frekuensi sensor kartu akses RFID serta serah terima kunci master fisik asrama.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-success/10 text-success font-caption-sm-medium text-caption-sm-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">sensors</span>
                    Reader Terhubung
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg">
                {{-- Kartu RFID --}}
                <div class="rounded-xl border border-outline-variant/30 p-space-md flex flex-col gap-space-sm bg-surface-container-low/40">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-on-surface font-semibold flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary text-[20px]">credit_card</span>
                            Kartu Akses RFID
                        </span>
                        <span class="px-2 py-0.5 rounded bg-secondary/10 text-secondary font-mono text-[11px] font-semibold">RFID-MNP-0202A</span>
                    </div>
                    <p class="font-caption-sm text-caption-sm text-on-surface-variant">
                        Uji tap reader petugas: <strong class="text-success">Terbaca Valid di Reader Petugas (13.56 MHz OK)</strong>. Tidak patah dan barcode identitas jelas.
                    </p>
                    <div class="flex items-center gap-space-lg mt-2 pt-2 border-t border-surface-container">
                        <label class="flex items-center gap-2 text-label-md font-medium text-primary cursor-pointer">
                            <input type="radio" name="rfid_status" value="valid" class="accent-secondary" checked required>
                            <span>Terbaca Valid</span>
                        </label>
                        <label class="flex items-center gap-2 text-label-md font-medium text-error cursor-pointer">
                            <input type="radio" name="rfid_status" value="tidak_valid" class="accent-error">
                            <span>Tidak Valid / Hilang</span>
                        </label>
                    </div>
                </div>

                {{-- Kunci Master Fisik --}}
                <div class="rounded-xl border border-outline-variant/30 p-space-md flex flex-col gap-space-sm bg-surface-container-low/40">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-on-surface font-semibold flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary text-[20px]">key</span>
                            Kunci Fisik Master Kamar
                        </span>
                        <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant text-[11px] font-mono">KEY-MNP-202</span>
                    </div>
                    <p class="font-caption-sm text-caption-sm text-on-surface-variant">
                        Pemeriksaan 1 anak kunci asli kamar & 1 anak kunci loker lemari diserahkan mahasiswa dalam kondisi utuh.
                    </p>
                    <div class="flex items-center gap-space-lg mt-2 pt-2 border-t border-surface-container">
                        <label class="flex items-center gap-2 text-label-md font-medium text-primary cursor-pointer">
                            <input type="radio" name="kunci_fisik" value="dikembalikan" class="accent-secondary" checked required>
                            <span>Diserahkan Lengkap</span>
                        </label>
                        <label class="flex items-center gap-2 text-label-md font-medium text-error cursor-pointer">
                            <input type="radio" name="kunci_fisik" value="belum" class="accent-error">
                            <span>Belum Diserahkan</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dokumentasi Foto Bukti Lapangan --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <div class="flex items-center gap-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">Dokumentasi Foto Bukti Lapangan</h2>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Grid 4 slot dokumentasi kondisi riil kamar untuk arsip berita acara pemeriksaan.</p>
                    </div>
                </div>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Maks 2MB per foto (JPG/PNG)</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
                {{-- Slot 1: Tampak Umum --}}
                <div class="rounded-xl border border-outline-variant/30 p-space-md flex flex-col justify-between h-48 bg-surface-container-low/30 hover:border-secondary transition-all relative overflow-hidden group">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-label-md text-label-md text-primary font-semibold">1. Tampak Umum Kamar</span>
                            <span class="material-symbols-outlined text-secondary text-[20px]">check_circle</span>
                        </div>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Sudut pandang keseluruhan pintu masuk hingga jendela.</p>
                    </div>
                    <div class="flex items-center justify-center py-2">
                        <span class="material-symbols-outlined text-[36px] text-secondary">image</span>
                    </div>
                    <label class="w-full py-1.5 rounded-lg bg-surface-container-lowest border border-outline-variant/40 hover:bg-surface-container text-center font-caption-sm text-caption-sm font-semibold text-primary cursor-pointer">
                        <span>Ganti / Upload Foto</span>
                        <input type="file" name="foto_1" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Slot 2: Detail Unit AC --}}
                <div class="rounded-xl border-2 border-[#C97D1A]/50 p-space-md flex flex-col justify-between h-48 bg-[#fffcf5] relative overflow-hidden group">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-label-md text-label-md text-[#8c5208] font-bold">2. Detail Kebocoran AC</span>
                            <span class="material-symbols-outlined text-[#C97D1A] text-[20px]">report_problem</span>
                        </div>
                        <p class="font-caption-sm text-caption-sm text-[#8c5208]/90">Bukti tetesan air kondensasi pada casing unit AC indoor.</p>
                    </div>
                    <div class="flex items-center justify-center py-2">
                        <span class="material-symbols-outlined text-[36px] text-[#C97D1A]">water_drop</span>
                    </div>
                    <label class="w-full py-1.5 rounded-lg bg-[#fff0d5] border border-[#C97D1A]/40 hover:bg-[#ffe6bc] text-center font-caption-sm text-caption-sm font-semibold text-[#8c5208] cursor-pointer">
                        <span>Upload Bukti Rusak</span>
                        <input type="file" name="foto_2" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Slot 3: Meja & Lemari --}}
                <div class="rounded-xl border border-outline-variant/30 p-space-md flex flex-col justify-between h-48 bg-surface-container-low/30 hover:border-secondary transition-all relative overflow-hidden group">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-label-md text-label-md text-primary font-semibold">3. Meja & Lemari Baju</span>
                            <span class="material-symbols-outlined text-secondary text-[20px]">check_circle</span>
                        </div>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Kebersihan meja belajar & kondisi fisik daun lemari.</p>
                    </div>
                    <div class="flex items-center justify-center py-2">
                        <span class="material-symbols-outlined text-[36px] text-secondary">dresser</span>
                    </div>
                    <label class="w-full py-1.5 rounded-lg bg-surface-container-lowest border border-outline-variant/40 hover:bg-surface-container text-center font-caption-sm text-caption-sm font-semibold text-primary cursor-pointer">
                        <span>Ganti / Upload Foto</span>
                        <input type="file" name="foto_3" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Slot 4: Foto Tambahan --}}
                <div class="rounded-xl border border-dashed border-outline-variant p-space-md flex flex-col justify-between h-48 bg-surface-container-low/20 hover:border-secondary transition-all relative overflow-hidden group text-center">
                    <div>
                        <span class="font-label-md text-label-md text-on-surface-variant font-semibold block">4. Dokumentasi Ekstra</span>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">Kamar mandi, plafon, atau sudut lainnya.</p>
                    </div>
                    <div class="flex items-center justify-center py-2">
                        <span class="material-symbols-outlined text-[36px] text-on-surface-variant/40">add_photo_alternate</span>
                    </div>
                    <label class="w-full py-1.5 rounded-lg bg-surface-container-lowest border border-outline-variant/40 hover:bg-surface-container text-center font-caption-sm text-caption-sm font-semibold text-primary cursor-pointer">
                        <span>+ Tambah Foto</span>
                        <input type="file" name="foto_4" accept="image/*" class="hidden">
                    </label>
                </div>
            </div>
        </div>

        {{-- Catatan Tambahan Petugas --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <h2 class="font-headline-sm text-headline-sm text-primary mb-1">Catatan Hasil Audit Petugas Lapangan</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant mb-space-md">Tuliskan keterangan detail mengenai temuan kamar atau instruksi bagi Fasilitator.</p>
            <textarea name="catatan" rows="3" placeholder="Contoh: Kamar secara umum dalam keadaan bersih dan rapi. Seluruh kunci dan kartu RFID telah diserahkan lengkap. Ditemukan kebocoran tetesan air ringan pada indoor AC yang memerlukan pembersihan pipa drainase oleh teknisi..." class="w-full rounded-xl bg-surface-container-low p-space-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-secondary resize-y">Kamar bersih dan terawat. Ditemukan rembesan tetesan air pada unit AC split indoor (terhubung ke tiket TKT-2025-0142). Kunci fisik 2 unit dan kartu RFID RFID-MNP-0202A telah diterima lengkap oleh petugas GO.</textarea>
            <input type="hidden" name="kondisi_kamar" id="input-kondisi-kamar" value="rusak_ringan">
        </div>

        {{-- Ringkasan Hasil Inspeksi & Alur Kewenangan --}}
        <div class="rounded-2xl bg-primary-container text-surface-container-lowest p-space-lg sm:p-space-xl shadow-md">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-lg">
                <div class="flex items-start gap-space-md">
                    <div class="w-12 h-12 rounded-xl bg-secondary flex items-center justify-center text-on-secondary shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-[28px]">verified</span>
                    </div>
                    <div>
                        <span class="font-headline-sm text-headline-sm text-surface-container-lowest block">Kompilasi Hasil Inspeksi Lapangan</span>
                        <p class="font-body-md text-body-md text-secondary-fixed-dim mt-1 max-w-xl">
                            <strong>Penegasan Batas Kewenangan:</strong> Petugas GO bertugas melakukan audit fisik & verifikasi serah terima kunci. Hasil audit ini akan <strong>otomatis diteruskan ke Fasilitator Asrama</strong> untuk validasi final persetujuan check-out serta penerbitan Surat Keterangan Bebas Asrama (SKBA).
                        </p>
                    </div>
                </div>

                {{-- Metric Counters --}}
                <div class="grid grid-cols-4 gap-space-sm bg-primary/40 backdrop-blur-xs p-space-md rounded-xl text-center shrink-0">
                    <div>
                        <span class="font-headline-md text-headline-md text-surface-container-lowest block" id="count-total">11</span>
                        <span class="text-[11px] uppercase tracking-wider text-secondary-fixed-dim">Diuji</span>
                    </div>
                    <div>
                        <span class="font-headline-md text-headline-md text-[#2E9E5B] block" id="count-baik">10</span>
                        <span class="text-[11px] uppercase tracking-wider text-secondary-fixed-dim">Baik</span>
                    </div>
                    <div>
                        <span class="font-headline-md text-headline-md text-[#C97D1A] block" id="count-rusak">1</span>
                        <span class="text-[11px] uppercase tracking-wider text-secondary-fixed-dim">Temuan</span>
                    </div>
                    <div>
                        <span class="material-symbols-outlined text-[#2E9E5B] text-[28px] block mx-auto mt-0.5">key</span>
                        <span class="text-[11px] uppercase tracking-wider text-secondary-fixed-dim">Lengkap</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-space-md pt-space-xs">
            <button type="button" onclick="document.getElementById('form-audit').action='{{ route('go.pengecekan-kamar.draft', $checkout->id_pengajuan) }}'; document.getElementById('form-audit').submit();" class="inline-flex items-center gap-2 px-space-lg py-3 rounded-xl border border-outline-variant/40 bg-surface-container-lowest hover:bg-surface-container-low text-on-surface-variant font-label-md text-label-md transition-colors cursor-pointer w-full sm:w-auto justify-center">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Batal & Simpan Draf</span>
            </button>

            <div class="flex flex-col sm:flex-row items-center gap-space-sm w-full sm:w-auto">
                <a href="{{ route('go.laporan-kerusakan.index') }}" class="inline-flex items-center gap-2 px-space-lg py-3 rounded-xl border border-secondary text-secondary hover:bg-secondary/10 font-label-md text-label-md transition-colors cursor-pointer w-full sm:w-auto justify-center">
                    <span class="material-symbols-outlined text-[18px]">report</span>
                    <span>Catat Kerusakan Terpisah</span>
                </a>

                <button type="submit" class="inline-flex items-center gap-2 px-space-xl py-3 rounded-xl bg-secondary hover:bg-secondary/90 text-on-secondary font-label-md text-label-md font-semibold shadow-brand-action transition-all cursor-pointer w-full sm:w-auto justify-center">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Selesaikan Pengecekan & Kirim ke Fasilitator</span>
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Script Counter & Timer --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.audit-radio');
        const countBaikEl = document.getElementById('count-baik');
        const countRusakEl = document.getElementById('count-rusak');
        const countTotalEl = document.getElementById('count-total');
        const inputKondisiKamar = document.getElementById('input-kondisi-kamar');

        function recalculate() {
            let baik = 0;
            let rusakRingan = 0;
            let rusakBerat = 0;

            radios.forEach(r => {
                if (r.checked) {
                    if (r.value === 'baik') baik++;
                    else if (r.value === 'rusak_ringan') rusakRingan++;
                    else if (r.value === 'rusak_berat') rusakBerat++;
                }
            });

            const total = baik + rusakRingan + rusakBerat;
            const totalRusak = rusakRingan + rusakBerat;

            if (countBaikEl) countBaikEl.textContent = baik;
            if (countRusakEl) countRusakEl.textContent = totalRusak;
            if (countTotalEl) countTotalEl.textContent = total;

            if (inputKondisiKamar) {
                if (rusakBerat > 0) inputKondisiKamar.value = 'rusak_berat';
                else if (rusakRingan > 0) inputKondisiKamar.value = 'rusak_ringan';
                else inputKondisiKamar.value = 'baik';
            }
        }

        radios.forEach(r => r.addEventListener('change', recalculate));
        recalculate();

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
