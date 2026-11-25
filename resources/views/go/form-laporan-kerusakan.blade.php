@extends('layouts.app')

@section('title', 'Pengajuan Laporan Kerusakan Fasilitas - Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary text-[22px]">build</span>
        <span class="font-label-md text-label-md">Pelaporan Kerusakan Lapangan</span>
    </div>
@endsection

@section('content')
<div class="flex flex-col w-full max-w-[1100px] mx-auto gap-space-xl pb-space-2xl">

    {{-- Breadcrumb & Top Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
        <div class="flex items-center gap-space-sm flex-wrap">
            <a href="{{ route('go.pengecekan-kamar.show', $checkout->id_pengajuan ?? 1) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-primary transition-colors text-label-md font-medium">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali ke Form Pengecekan</span>
            </a>
            <span class="text-on-surface-variant/40">/</span>
            <span class="font-caption-sm text-caption-sm text-on-surface-variant">Laporan Kerusakan</span>
            <span class="text-on-surface-variant/40">/</span>
            <span class="font-caption-sm-medium text-caption-sm-medium text-secondary">Formulir Tiket Baru</span>
        </div>

        <div class="flex items-center gap-space-sm">
            <span class="px-3 py-1 rounded-full bg-error/15 text-error font-caption-sm-medium text-caption-sm-medium flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                Status: Draf Pengajuan Tiket
            </span>
        </div>
    </div>

    {{-- Header Title --}}
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-space-xs border-b border-surface-container pb-space-md">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Form Pengajuan Laporan Kerusakan Fasilitas</h1>
                <span class="px-2.5 py-0.5 rounded-lg bg-secondary/10 text-secondary font-mono text-[13px] font-semibold">TKT-NEW</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Pencatatan rincian teknis temuan kerusakan fisik atau mekanikal kamar saat inspeksi check-out untuk disposisi tim teknisi MEP.
            </p>
        </div>
    </div>

    <form action="{{ route('go.laporan-kerusakan.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-space-xl">
        @csrf
        <input type="hidden" name="id_pengajuan" value="{{ $checkout->id_pengajuan ?? 1 }}">

        {{-- 1. Asal Sumber Temuan & Lokasi --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <h2 class="font-title-md text-title-md text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[20px]">location_on</span>
                    1. Asal Sumber Temuan & Lokasi Kejadian
                </h2>
                <span class="px-2.5 py-0.5 rounded-full bg-secondary/10 text-secondary text-[11px] font-mono font-medium">
                    Tiket Inspeksi: {{ $nomorAudit ?? '#CHK-2026-0089' }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-lg">
                {{-- Gedung --}}
                <div class="flex items-start gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary shrink-0">
                        <span class="material-symbols-outlined text-[22px]">apartment</span>
                    </div>
                    <div>
                        <span class="text-[11px] uppercase tracking-wider font-medium text-on-surface-variant block">Gedung Asrama</span>
                        <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ $gedung->nama_gedung ?? 'Gedung Menpora Putra' }}</span>
                        <span class="font-caption-sm text-caption-sm text-on-surface-variant">Zona Sayap Barat</span>
                    </div>
                </div>

                {{-- Kamar & Bed --}}
                <div class="flex items-start gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary shrink-0">
                        <span class="material-symbols-outlined text-[22px]">meeting_room</span>
                    </div>
                    <div>
                        <span class="text-[11px] uppercase tracking-wider font-medium text-on-surface-variant block">Nomor Kamar</span>
                        <span class="font-label-md text-label-md text-on-surface font-semibold block">Kamar {{ $kamar->nomor_kamar ?? '202' }} &mdash; Bed A</span>
                        <span class="font-caption-sm text-caption-sm text-on-surface-variant">Lantai 2</span>
                    </div>
                </div>

                {{-- Mahasiswa Penghuni --}}
                <div class="flex items-start gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-on-primary font-bold text-sm shrink-0 shadow-sm">
                        {{ strtoupper(substr($pengguna->nama_lengkap ?? 'M', 0, 1)) }}
                    </div>
                    <div>
                        <span class="text-[11px] uppercase tracking-wider font-medium text-on-surface-variant block">Penghuni Terkait</span>
                        <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ $pengguna->nama_lengkap ?? 'Muhammad Fadhil Ar-Rayyan' }}</span>
                        <span class="font-caption-sm text-caption-sm text-secondary font-mono">NIM: 2211522004</span>
                    </div>
                </div>

                {{-- Petugas GO Pelapor --}}
                <div class="flex items-start gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary shrink-0">
                        <span class="material-symbols-outlined text-[22px]">badge</span>
                    </div>
                    <div>
                        <span class="text-[11px] uppercase tracking-wider font-medium text-on-surface-variant block">Petugas GO Pelapor</span>
                        <span class="font-label-md text-label-md text-on-surface font-semibold block">{{ auth()->user()->nama_lengkap ?? 'Rahardian Pratama, A.Md.' }}</span>
                        <span class="font-caption-sm text-caption-sm text-on-surface-variant">General Officer Lapangan</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Klasifikasi & Komponen Rusak --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <h2 class="font-title-md text-title-md text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[20px]">category</span>
                    2. Klasifikasi & Komponen Rusak
                </h2>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Pilih kategori dan kode aset inventaris</span>
            </div>

            <div class="flex flex-col gap-space-lg">
                {{-- Kategori Kerusakan Visual Selector --}}
                <div>
                    <label class="font-label-md text-label-md text-on-surface font-semibold block mb-2">
                        Kategori Kerusakan Fasilitas <span class="text-error">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-space-sm" id="category-selector">
                        <label class="flex flex-col items-center justify-center p-space-md rounded-xl border-2 border-secondary bg-secondary/10 cursor-pointer transition-all text-center category-card">
                            <input type="radio" name="kategori_kerusakan" value="ac" class="hidden" checked>
                            <span class="material-symbols-outlined text-secondary text-[28px] mb-1">ac_unit</span>
                            <span class="font-label-md text-label-md font-bold text-primary">AC & Pendingin</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-space-md rounded-xl border border-outline-variant/30 bg-surface-container-low hover:border-secondary cursor-pointer transition-all text-center category-card">
                            <input type="radio" name="kategori_kerusakan" value="listrik" class="hidden">
                            <span class="material-symbols-outlined text-on-surface-variant text-[28px] mb-1">electrical_services</span>
                            <span class="font-label-md text-label-md font-semibold text-on-surface">Kelistrikan</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-space-md rounded-xl border border-outline-variant/30 bg-surface-container-low hover:border-secondary cursor-pointer transition-all text-center category-card">
                            <input type="radio" name="kategori_kerusakan" value="plumbing" class="hidden">
                            <span class="material-symbols-outlined text-on-surface-variant text-[28px] mb-1">plumbing</span>
                            <span class="font-label-md text-label-md font-semibold text-on-surface">Plumbing & Air</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-space-md rounded-xl border border-outline-variant/30 bg-surface-container-low hover:border-secondary cursor-pointer transition-all text-center category-card">
                            <input type="radio" name="kategori_kerusakan" value="pintu_kunci" class="hidden">
                            <span class="material-symbols-outlined text-on-surface-variant text-[28px] mb-1">lock</span>
                            <span class="font-label-md text-label-md font-semibold text-on-surface">Pintu & Kunci</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-space-md rounded-xl border border-outline-variant/30 bg-surface-container-low hover:border-secondary cursor-pointer transition-all text-center category-card">
                            <input type="radio" name="kategori_kerusakan" value="mebel" class="hidden">
                            <span class="material-symbols-outlined text-on-surface-variant text-[28px] mb-1">chair</span>
                            <span class="font-label-md text-label-md font-semibold text-on-surface">Mebel / Kasur</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-space-md rounded-xl border border-outline-variant/30 bg-surface-container-low hover:border-secondary cursor-pointer transition-all text-center category-card">
                            <input type="radio" name="kategori_kerusakan" value="struktur" class="hidden">
                            <span class="material-symbols-outlined text-on-surface-variant text-[28px] mb-1">foundation</span>
                            <span class="font-label-md text-label-md font-semibold text-on-surface">Struktur Bangunan</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
                    {{-- Fasilitas Terkait (Database dropdown) --}}
                    <div>
                        <label class="font-label-md text-label-md text-on-surface font-semibold block mb-1">
                            Pilih Fasilitas / Inventaris Terdaftar <span class="text-error">*</span>
                        </label>
                        <select name="id_fasilitas" required class="w-full rounded-xl bg-surface-container-low border border-outline-variant/30 p-2.5 text-body-md text-on-surface outline-none focus:ring-2 focus:ring-secondary">
                            @foreach ($fasilitas as $f)
                                <option value="{{ $f->id_fasilitas }}" {{ (stripos($f->nama_fasilitas, 'AC') !== false) ? 'selected' : '' }}>
                                    {{ $f->nama_fasilitas }} ({{ $f->kamar ? 'Kamar ' . $f->kamar->nomor_kamar : ($f->gedung ? $f->gedung->nama_gedung : 'Umum') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kode Aset Inventaris --}}
                    <div>
                        <label class="font-label-md text-label-md text-on-surface font-semibold block mb-1">
                            Kode Aset Inventaris Kamar
                        </label>
                        <input type="text" name="kode_aset" value="AST-MNP-AC-0202" class="w-full rounded-xl bg-surface-container-low border border-outline-variant/30 p-2.5 font-mono text-body-md text-on-surface outline-none focus:ring-2 focus:ring-secondary">
                    </div>

                    {{-- Tingkat Urgensi SLA --}}
                    <div>
                        <label class="font-label-md text-label-md text-on-surface font-semibold block mb-1">
                            Tingkat Urgensi SLA <span class="text-error">*</span>
                        </label>
                        <select name="tingkat_urgensi" class="w-full rounded-xl bg-surface-container-low border border-outline-variant/30 p-2.5 text-body-md text-on-surface outline-none focus:ring-2 focus:ring-secondary font-medium">
                            <option value="reguler">Reguler (SLA 24 - 48 Jam)</option>
                            <option value="mendesak" selected class="text-[#C97D1A] font-semibold">Mendesak (&lt; 6 Jam)</option>
                            <option value="darurat" class="text-error font-bold">Darurat (&lt; 1 Jam)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Diagnosa & Gejala Lapangan --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <h2 class="font-title-md text-title-md text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[20px]">troubleshoot</span>
                    3. Diagnosa & Gejala Lapangan
                </h2>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Hasil pengujian fisik petugas GO</span>
            </div>

            <div class="flex flex-col gap-space-md">
                <div>
                    <label class="font-label-md text-label-md text-on-surface font-semibold block mb-1">
                        Judul / Ringkasan Kerusakan <span class="text-error">*</span>
                    </label>
                    <input type="text" name="judul_kerusakan" value="Kebocoran Air Kondensasi pada Casing Indoor AC Split Kamar 202" class="w-full rounded-xl bg-surface-container-low border border-outline-variant/30 p-3 text-body-md text-on-surface font-semibold outline-none focus:ring-2 focus:ring-secondary" required>
                </div>

                <div>
                    <label class="font-label-md text-label-md text-on-surface font-semibold block mb-1">
                        Deskripsi Lengkap Gejala & Hasil Pemeriksaan <span class="text-error">*</span>
                    </label>
                    <textarea name="deskripsi" rows="4" class="w-full rounded-xl bg-surface-container-low border border-outline-variant/30 p-space-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-secondary resize-y" required>Saat AC split dioperasikan pada suhu 20°C selama 15 menit, terjadi rembesan dan tetesan air terus menerus dari sudut kanan bawah unit indoor ke atas meja belajar mahasiswa. Saluran pipa drainase pembuangan air diduga tersumbat lendir/kotoran atau kemiringan pipa pembuangan terbalik. Kompresor outdoor dan hembusan kipas masih berfungsi dingin.</textarea>
                </div>
            </div>
        </div>

        {{-- 4. Dokumentasi Foto Bukti Kerusakan --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <h2 class="font-title-md text-title-md text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[20px]">add_a_photo</span>
                    4. Dokumentasi Foto Bukti Kerusakan
                </h2>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Resolusi tinggi (jarak dekat & sudut ruang)</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-space-md">
                {{-- Foto 1: Jarak Dekat --}}
                <div class="rounded-xl border-2 border-[#C97D1A]/50 p-space-md flex flex-col justify-between h-52 bg-[#fffcf5] relative">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-label-md text-label-md text-[#8c5208] font-bold">Foto Jarak Dekat (Macro)</span>
                            <span class="material-symbols-outlined text-[#C97D1A] text-[20px]">water_drop</span>
                        </div>
                        <p class="font-caption-sm text-caption-sm text-[#8c5208]/90">Detail titik kebocoran pada casing indoor AC.</p>
                    </div>
                    <div class="flex items-center justify-center py-2">
                        <span class="material-symbols-outlined text-[40px] text-[#C97D1A]">broken_image</span>
                    </div>
                    <label class="w-full py-2 rounded-lg bg-[#fff0d5] border border-[#C97D1A]/40 hover:bg-[#ffe6bc] text-center font-caption-sm text-caption-sm font-semibold text-[#8c5208] cursor-pointer">
                        <span>+ Unggah Foto Detail Kerusakan</span>
                        <input type="file" name="foto" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Foto 2: Orientasi Ruang --}}
                <div class="rounded-xl border border-outline-variant/30 p-space-md flex flex-col justify-between h-52 bg-surface-container-low/30 hover:border-secondary transition-all relative">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-label-md text-label-md text-primary font-semibold">Foto Sudut Ruang (Overview)</span>
                            <span class="material-symbols-outlined text-secondary text-[20px]">crop_original</span>
                        </div>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Posisi unit AC terhadap meja & perabot sekitar.</p>
                    </div>
                    <div class="flex items-center justify-center py-2">
                        <span class="material-symbols-outlined text-[40px] text-secondary">image</span>
                    </div>
                    <label class="w-full py-2 rounded-lg bg-surface-container-lowest border border-outline-variant/40 hover:bg-surface-container text-center font-caption-sm text-caption-sm font-semibold text-primary cursor-pointer">
                        <span>+ Unggah Foto Sudut Ruang</span>
                        <input type="file" name="foto_ruang" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Foto 3: Foto Opsional --}}
                <div class="rounded-xl border border-dashed border-outline-variant p-space-md flex flex-col justify-between h-52 bg-surface-container-low/20 hover:border-secondary transition-all relative text-center">
                    <div>
                        <span class="font-label-md text-label-md text-on-surface-variant font-semibold block">Foto Pendukung Tambahan</span>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">Foto pelat nama / seri unit AC atau outdoor.</p>
                    </div>
                    <div class="flex items-center justify-center py-2">
                        <span class="material-symbols-outlined text-[40px] text-on-surface-variant/40">add_photo_alternate</span>
                    </div>
                    <label class="w-full py-2 rounded-lg bg-surface-container-lowest border border-outline-variant/40 hover:bg-surface-container text-center font-caption-sm text-caption-sm font-semibold text-primary cursor-pointer">
                        <span>+ Tambah Bukti Opsional</span>
                        <input type="file" name="foto_tambahan" accept="image/*" class="hidden">
                    </label>
                </div>
            </div>
        </div>

        {{-- 5. Rekomendasi Disposisi & Tanggungan --}}
        <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-sm">
            <div class="flex items-center justify-between mb-space-md border-b border-surface-container pb-space-sm">
                <h2 class="font-title-md text-title-md text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[20px]">handyman</span>
                    5. Rekomendasi Disposisi & Tanggungan Biaya
                </h2>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Rekomendasi tindak lanjut penanganan</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-space-lg">
                {{-- Disposisi Tim Teknisi --}}
                <div class="rounded-xl bg-surface-container-low/40 border border-outline-variant/30 p-space-md">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-on-surface-variant block mb-2">Penugasan Teknisi</span>
                    <label class="flex items-center gap-2 text-label-md font-medium text-primary mb-2">
                        <input type="radio" name="tim_teknisi" value="mep" class="accent-secondary" checked>
                        <span>Tim Teknisi MEP (Mekanikal, Elektrikal, Plumbing)</span>
                    </label>
                    <label class="flex items-center gap-2 text-label-md font-medium text-primary mb-2">
                        <input type="radio" name="tim_teknisi" value="sipil" class="accent-secondary">
                        <span>Tim Sipil & Mebel</span>
                    </label>
                    <label class="flex items-center gap-2 text-label-md font-medium text-primary">
                        <input type="radio" name="tim_teknisi" value="vendor" class="accent-secondary">
                        <span>Vendor Luar / Garansi Pabrikan</span>
                    </label>
                </div>

                {{-- Status Tanggungan Biaya Mahasiswa --}}
                <div class="rounded-xl bg-surface-container-low/40 border border-outline-variant/30 p-space-md">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-on-surface-variant block mb-2">Ketetapan Tanggungan Biaya</span>
                    <label class="flex items-center gap-2 text-label-md font-medium text-primary mb-2">
                        <input type="radio" name="status_tanggungan" value="bebas" class="accent-secondary" checked>
                        <span>Bebas Biaya (Pemeliharaan Rutin / Aus Wajar)</span>
                    </label>
                    <label class="flex items-center gap-2 text-label-md font-medium text-[#C97D1A] mb-2">
                        <input type="radio" name="status_tanggungan" value="evaluasi" class="accent-[#C97D1A]">
                        <span>Perlu Evaluasi Fasilitator</span>
                    </label>
                    <label class="flex items-center gap-2 text-label-md font-medium text-error">
                        <input type="radio" name="status_tanggungan" value="beban_mahasiswa" class="accent-error">
                        <span>Beban Mahasiswa (Kelalaian/Kerusakan Fisik)</span>
                    </label>
                </div>

                {{-- Catatan Alat Pendukung --}}
                <div class="rounded-xl bg-surface-container-low/40 border border-outline-variant/30 p-space-md flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] uppercase tracking-wider font-semibold text-on-surface-variant block mb-1">Catatan Alat & Bahan Teknisi</span>
                        <textarea name="catatan_alat" rows="3" class="w-full rounded-lg bg-surface-container-lowest border border-outline-variant/30 p-2 text-caption-sm text-on-surface outline-none focus:ring-1 focus:ring-secondary resize-none" placeholder="Perlu disiapkan: Pompa cuci AC mini, plastik drainase, selang air, dan tangga lipat 2 meter.">Perlu disiapkan: Pompa pembersih pipa drainase AC, cairan pembersih koil, dan tangga lipat 2 meter.</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bilah Aksi Tombol Final --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-space-md pt-space-xs border-t border-surface-container">
            <a href="{{ route('go.pengecekan-kamar.show', $checkout->id_pengajuan ?? 1) }}" class="inline-flex items-center gap-2 px-space-lg py-3 rounded-xl border border-outline-variant/40 bg-surface-container-lowest hover:bg-surface-container-low text-on-surface-variant font-label-md text-label-md transition-colors cursor-pointer w-full sm:w-auto justify-center">
                <span class="material-symbols-outlined text-[18px]">close</span>
                <span>Batal & Kembali</span>
            </a>

            <div class="flex flex-col sm:flex-row items-center gap-space-sm w-full sm:w-auto">
                <button type="submit" name="simpan_draf" value="1" class="inline-flex items-center gap-2 px-space-lg py-3 rounded-xl border border-secondary text-secondary hover:bg-secondary/10 font-label-md text-label-md transition-colors cursor-pointer w-full sm:w-auto justify-center">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Draf Tiket</span>
                </button>

                <button type="submit" class="inline-flex items-center gap-2 px-space-xl py-3 rounded-xl bg-secondary hover:bg-secondary/90 text-on-secondary font-label-md text-label-md font-semibold shadow-brand-action transition-all cursor-pointer w-full sm:w-auto justify-center">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Kirim & Terbitkan Tiket Kerusakan</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryCards = document.querySelectorAll('.category-card');
        categoryCards.forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            card.addEventListener('click', function() {
                categoryCards.forEach(c => {
                    c.classList.remove('border-2', 'border-secondary', 'bg-secondary/10');
                    c.classList.add('border', 'border-outline-variant/30', 'bg-surface-container-low');
                    const icon = c.querySelector('.material-symbols-outlined');
                    const text = c.querySelector('span:last-child');
                    if (icon) {
                        icon.classList.remove('text-secondary');
                        icon.classList.add('text-on-surface-variant');
                    }
                    if (text) {
                        text.classList.remove('font-bold', 'text-primary');
                        text.classList.add('font-semibold', 'text-on-surface');
                    }
                });

                card.classList.remove('border', 'border-outline-variant/30', 'bg-surface-container-low');
                card.classList.add('border-2', 'border-secondary', 'bg-secondary/10');
                const icon = card.querySelector('.material-symbols-outlined');
                const text = card.querySelector('span:last-child');
                if (icon) {
                    icon.classList.remove('text-on-surface-variant');
                    icon.classList.add('text-secondary');
                }
                if (text) {
                    text.classList.remove('font-semibold', 'text-on-surface');
                    text.classList.add('font-bold', 'text-primary');
                }
            });
        });
    });
</script>
@endpush
@endsection