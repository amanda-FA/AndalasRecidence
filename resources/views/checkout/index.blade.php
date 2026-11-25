@extends('layouts.app')

@section('title', 'Check-out & Bebas Asrama - Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary text-[22px]" aria-hidden="true">assignment_return</span>
        <span class="font-label-md text-label-md">Check-out &amp; Bebas Asrama</span>
    </div>
@endsection

@section('content')
<div id="checkout-bebas-page" class="w-full max-w-[1440px] mx-auto flex flex-col gap-space-xl">

    {{-- Breadcrumb & Title --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2 text-caption-sm text-on-surface-variant mb-1">
                <a href="{{ url('/') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-primary font-medium">Check-out &amp; Bebas Asrama</span>
            </div>
            <h1 class="font-headline-md text-headline-md lg:text-[30px] text-primary font-bold">Check-out &amp; Bebas Asrama</h1>
            <p class="text-body-md text-on-surface-variant mt-1">Pantau status pengajuan check-out hunian, hasil audit fisik kamar oleh General Officer, dan perolehan Surat Bebas Asrama.</p>
        </div>

        <div class="flex flex-wrap items-center gap-space-sm">
            <button type="button" onclick="openCheckoutModal()" class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-xl border border-outline-variant/40 bg-surface-container-lowest hover:bg-surface-container-low text-on-surface-variant font-label-md text-label-md transition-colors cursor-pointer shadow-xs">
                <span class="material-symbols-outlined text-[18px]">edit_calendar</span>
                <span>{{ $checkout ? 'Perbarui Rencana Check-out' : 'Formulir Rencana Check-out' }}</span>
            </button>
            <button type="button" onclick="openSkbaModal()" class="inline-flex items-center gap-2 px-space-lg py-2.5 rounded-xl bg-secondary hover:bg-secondary/90 text-on-secondary font-label-md text-label-md font-medium shadow-brand-action transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">workspace_premium</span>
                <span>Lihat Surat Bebas (SKBA)</span>
            </button>
        </div>
    </div>

    {{-- Alert Tunggakan Pembayaran --}}
    @if(!$hunian['bebas_tunggakan'])
        <div class="rounded-2xl bg-amber-500/10 border-2 border-amber-500/40 p-space-md sm:p-space-lg flex items-start gap-space-md">
            <div class="w-11 h-11 rounded-2xl bg-amber-500/20 text-amber-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-headline-sm text-[16px] font-bold text-amber-950 flex items-center gap-2">
                    <span>Tunggakan Pembayaran Terdeteksi</span>
                    <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 text-[10px] font-bold uppercase">Wajib Lunas</span>
                </h4>
                <p class="text-body-md text-on-surface-variant mt-1 leading-relaxed">
                    Sistem mendeteksi adanya tagihan iuran/sewa hunian yang belum terselesaikan. Sesuai prosedur operasional, seluruh kewajiban finansial harus dilunasi terlebih dahulu sebelum pengajuan check-out dan penerbitan Surat Bebas Asrama (SKBA) dapat disetujui.
                </p>
                <div class="mt-3 flex items-center gap-3">
                    <a href="{{ route('pembayaran.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-label-md text-label-md font-bold transition-all shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">payments</span>
                        <span>Buka Menu Pembayaran</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Alert Penolakan Check-out --}}
    @if($checkout && $checkout->status_pengajuan === 'ditolak')
        <div class="rounded-2xl bg-error-container/40 border-2 border-error/40 p-space-md sm:p-space-lg flex items-start gap-space-md">
            <div class="w-11 h-11 rounded-2xl bg-error/15 text-error flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">cancel</span>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-headline-sm text-[16px] font-bold text-error flex items-center gap-2">
                    <span>Pengajuan Check-out Ditolak</span>
                </h4>
                <p class="text-body-md text-on-surface-variant mt-1 leading-relaxed">
                    {{ $checkout->alasan_penolakan ?: 'Terdapat kendala fisik fasilitas kamar atau kelengkapan administrasi yang belum terpenuhi. Silakan hubungi pengelola asrama atau ajukan ulang setelah kendala diselesaikan.' }}
                </p>
            </div>
        </div>
    @endif

    {{-- Notice Sesuai Business Process Terbaru --}}
    <div class="rounded-2xl bg-secondary-fixed/50 border border-secondary/20 p-space-md sm:p-space-lg flex items-start gap-space-md">
        <div class="w-10 h-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[24px]">verified</span>
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="font-label-md text-label-md font-bold text-primary flex items-center gap-2">
                <span>Alur Otomasi Bebas Asrama Terintegrasi</span>
                <span class="px-2 py-0.5 rounded-full bg-secondary text-on-secondary text-[10px] uppercase font-bold tracking-wider">Business Process Baru</span>
            </h4>
            <p class="text-body-md text-on-surface-variant mt-1 leading-relaxed">
                Anda hanya perlu mengajukan <strong>1 kali pengajuan check-out</strong>. Sistem secara otomatis menyambungkan hasil audit kamar fisik staf General Officer (GO) ke persetujuan Fasilitator. <strong>Surat Bebas Asrama (SKBA) langsung terbit otomatis tanpa perlu pengajuan terpisah</strong> dan langsung tersinkronisasi ke portal akademik kampus.
            </p>
        </div>
    </div>

    {{-- 1. Banner Hunian Aktif --}}
    <section class="rounded-3xl bg-gradient-to-br from-primary-container via-[#18265e] to-secondary text-on-primary shadow-brand-card overflow-hidden relative isolate" aria-labelledby="banner-hunian-title">
        <span class="material-symbols-outlined absolute -right-6 -bottom-10 text-[220px] text-white/[0.04] -z-10 pointer-events-none select-none">apartment</span>
        <div class="p-space-lg sm:p-space-xl flex flex-col gap-space-lg">
            {{-- Top Tag & Status --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-primary-fixed text-caption-sm-medium">
                    <span class="material-symbols-outlined text-[16px]">home_pin</span>
                    <span class="tracking-wider uppercase font-semibold text-[11px]">Hunian Aktif Mahasiswa</span>
                </div>

                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[12px] font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Status Finansial: {{ $hunian['status_finansial'] }}</span>
                </div>
            </div>

            {{-- Main Room & Occupant Info --}}
            <div class="checkout-identity-grid grid grid-cols-1 gap-6 items-center">
                <div class="flex items-center gap-4 sm:gap-6 min-w-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                        <span class="material-symbols-outlined text-[36px] sm:text-[44px] text-primary-fixed">meeting_room</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="text-caption-sm uppercase tracking-widest text-primary-fixed-dim font-medium">Nomor Kamar &amp; Alokasi Bed</span>
                        </div>
                        <h2 id="banner-hunian-title" class="font-headline-lg text-[32px] sm:text-[40px] font-bold tracking-tight text-white mt-0.5">
                            Kamar {{ $hunian['nomor_kamar'] }} <span class="text-primary-fixed-dim font-normal text-[24px] sm:text-[30px]">· {{ $hunian['bed'] }}</span>
                        </h2>
                        <p class="text-body-md text-primary-fixed/90 font-medium flex items-center gap-2 mt-1">
                            <span class="material-symbols-outlined text-[18px]">domain</span>
                            <span>{{ $hunian['gedung'] }} (Lantai {{ $hunian['lantai'] }})</span>
                        </p>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-space-md border border-white/15 flex flex-col gap-2 min-w-0">
                    <div class="flex items-center gap-2 text-primary-fixed-dim text-caption-sm">
                        <span class="material-symbols-outlined text-[16px]">badge</span>
                        <span class="uppercase tracking-wider font-semibold text-[11px]">Identitas Penghuni</span>
                    </div>
                    <p class="font-bold text-white text-[17px] leading-snug">{{ $penghuni['nama'] }}</p>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-primary-fixed text-caption-sm">
                        <span>NIM: <strong>{{ $penghuni['nim'] }}</strong></span>
                        <span>•</span>
                        <span>{{ $penghuni['prodi'] }}</span>
                    </div>
                    <div class="text-[12px] text-primary-fixed-dim truncate">
                        Kontak: {{ $penghuni['kontak'] }} · {{ $penghuni['email'] }}
                    </div>
                </div>
            </div>

            {{-- Lease Dates & Highlights Bar --}}
            <div class="pt-space-md border-t border-white/15 grid grid-cols-2 sm:grid-cols-4 gap-4 text-primary-fixed">
                <div>
                    <span class="text-[11px] text-primary-fixed-dim block uppercase tracking-wider">Tanggal Masuk</span>
                    <span class="text-label-md font-semibold text-white mt-0.5 block">{{ $hunian['tanggal_masuk'] }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-primary-fixed-dim block uppercase tracking-wider">Tanggal Berakhir Sewa</span>
                    <span class="text-label-md font-semibold text-white mt-0.5 block">{{ $hunian['tanggal_berakhir_sewa'] }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-primary-fixed-dim block uppercase tracking-wider">Deposit Jaminan</span>
                    <span class="text-label-md font-semibold text-emerald-300 mt-0.5 block">Rp {{ number_format($hunian['nominal_deposit'], 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-primary-fixed-dim block uppercase tracking-wider">Status Tanggungan</span>
                    <span class="text-label-md font-semibold text-emerald-300 mt-0.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        <span>Lunas 100%</span>
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. Tahapan Automasi Check-out --}}
    <section class="rounded-3xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-brand-card flex flex-col gap-space-lg" aria-labelledby="automation-flow-title">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 id="automation-flow-title" class="font-headline-sm text-headline-sm text-primary font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[26px]">linear_scale</span>
                    <span>Tahapan Automasi Check-out &amp; Penerbitan Surat Bebas</span>
                </h3>
                <p class="text-body-md text-on-surface-variant mt-1">Status progres otomatis dari pengajuan mahasiswa hingga dokumen bebas asrama diterbitkan.</p>
            </div>

            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-caption-sm font-semibold border border-emerald-200 self-start sm:self-auto">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Alur Selesai (100% Tervalidasi)</span>
            </span>
        </div>

        {{-- Stepper Grid --}}
        <div class="checkout-stepper-grid grid grid-cols-1 md:grid-cols-2 gap-4 relative">
            @foreach($tahapan as $t)
                <div class="rounded-2xl p-space-md border transition-all duration-200 relative flex flex-col justify-between {{ $t['status'] === 'done' ? 'bg-surface-container-lowest border-emerald-200/80 shadow-xs' : 'bg-surface-container-low/50 border-outline-variant/20' }}">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-[14px] {{ $t['status'] === 'done' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-surface-container text-on-surface-variant' }}">
                                <span class="material-symbols-outlined text-[20px]">{{ $t['ikon'] }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $t['status'] === 'done' ? 'bg-emerald-100 text-emerald-800' : 'bg-surface-container text-on-surface-variant' }}">
                                @if($t['status'] === 'done')
                                    <span class="material-symbols-outlined text-[13px]">done</span>
                                    <span>Selesai</span>
                                @else
                                    <span>Menunggu</span>
                                @endif
                            </span>
                        </div>

                        <span class="text-[11px] font-bold text-secondary tracking-wider uppercase block">Tahap 0{{ $t['urutan'] }}</span>
                        <h4 class="font-headline-sm text-[16px] font-bold text-primary mt-0.5 leading-snug">{{ $t['judul'] }}</h4>
                        <p class="text-[12px] font-medium text-on-surface-variant mt-0.5">{{ $t['subjudul'] }}</p>

                        <p class="text-caption-sm text-on-surface-variant/90 mt-2.5 leading-relaxed">
                            {{ $t['keterangan'] }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-outline-variant/10 flex items-center justify-between text-[11px] text-on-surface-variant">
                        <span class="inline-flex items-center gap-1 font-medium">
                            <span class="material-symbols-outlined text-[14px]">schedule</span>
                            <span>{{ $t['waktu'] }}</span>
                        </span>
                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 3 & 4. Grid Dua Kolom: Hasil Inspeksi Fisik (GO) & Penerbitan Otomatis SKBA --}}
    <div class="checkout-details-grid grid grid-cols-1 gap-space-xl items-start">

        {{-- Kolom Kiri (Lg:col-span-7): Hasil Inspeksi Kamar Fisik --}}
        <section class="rounded-3xl bg-surface-container-lowest border border-outline-variant/15 p-space-lg sm:p-space-xl shadow-brand-card flex flex-col gap-space-lg" aria-labelledby="inspection-card-title">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-space-sm border-b border-surface-container">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">fact_check</span>
                    </div>
                    <div>
                        <h3 id="inspection-card-title" class="font-headline-sm text-headline-sm text-primary font-bold">Hasil Inspeksi Kamar Fisik</h3>
                        <p class="text-caption-sm text-on-surface-variant mt-0.5">Audit lapangan inventaris dan serah terima kunci oleh General Officer (GO).</p>
                    </div>
                </div>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-surface-container-low text-secondary font-mono text-[12px] font-semibold self-start sm:self-auto">
                    {{ $inspeksi['no_registrasi'] }}
                </span>
            </div>

            {{-- Info Petugas Pemeriksa & Tanggal --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-surface-container-low/60 rounded-2xl p-space-md border border-outline-variant/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-white flex items-center justify-center font-bold text-[14px]">
                        RP
                    </div>
                    <div>
                        <span class="text-[11px] uppercase tracking-wider text-on-surface-variant font-medium">Staf General Officer (GO)</span>
                        <p class="font-bold text-primary text-[15px]">{{ $inspeksi['petugas'] }}</p>
                        <p class="text-[11px] text-on-surface-variant">{{ $inspeksi['nip_petugas'] }} · Staf Lapangan</p>
                    </div>
                </div>

                <div class="sm:border-l sm:border-outline-variant/20 sm:pl-4 flex flex-col justify-center">
                    <span class="text-[11px] uppercase tracking-wider text-on-surface-variant font-medium">Waktu Selesai Pengecekan</span>
                    <p class="font-bold text-primary text-[15px] mt-0.5">{{ $inspeksi['tanggal_cek'] }}</p>
                    <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1 mt-0.5">
                        <span class="material-symbols-outlined text-[13px]">verified</span>
                        <span>Berita Acara Digital Divalidasi</span>
                    </span>
                </div>
            </div>

            {{-- Metrik Penilaian Aset & Kunci --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-space-md flex flex-col gap-1">
                    <div class="flex items-center justify-between text-emerald-800">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Kondisi Aset</span>
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                    </div>
                    <p class="font-headline-sm text-[20px] font-bold text-emerald-900 mt-1">{{ $inspeksi['kondisi_aset'] }}</p>
                    <span class="text-[11px] text-emerald-700">9 dari 9 fasilitas lolos audit</span>
                </div>

                <div class="rounded-2xl border border-secondary/20 bg-secondary-fixed/30 p-space-md flex flex-col gap-1">
                    <div class="flex items-center justify-between text-secondary">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Temuan Denda</span>
                        <span class="material-symbols-outlined text-[18px]">savings</span>
                    </div>
                    <p class="font-headline-sm text-[20px] font-bold text-primary mt-1">{{ $inspeksi['temuan_denda_formatted'] }}</p>
                    <span class="text-[11px] text-on-surface-variant">Bebas beban ganti rugi</span>
                </div>

                <div class="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-space-md flex flex-col gap-1">
                    <div class="flex items-center justify-between text-on-surface-variant">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Akses &amp; RFID</span>
                        <span class="material-symbols-outlined text-[18px]">key</span>
                    </div>
                    <p class="font-headline-sm text-[20px] font-bold text-primary mt-1">Diserahkan</p>
                    <span class="text-[11px] text-emerald-700 font-medium">Kunci &amp; Kartu RFID lengkap</span>
                </div>
            </div>

            {{-- Detail Serah Terima Kunci --}}
            <div class="rounded-2xl border border-outline-variant/15 p-space-md flex flex-col gap-2">
                <span class="text-[12px] font-bold text-primary uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-secondary">vpn_key</span>
                    <span>Validasi Serah Terima Kunci &amp; Akses Ruangan</span>
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-caption-sm text-on-surface-variant">
                    <div class="p-2.5 rounded-xl bg-surface-container-low flex items-center justify-between">
                        <span>Kartu Akses RFID:</span>
                        <span class="font-semibold text-primary">RFID-MNP-0202A (Valid)</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-surface-container-low flex items-center justify-between">
                        <span>Kunci Master Kamar:</span>
                        <span class="font-semibold text-primary">1 Kunci Fisik (Lengkap)</span>
                    </div>
                </div>
                <p class="text-[11px] text-on-surface-variant italic mt-1">Catatan GO: "{{ $inspeksi['catatan'] }}"</p>
            </div>

            {{-- Refund Dana Deposit Jaminan --}}
            <div class="rounded-2xl border-2 border-emerald-500/30 bg-gradient-to-r from-emerald-50 to-teal-50/40 p-space-md sm:p-space-lg flex flex-col gap-space-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-700 text-[22px]">account_balance_wallet</span>
                        <span class="font-bold text-emerald-950 text-[15px]">Pencairan Kembali Deposit Jaminan</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-600 text-white font-bold text-[11px] uppercase tracking-wider">
                        {{ $inspeksi['deposit_refund']['status'] }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-1">
                    <div>
                        <span class="text-[11px] text-emerald-800 uppercase font-medium">Nominal Pengembalian</span>
                        <p class="font-headline-md text-[24px] font-bold text-emerald-900 leading-tight">
                            {{ $inspeksi['deposit_refund']['nominal_formatted'] }}
                        </p>
                        <span class="text-[11px] text-emerald-700">Dikembalikan 100% tanpa potongan</span>
                    </div>

                    <div class="bg-white/80 rounded-xl p-3 border border-emerald-200">
                        <span class="text-[11px] text-on-surface-variant uppercase font-medium">Rekening Tujuan Mahasiswa</span>
                        <p class="font-bold text-primary text-[14px] mt-0.5">{{ $inspeksi['deposit_refund']['bank'] }}</p>
                        <p class="font-mono font-semibold text-secondary text-[13px]">{{ $inspeksi['deposit_refund']['nomor_rekening'] }}</p>
                        <p class="text-[11px] text-on-surface-variant">a.n. {{ $inspeksi['deposit_refund']['atas_nama'] }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Kolom Kanan (Lg:col-span-5): Penerbitan Otomatis Surat Bebas Asrama (SKBA) --}}
        <section class="rounded-3xl bg-surface-container-lowest border-2 border-secondary/30 p-space-lg sm:p-space-xl shadow-brand-card flex flex-col gap-space-lg relative overflow-hidden" aria-labelledby="skba-card-title">
            {{-- Decorative seal watermark --}}
            <span class="material-symbols-outlined absolute -right-6 -bottom-10 text-[180px] text-secondary/[0.04] pointer-events-none select-none">verified</span>

            <div class="flex items-center justify-between pb-space-sm border-b border-surface-container">
                <div class="flex items-center gap-2.5">
                    <div class="w-11 h-11 rounded-2xl bg-secondary text-white flex items-center justify-center shrink-0 shadow-brand-action">
                        <span class="material-symbols-outlined text-[24px]">workspace_premium</span>
                    </div>
                    <div>
                        <h3 id="skba-card-title" class="font-headline-sm text-headline-sm text-primary font-bold">Surat Bebas Asrama</h3>
                        <p class="text-caption-sm text-on-surface-variant mt-0.5">Dokumen Resmi Terbit Otomatis</p>
                    </div>
                </div>

                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                    <span class="material-symbols-outlined text-[13px]">check_circle</span>
                    <span>Terbit Resmi</span>
                </span>
            </div>

            {{-- Card Dokumen Resmi --}}
            <div class="rounded-2xl border border-secondary/20 bg-gradient-to-br from-surface-container-lowest via-surface-container-low/40 to-secondary-fixed/20 p-space-md sm:p-space-lg flex flex-col gap-space-md relative">
                {{-- Kop Mini Dokumen --}}
                <div class="flex items-center gap-3 pb-3 border-b border-outline-variant/20">
                    <img src="{{ asset('images/logo_andalasresidence.png') }}" alt="Logo UNAND" class="w-10 h-10 object-contain rounded-lg bg-white p-1 shadow-xs">
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-on-surface-variant font-bold">Universitas Andalas</p>
                        <p class="text-[12px] font-bold text-primary">UPT Asrama Mahasiswa</p>
                    </div>
                </div>

                <div>
                    <span class="text-[11px] text-on-surface-variant uppercase tracking-wider font-semibold">Nomor Registrasi SKBA</span>
                    <p class="font-mono font-bold text-secondary text-[16px] sm:text-[17px] mt-0.5">{{ $suratBebas['nomor_surat'] }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-caption-sm">
                    <div>
                        <span class="text-[11px] text-on-surface-variant uppercase">Tanggal Terbit:</span>
                        <p class="font-semibold text-primary">{{ $suratBebas['tanggal_terbit'] }}</p>
                    </div>
                    <div>
                        <span class="text-[11px] text-on-surface-variant uppercase">Otentikasi:</span>
                        <p class="font-semibold text-emerald-700 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">lock</span>
                            <span>Digital Signature</span>
                        </p>
                    </div>
                </div>

                {{-- Digital Seal Stamp Representation --}}
                <div class="rounded-xl bg-white p-3 border border-secondary/30 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full border-2 border-dashed border-secondary text-secondary flex flex-col items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">approval_delegation</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary block">Digital Seal Terverifikasi</span>
                        <p class="text-[12px] font-bold text-primary truncate">{{ $suratBebas['pejabat_nama'] }}</p>
                        <p class="text-[10px] text-on-surface-variant truncate">{{ $suratBebas['pejabat_jabatan'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Integrasi Portal Akademik (KRS & Wisuda) --}}
            <div class="rounded-2xl bg-secondary-fixed/40 border border-secondary/20 p-space-md flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary text-[22px] shrink-0 mt-0.5">sync_saved_locally</span>
                <div>
                    <h5 class="text-label-md font-bold text-primary">{{ $suratBebas['integrasi_portal'] }}</h5>
                    <p class="text-caption-sm text-on-surface-variant mt-1 leading-relaxed">
                        Data bebas asrama Anda telah disinkronkan secara real-time ke sistem portal akademik universitas. Anda <strong>tidak perlu mengunggah surat fisik</strong> saat pengisian KRS semester depan atau pendaftaran sidang/wisuda.
                    </p>
                </div>
            </div>

            {{-- Tombol Aksi Utama --}}
            <div class="flex flex-col gap-2.5 pt-2">
                <a href="{{ route('checkout.surat-bebas.preview') }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-space-lg py-3 rounded-xl bg-secondary hover:bg-secondary/90 text-on-secondary font-label-md text-label-md font-bold shadow-brand-action transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                    <span>Unduh Surat Bebas Asrama (.PDF)</span>
                </a>

                <button type="button" onclick="openSkbaModal()" class="w-full inline-flex items-center justify-center gap-2 px-space-lg py-3 rounded-xl border border-secondary/30 bg-surface-container-lowest hover:bg-surface-container-low text-secondary font-label-md text-label-md font-semibold transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                    <span>Lihat Pratinjau Dokumen</span>
                </button>
            </div>
        </section>

    </div>
</div>

{{-- MODAL PRATINJAU DOKUMEN SURAT BEBAS ASRAMA (SKBA) --}}
<div id="skba-preview-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-primary/60 backdrop-blur-xs" role="dialog" aria-modal="true" aria-labelledby="skba-modal-title">
    <div class="bg-surface-container-lowest rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-[fadeIn_0.15s_ease-out]">
        {{-- Modal Header --}}
        <div class="px-space-lg py-space-md border-b border-surface-container flex items-center justify-between bg-surface-container-low/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[22px]">description</span>
                <h3 id="skba-modal-title" class="font-headline-sm text-headline-sm text-primary font-bold">Pratinjau Surat Bebas Asrama (SKBA)</h3>
            </div>
            <button type="button" onclick="closeSkbaModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer" aria-label="Tutup">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Modal Document Content (Official Letter Format) --}}
        <div class="p-space-lg sm:p-space-xl overflow-y-auto flex-1 bg-[#fafafa]">
            <div id="skba-printable-area" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 sm:p-12 text-[#111827] max-w-[680px] mx-auto flex flex-col gap-6 font-serif">
                
                {{-- Kop Surat Resmi UNAND --}}
                <div class="flex items-center gap-4 pb-4 border-b-2 border-double border-gray-800 text-center font-sans">
                    <img src="{{ asset('images/logo_andalasresidence.png') }}" alt="Logo UNAND" class="w-16 h-16 object-contain">
                    <div class="flex-1">
                        <p class="text-[12px] font-bold tracking-wider uppercase">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</p>
                        <h4 class="text-[17px] font-black uppercase text-[#000a39] tracking-tight">Universitas Andalas</h4>
                        <p class="text-[14px] font-bold uppercase text-secondary">Unit Pelaksana Teknis (UPT) Asrama Mahasiswa</p>
                        <p class="text-[10px] text-gray-500 font-normal mt-0.5">Gedung Pusat Kegiatan Mahasiswa Lt. 2, Kampus Limau Manis, Padang 25163 | Telp: (0751) 71181</p>
                    </div>
                </div>

                {{-- Judul Surat --}}
                <div class="text-center font-sans mt-2">
                    <h2 class="text-[16px] font-bold uppercase underline tracking-wider text-[#000a39]">SURAT KETERANGAN BEBAS ASRAMA (SKBA)</h2>
                    <p class="text-[13px] font-semibold text-gray-700 mt-0.5">Nomor: {{ $suratBebas['nomor_surat'] }}</p>
                </div>

                {{-- Isi Surat --}}
                <div class="text-[13px] leading-relaxed font-sans text-gray-800 flex flex-col gap-4">
                    <p>Kepala Unit Pelaksana Teknis (UPT) Asrama Mahasiswa Universitas Andalas dengan ini menerangkan bahwa:</p>

                    <div class="grid grid-cols-[140px_10px_1fr] gap-y-1.5 px-4 font-sans text-[13px]">
                        <span class="text-gray-600">Nama Lengkap</span><span>:</span><strong class="text-gray-900">{{ $penghuni['nama'] }}</strong>
                        <span class="text-gray-600">NIM</span><span>:</span><strong class="text-gray-900">{{ $penghuni['nim'] }}</strong>
                        <span class="text-gray-600">Fakultas / Prodi</span><span>:</span><span>{{ $penghuni['fakultas'] }} / {{ $penghuni['prodi'] }}</span>
                        <span class="text-gray-600">Alokasi Hunian</span><span>:</span><span>{{ $hunian['gedung'] }}, Kamar {{ $hunian['nomor_kamar'] }} ({{ $hunian['bed'] }})</span>
                        <span class="text-gray-600">Periode Tinggal</span><span>:</span><span>{{ $hunian['periode'] }} (Masuk: {{ $hunian['tanggal_masuk'] }})</span>
                    </div>

                    <p class="mt-2 text-justify">
                        Telah secara resmi menyelesaikan masa hunian di Asrama Mahasiswa Universitas Andalas, menyerahkan seluruh inventaris kamar dalam kondisi <strong>Lengkap &amp; Bersih</strong>, melunasi seluruh kewajiban finansial (Bebas Tunggakan), serta telah menyelesaikan serah terima kunci fisik dan kartu akses RFID.
                    </p>

                    <p class="text-justify">
                        Surat keterangan ini diterbitkan secara otomatis melalui Sistem Informasi Andalas Residence untuk dipergunakan sebagaimana mestinya sebagai bukti pemenuhan syarat bebas asrama pada <strong>Portal Akademik Universitas Andalas (KRS &amp; Wisuda)</strong>.
                    </p>
                </div>

                {{-- Tanda Tangan & Digital Seal --}}
                <div class="grid grid-cols-2 gap-4 mt-6 pt-4 border-t border-gray-100 font-sans text-[12px] items-end">
                    <div class="flex flex-col gap-2">
                        <div class="w-24 h-24 border border-gray-300 p-1 bg-white rounded-lg flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-[48px] text-gray-800">qr_code_2</span>
                            <span class="text-[8px] font-mono text-gray-500">AUTHENTIC</span>
                        </div>
                        <p class="text-[10px] text-gray-500">Scan QR Code untuk verifikasi otentikasi dokumen secara online.</p>
                    </div>

                    <div class="text-right flex flex-col items-end">
                        <p>Padang, {{ $suratBebas['tanggal_terbit'] }}</p>
                        <p class="font-bold text-[#000a39]">{{ $suratBebas['pejabat_jabatan'] }},</p>
                        <div class="my-2 px-3 py-1.5 rounded-lg border border-secondary/30 bg-secondary-fixed/30 text-secondary text-right inline-block">
                            <span class="text-[10px] font-bold uppercase tracking-wider block">Ditandatangani secara Digital</span>
                            <span class="text-[9px] font-mono block text-gray-600">{{ $suratBebas['verification_code'] }}</span>
                        </div>
                        <p class="font-bold underline text-gray-900 mt-1">{{ $suratBebas['pejabat_nama'] }}</p>
                        <p class="text-[11px] text-gray-600">NIP. {{ $suratBebas['pejabat_nip'] }}</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Modal Actions --}}
        <div class="px-space-lg py-space-md border-t border-surface-container bg-surface-container-low/50 flex flex-wrap items-center justify-between gap-3">
            <span class="text-caption-sm text-on-surface-variant flex items-center gap-1.5">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">verified</span>
                <span>Dokumen Sah &amp; Tersambung ke Portal Akademik</span>
            </span>

            <div class="flex items-center gap-2">
                <button type="button" onclick="closeSkbaModal()" class="px-space-md py-2 rounded-xl border border-outline-variant/40 bg-surface-container text-on-surface hover:bg-surface-container-high font-label-md text-label-md transition-colors cursor-pointer">
                    Tutup
                </button>
                <button type="button" onclick="window.print()" class="px-space-lg py-2 rounded-xl bg-secondary hover:bg-secondary/90 text-on-secondary font-label-md text-label-md font-bold shadow-brand-action transition-all cursor-pointer inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Cetak / Unduh PDF</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL FORMULIR CHECK-OUT MANDIRI (SINGLE-ENTRY) --}}
<div id="checkout-form-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-primary/60 backdrop-blur-xs" role="dialog" aria-modal="true" aria-labelledby="form-modal-title">
    <div class="bg-surface-container-lowest rounded-3xl shadow-2xl max-w-xl w-full p-space-lg sm:p-space-xl flex flex-col gap-space-lg animate-[fadeIn_0.15s_ease-out]">
        <div class="flex items-center justify-between border-b border-surface-container pb-space-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">assignment_turned_in</span>
                </div>
                <div>
                    <h3 id="form-modal-title" class="font-headline-sm text-headline-sm text-primary font-bold">Formulir Rencana Check-out</h3>
                    <p class="text-caption-sm text-on-surface-variant mt-0.5">Pengajuan terpadu check-out &amp; bebas asrama.</p>
                </div>
            </div>
            <button type="button" onclick="closeCheckoutModal()" class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" class="flex flex-col gap-space-md">
            @csrf

            @if(!$hunian['bebas_tunggakan'])
                <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-900 text-caption-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-amber-700">warning</span>
                    <span>Anda masih memiliki tunggakan tagihan pembayaran. Pengajuan hanya dapat diproses setelah tagihan diselesaikan.</span>
                </div>
            @endif

            <div class="flex flex-col gap-1.5">
                <label for="tanggal_rencanaKeluar" class="font-label-md text-label-md font-bold text-primary">Rencana Tanggal Keluar Hunian <span class="text-error">*</span></label>
                <input type="date" id="tanggal_rencanaKeluar" name="tanggal_rencanaKeluar" min="{{ now()->addDays(2)->format('Y-m-d') }}" value="{{ old('tanggal_rencanaKeluar', $checkout?->tanggal_rencanaKeluar ? \Carbon\Carbon::parse($checkout->tanggal_rencanaKeluar)->format('Y-m-d') : now()->addDays(2)->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/40 bg-surface-container-lowest focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none text-on-surface text-body-md transition-all">
                <span class="text-caption-sm text-on-surface-variant">Minimal H+2 dari hari ini untuk memberikan waktu penjadwalan audit fisik oleh tim GO.</span>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="alasan" class="font-label-md text-label-md font-bold text-primary">Alasan Pengajuan Check-out <span class="text-error">*</span></label>
                <select id="alasan" name="alasan" required class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/40 bg-surface-container-lowest focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none text-on-surface text-body-md transition-all">
                    @php $selectedAlasan = old('alasan', $checkout?->alasan ?? 'Selesai masa studi / semester'); @endphp
                    <option value="Selesai masa studi / semester" {{ $selectedAlasan === 'Selesai masa studi / semester' ? 'selected' : '' }}>Selesai masa studi / semester</option>
                    <option value="Kelulusan / Wisuda" {{ $selectedAlasan === 'Kelulusan / Wisuda' ? 'selected' : '' }}>Kelulusan / Wisuda</option>
                    <option value="Pindah domisili / tempat tinggal" {{ $selectedAlasan === 'Pindah domisili / tempat tinggal' ? 'selected' : '' }}>Pindah domisili / tempat tinggal</option>
                    <option value="Alasan pribadi atau keluarga" {{ $selectedAlasan === 'Alasan pribadi atau keluarga' ? 'selected' : '' }}>Alasan pribadi atau keluarga</option>
                </select>
            </div>

            <div class="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-space-md flex flex-col gap-3">
                <span class="text-label-md font-bold text-primary flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-secondary text-[18px]">account_balance</span>
                    <span>Rekening Pengembalian Dana Deposit (Rp 50.000)</span>
                </span>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1">
                        <label for="nama_bank" class="text-caption-sm font-medium text-on-surface-variant">Bank Tujuan</label>
                        <input type="text" id="nama_bank" name="nama_bank" value="{{ old('nama_bank', $checkout?->nama_bank ?? 'Bank Nagari') }}" class="px-3 py-2 rounded-lg border border-outline-variant/40 bg-white text-body-md">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="nomor_rekening" class="text-caption-sm font-medium text-on-surface-variant">Nomor Rekening</label>
                        <input type="text" id="nomor_rekening" name="nomor_rekening" value="{{ old('nomor_rekening', $checkout?->nomor_rekening ?? '2100.0210.45892-1') }}" class="px-3 py-2 rounded-lg border border-outline-variant/40 bg-white text-body-md font-mono">
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="nama_pemilik_rekening" class="text-caption-sm font-medium text-on-surface-variant">Nama Pemilik Rekening</label>
                    <input type="text" id="nama_pemilik_rekening" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening', $checkout?->nama_pemilik_rekening ?? $penghuni['nama']) }}" class="px-3 py-2 rounded-lg border border-outline-variant/40 bg-white text-body-md">
                </div>
            </div>

            <div class="flex items-center justify-end gap-space-sm pt-space-xs">
                <button type="button" onclick="closeCheckoutModal()" class="px-space-md py-2.5 rounded-xl border border-outline-variant/40 bg-surface-container text-on-surface hover:bg-surface-container-high font-label-md text-label-md transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" {{ !$hunian['bebas_tunggakan'] ? 'disabled' : '' }} class="px-space-lg py-2.5 rounded-xl bg-secondary hover:bg-secondary/90 disabled:opacity-50 disabled:cursor-not-allowed text-on-secondary font-label-md text-label-md font-bold shadow-brand-action transition-all cursor-pointer">
                    Kirim Permohonan Check-out
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSkbaModal() {
        const modal = document.getElementById('skba-preview-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeSkbaModal() {
        const modal = document.getElementById('skba-preview-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function openCheckoutModal() {
        const modal = document.getElementById('checkout-form-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeCheckoutModal() {
        const modal = document.getElementById('checkout-form-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
@endsection
