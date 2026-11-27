@extends('layouts.app')

@section('title', 'Monitoring Kerusakan Fasilitas - Pimpinan Andalas Residence')

@push('styles')
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 1.2cm 1cm 1.2cm 1cm;
        }
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 10pt !important;
        }
        .print\:hidden {
            display: none !important;
        }
        .print\:block {
            display: block !important;
        }
        .print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .print-table th, .print-table td {
            border: 1px solid #222222 !important;
            padding: 4px 6px !important;
            vertical-align: top;
        }
        .print-table th {
            background-color: #f1f5f9 !important;
            font-weight: bold;
            text-align: center;
        }
        .kop-border {
            border-bottom: 3px double #000000 !important;
        }
    }
</style>
@endpush

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary text-[22px]">monitoring</span>
        <span class="font-label-md text-label-md font-semibold">Monitoring Kerusakan Fasilitas</span>
    </div>
@endsection

@section('content')

{{-- ========================================================================= --}}
{{-- 1. TAMPILAN INTERAKTIF WEB (Disembunyikan Otomatis Saat Dicetak / Print)   --}}
{{-- ========================================================================= --}}
<div class="max-w-6xl mx-auto w-full flex flex-col gap-space-lg print:hidden">

    {{-- Title & Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Monitoring Kerusakan Fasilitas</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Pemantauan rekapitulasi status penanganan fasilitas di seluruh asrama.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-space-md py-2 rounded-lg bg-surface-container-lowest text-primary shadow-sm hover:bg-surface-container-low transition-all font-label-md text-label-md cursor-pointer border border-outline-variant/10">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak Rekap</span>
            </button>
        </div>
    </div>

    {{-- 4 Stat Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-space-md">
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Total Laporan</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ $kpi['total_laporan'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tiket</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Selesai Diperbaiki</span>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="flex items-baseline gap-2">
                    <span class="font-headline-lg text-headline-lg text-success font-bold">{{ $kpi['selesai'] }}</span>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tiket</span>
                </div>
                <span class="text-caption-sm font-semibold text-success">{{ $kpi['persen_selesai'] }}%</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Sedang Dikerjakan</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-[#99620a] font-bold">{{ $kpi['dalam_pengerjaan'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tiket</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Menunggu / Tertunda</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-secondary font-bold">{{ $kpi['menunggu'] + $kpi['tertunda'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tiket</span>
            </div>
        </div>
    </div>

    {{-- Rekapitulasi per Gedung --}}
    <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-outline-variant/10">
        <h2 class="font-title-md text-title-md text-primary font-semibold mb-3">Distribusi Penanganan per Gedung</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-space-sm">
            @foreach ($rekapGedung as $gedung)
                <div class="p-3 bg-surface-container-low rounded-lg flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <strong class="text-primary text-[13px]">{{ $gedung['nama_gedung'] }}</strong>
                        <span class="text-[11px] font-semibold text-secondary">{{ $gedung['total'] }} Laporan</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-caption-sm text-on-surface-variant">
                        <span>Selesai: <strong class="text-success">{{ $gedung['selesai'] }}</strong></span>
                        <span>Proses: <strong class="text-[#99620a]">{{ $gedung['dalam_proses'] }}</strong></span>
                        <span>Penyelesaian: <strong class="text-primary">{{ $gedung['tingkat_selesai'] }}%</strong></span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Tabel Laporan Terkini --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/10 flex flex-col">
        <div class="p-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm border-b border-surface-container">
            <h2 class="font-title-md text-title-md text-primary font-semibold">Daftar Laporan Terkini</h2>
            
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input id="search-monitoring" type="search" placeholder="Cari tiket, gedung, fasilitas..." class="px-3 py-1.5 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-secondary/20 w-full sm:w-64">
                <select id="filter-status-monitoring" class="px-3 py-1.5 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none cursor-pointer">
                    <option value="semua">Semua Status</option>
                    <option value="selesai">Selesai</option>
                    <option value="dalam_pengerjaan">Dalam Pengerjaan</option>
                    <option value="diajukan">Menunggu</option>
                    <option value="tertunda">Tertunda</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-body-md border-collapse">
                <thead class="bg-surface-container-low text-on-surface-variant text-[11px] uppercase font-semibold">
                    <tr>
                        <th class="py-2.5 px-space-md w-28">ID Tiket</th>
                        <th class="py-2.5 px-space-md w-28">Tgl Lapor</th>
                        <th class="py-2.5 px-space-md">Fasilitas &amp; Kendala</th>
                        <th class="py-2.5 px-space-md w-40">Lokasi Hunian</th>
                        <th class="py-2.5 px-space-md w-36">Pelapor</th>
                        <th class="py-2.5 px-space-md w-40">Teknisi</th>
                        <th class="py-2.5 px-space-md text-center w-28">Status</th>
                        <th class="py-2.5 px-space-md text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody id="rows-monitoring" class="divide-y divide-surface-container">
                    @foreach ($reports as $item)
                        <tr class="monitoring-row hover:bg-surface-container-low/30 transition-colors"
                            data-keyword="{{ strtolower($item['nomor_tiket'] . ' ' . $item['gedung_nama'] . ' ' . $item['fasilitas_nama'] . ' ' . $item['deskripsi']) }}"
                            data-status="{{ $item['status_perbaikan'] }}">
                            <td class="py-3 px-space-md font-mono text-[13px] font-semibold text-secondary">
                                {{ $item['nomor_tiket'] }}
                            </td>
                            <td class="py-3 px-space-md text-[13px] text-on-surface">
                                {{ $item['tanggal_lapor'] }}
                            </td>
                            <td class="py-3 px-space-md">
                                <div class="font-medium text-primary text-[13px]">{{ $item['fasilitas_nama'] }}</div>
                                <div class="text-[12px] text-on-surface-variant line-clamp-1">{{ $item['deskripsi'] }}</div>
                            </td>
                            <td class="py-3 px-space-md text-[13px]">
                                <div class="text-primary font-medium">{{ $item['gedung_nama'] }}</div>
                                <div class="text-[11px] text-outline">{{ $item['kamar_nomor'] }}</div>
                            </td>
                            <td class="py-3 px-space-md text-[13px] text-on-surface">
                                {{ $item['pelapor_nama'] }}
                            </td>
                            <td class="py-3 px-space-md text-[13px] text-on-surface">
                                {{ $item['teknisi_nama'] }}
                            </td>
                            <td class="py-3 px-space-md text-center">
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium {{ $item['status_badge'] }}">
                                    {{ $item['status_label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-space-md text-center">
                                <button type="button" 
                                    onclick='openDetailLaporan(@json($item))'
                                    class="px-2.5 py-1 text-[12px] font-medium text-primary bg-surface-container hover:bg-surface-container-high rounded transition-colors cursor-pointer">
                                    Lihat
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-space-md py-2.5 border-t border-surface-container flex items-center justify-between text-[11px] text-on-surface-variant bg-surface-container-lowest">
            <span>Total laporan tercatat: <strong class="text-primary">{{ $reports->count() }}</strong></span>
            <span>Data diperbarui secara real-time dari sistem pelaporan</span>
        </div>
    </div>

</div>

{{-- ========================================================================= --}}
{{-- 2. DOKUMEN CETAK RESMI (Hanya Muncul Saat Mencetak / PDF - Resmi & Rapi)    --}}
{{-- ========================================================================= --}}
<div class="hidden print:block font-serif text-black leading-tight">
    
    {{-- Kop Surat Resmi Universitas Andalas --}}
    <div class="flex items-center justify-between pb-3 mb-4 kop-border">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/logo_unand.png') }}" alt="Logo Universitas Andalas" class="w-16 h-20 object-contain shrink-0">
            <div>
                <h4 class="text-[10pt] uppercase tracking-wider font-semibold text-gray-800">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h4>
                <h2 class="text-[14pt] font-bold uppercase tracking-wider text-black">UNIVERSITAS ANDALAS</h2>
                <h3 class="text-[12pt] font-bold uppercase text-black">UPT ASRAMA MAHASISWA (ANDALAS RESIDENCE)</h3>
                <p class="text-[8.5pt] text-gray-700 italic">Gedung Pengelola Asrama, Kampus Limau Manis, Padang 25163 | Telp: (0751) 71181 | Laman: asrama.unand.ac.id</p>
            </div>
        </div>
    </div>

    {{-- Judul Laporan Formal --}}
    <div class="text-center my-4">
        <h1 class="text-[13pt] font-bold uppercase underline tracking-wide">REKAPITULASI PEMANTAUAN KERUSAKAN FASILITAS ASRAMA</h1>
        <p class="text-[9.5pt] text-gray-800 mt-1">Nomor Dokumen: AR/MNT-KERUSAKAN/{{ date('Y/m') }}/01 &bull; Tanggal Cetak: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
    </div>

    {{-- I. Ringkasan Penanganan Fasilitas --}}
    <div class="mb-4">
        <h5 class="text-[10pt] font-bold uppercase mb-1.5">I. RINGKASAN PENANGANAN FASILITAS EKSEKUTIF</h5>
        <table class="print-table">
            <thead>
                <tr>
                    <th>Total Laporan</th>
                    <th>Selesai Diperbaiki</th>
                    <th>Sedang Dikerjakan</th>
                    <th>Menunggu / Tertunda</th>
                    <th>Tingkat Penyelesaian (%)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center font-medium">
                    <td>{{ $kpi['total_laporan'] }} Tiket</td>
                    <td>{{ $kpi['selesai'] }} Tiket</td>
                    <td>{{ $kpi['dalam_pengerjaan'] }} Tiket</td>
                    <td>{{ $kpi['menunggu'] + $kpi['tertunda'] }} Tiket</td>
                    <td><strong>{{ $kpi['persen_selesai'] }}%</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- II. Distribusi per Gedung --}}
    <div class="mb-4">
        <h5 class="text-[10pt] font-bold uppercase mb-1.5">II. REKAPITULASI PENANGANAN PER GEDUNG ASRAMA</h5>
        <table class="print-table">
            <thead>
                <tr>
                    <th class="w-8">No</th>
                    <th>Nama Gedung Asrama</th>
                    <th class="w-24">Total Laporan</th>
                    <th class="w-24">Selesai</th>
                    <th class="w-28">Dalam Proses</th>
                    <th class="w-28">Penyelesaian (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekapGedung as $index => $gedung)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $gedung['nama_gedung'] }}</td>
                        <td class="text-center">{{ $gedung['total'] }}</td>
                        <td class="text-center">{{ $gedung['selesai'] }}</td>
                        <td class="text-center">{{ $gedung['dalam_proses'] }}</td>
                        <td class="text-center font-semibold">{{ $gedung['tingkat_selesai'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- III. Rincian Laporan Kerusakan --}}
    <div class="mb-6">
        <h5 class="text-[10pt] font-bold uppercase mb-1.5">III. RINCIAN STATUS TIKET KERUSAKAN FASILITAS</h5>
        <table class="print-table">
            <thead>
                <tr>
                    <th class="w-7">No</th>
                    <th class="w-24">No. Tiket</th>
                    <th class="w-20">Tgl Lapor</th>
                    <th>Fasilitas &amp; Kendala Kerusakan</th>
                    <th class="w-32">Lokasi Hunian</th>
                    <th class="w-28">Pelapor</th>
                    <th class="w-32">Teknisi</th>
                    <th class="w-20">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center font-mono font-semibold text-[9pt]">{{ $item['nomor_tiket'] }}</td>
                        <td class="text-center text-[9pt]">{{ $item['tanggal_lapor'] }}</td>
                        <td>
                            <strong>{{ $item['fasilitas_nama'] }}</strong><br>
                            <span class="text-[8.5pt] text-gray-700">{{ $item['deskripsi'] }}</span>
                        </td>
                        <td>{{ $item['gedung_nama'] }}<br><span class="text-[8.5pt] text-gray-700">{{ $item['kamar_nomor'] }}</span></td>
                        <td>{{ $item['pelapor_nama'] }}</td>
                        <td>{{ $item['teknisi_nama'] }}</td>
                        <td class="text-center font-semibold">{{ $item['status_label'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Kolom Pengesahan Tanda Tangan Resmi --}}
    <div class="mt-8 pt-2 flex justify-between text-[10pt]" style="page-break-inside: avoid;">
        <div class="text-center w-64">
            <p>Mengetahui,</p>
            <p class="font-bold">Koordinator Sarana &amp; Fasilitas</p>
            <div class="h-20"></div>
            <p class="font-bold underline">Rahadian Triputra, S.T., M.T.</p>
            <p class="text-[8.5pt]">NIP. 198503152010121002</p>
        </div>
        <div class="text-center w-64">
            <p>Padang, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
            <p class="font-bold">Kepala UPT Asrama Mahasiswa</p>
            <div class="h-20"></div>
            <p class="font-bold underline">Dr. Eng. Ir. H. Pimpinan Asrama, M.T.</p>
            <p class="text-[8.5pt]">NIP. 197405201999031001</p>
        </div>
    </div>

</div>

{{-- Modal Detail Laporan Kerusakan (Read-Only Pimpinan) --}}
<div id="modal-detail-laporan" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs print:hidden">
    <div class="bg-surface-container-lowest rounded-xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-xl border border-outline-variant/10 overflow-hidden">
        
        {{-- Header Modal --}}
        <div class="p-space-md border-b border-surface-container flex items-center justify-between bg-surface-container-low/40">
            <div class="flex items-center gap-2">
                <span id="modal-nomor-tiket" class="font-mono text-title-md font-bold text-primary">TKT-XXXX</span>
                <span id="modal-status-badge" class="px-2 py-0.5 rounded text-[11px] font-medium">Status</span>
            </div>
            <button type="button" onclick="closeDetailLaporan()" class="p-1 text-on-surface-variant hover:text-primary rounded-lg transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Body Modal (Scrollable) --}}
        <div class="p-space-md overflow-y-auto flex flex-col gap-space-md text-[13px]">
            {{-- Info Pokok --}}
            <div class="grid grid-cols-2 gap-3 bg-surface-container-low/40 p-3 rounded-lg">
                <div>
                    <span class="text-caption-sm text-on-surface-variant block">Pelapor &amp; Kontak</span>
                    <strong id="modal-pelapor" class="text-primary block">-</strong>
                    <span id="modal-kontak-pelapor" class="text-[11px] text-outline block">-</span>
                </div>
                <div>
                    <span class="text-caption-sm text-on-surface-variant block">Lokasi Hunian</span>
                    <strong id="modal-gedung" class="text-primary block">-</strong>
                    <span id="modal-kamar" class="text-[11px] text-outline block">-</span>
                </div>
                <div>
                    <span class="text-caption-sm text-on-surface-variant block">Teknisi Penanggung Jawab</span>
                    <strong id="modal-teknisi" class="text-primary block">-</strong>
                </div>
                <div>
                    <span class="text-caption-sm text-on-surface-variant block">Target Batas Waktu (SLA)</span>
                    <span id="modal-sla" class="text-secondary font-medium block">-</span>
                </div>
            </div>

            {{-- Fasilitas & Kendala --}}
            <div>
                <span class="text-caption-sm text-on-surface-variant block mb-1">Fasilitas yang Dilaporkan</span>
                <strong id="modal-fasilitas" class="text-primary block text-[14px]">-</strong>
                <p id="modal-deskripsi" class="mt-1 text-on-surface bg-surface-container-low/30 p-2.5 rounded border border-surface-container/60 leading-relaxed"></p>
            </div>

            {{-- Catatan Pengerjaan & Suku Cadang --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-surface-container-low/30 p-2.5 rounded border border-surface-container/60">
                    <span class="text-caption-sm text-on-surface-variant block font-medium mb-1">Catatan Penanganan</span>
                    <p id="modal-catatan" class="text-[12px] text-on-surface leading-normal">-</p>
                </div>
                <div class="bg-surface-container-low/30 p-2.5 rounded border border-surface-container/60">
                    <span class="text-caption-sm text-on-surface-variant block font-medium mb-1">Kebutuhan Suku Cadang</span>
                    <p id="modal-suku-cadang" class="text-[12px] text-on-surface leading-normal">-</p>
                </div>
            </div>

            {{-- Linimasa Perkembangan --}}
            <div>
                <span class="text-caption-sm text-on-surface-variant block font-medium mb-2">Linimasa Perkembangan Penanganan</span>
                <div id="modal-linimasa" class="flex flex-col gap-2">
                    {{-- Dinamis diisi JS --}}
                </div>
            </div>

            {{-- Notice Read-Only --}}
            <div class="p-2.5 bg-surface-container-low rounded text-[11px] text-on-surface-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-secondary">visibility</span>
                <span>Akses Mode Baca: Pimpinan memantau perkembangan dan riwayat penanganan tanpa mengubah data tiket.</span>
            </div>
        </div>

        {{-- Footer Modal --}}
        <div class="p-3 border-t border-surface-container flex items-center justify-end bg-surface-container-low/40">
            <button type="button" onclick="closeDetailLaporan()" class="px-4 py-1.5 rounded-lg bg-surface-container-high text-primary hover:bg-surface-container-highest transition-colors text-[12px] font-medium cursor-pointer">
                Tutup
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
    const searchInput = document.getElementById('search-monitoring');
    const statusSelect = document.getElementById('filter-status-monitoring');
    const rows = document.querySelectorAll('.monitoring-row');

    function filterMonitoring() {
        const q = searchInput.value.toLowerCase().trim();
        const s = statusSelect.value;

        rows.forEach(r => {
            const kw = r.dataset.keyword;
            const st = r.dataset.status;

            const matchQ = !q || kw.includes(q);
            const matchS = (s === 'semua') || (st === s);

            r.style.display = (matchQ && matchS) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterMonitoring);
    statusSelect.addEventListener('change', filterMonitoring);

    // Modal Detail Logic
    const modalDetail = document.getElementById('modal-detail-laporan');

    function openDetailLaporan(item) {
        document.getElementById('modal-nomor-tiket').textContent = item.nomor_tiket;
        
        const badge = document.getElementById('modal-status-badge');
        badge.textContent = item.status_label;
        badge.className = 'px-2 py-0.5 rounded text-[11px] font-medium ' + item.status_badge;

        document.getElementById('modal-pelapor').textContent = item.pelapor_nama;
        document.getElementById('modal-kontak-pelapor').textContent = item.kontak_pelapor || '-';
        document.getElementById('modal-gedung').textContent = item.gedung_nama;
        document.getElementById('modal-kamar').textContent = item.kamar_nomor;
        document.getElementById('modal-teknisi').textContent = item.teknisi_nama;
        document.getElementById('modal-sla').textContent = item.sla_target || 'Target SLA: 24 Jam';
        document.getElementById('modal-fasilitas').textContent = item.fasilitas_nama;
        document.getElementById('modal-deskripsi').textContent = item.deskripsi;
        document.getElementById('modal-catatan').textContent = item.catatan_penanganan || 'Belum ada catatan khusus.';
        document.getElementById('modal-suku-cadang').textContent = item.kebutuhan_suku_cadang || 'Tidak membutuhkan suku cadang khusus.';

        // Render linimasa
        const timelineContainer = document.getElementById('modal-linimasa');
        timelineContainer.innerHTML = '';
        if (item.linimasa && Array.isArray(item.linimasa)) {
            item.linimasa.forEach(step => {
                const el = document.createElement('div');
                el.className = 'flex items-center justify-between p-2 rounded bg-surface-container-low/50 text-[12px]';
                
                let dotColor = 'bg-outline';
                if (step.status === 'selesai') dotColor = 'bg-success';
                else if (step.status === 'proses') dotColor = 'bg-[#99620a]';
                else if (step.status === 'tertunda') dotColor = 'bg-error';

                el.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full ${dotColor}"></span>
                        <span class="font-medium text-primary">${step.tahap}</span>
                    </div>
                    <span class="text-on-surface-variant text-[11px]">${step.waktu}</span>
                `;
                timelineContainer.appendChild(el);
            });
        }

        modalDetail.classList.remove('hidden');
    }

    function closeDetailLaporan() {
        modalDetail.classList.add('hidden');
    }

    modalDetail.addEventListener('click', function(e) {
        if (e.target === modalDetail) {
            closeDetailLaporan();
        }
    });
</script>
@endpush
@endsection
