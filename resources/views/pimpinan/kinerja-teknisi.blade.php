@extends('layouts.app')

@section('title', 'Kinerja Teknisi - Pimpinan Andalas Residence')

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
        <span class="material-symbols-outlined text-secondary text-[22px]">engineering</span>
        <span class="font-label-md text-label-md font-semibold">Monitoring Kinerja Teknisi</span>
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
            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Monitoring Kinerja Teknisi</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Evaluasi produktivitas, kepatuhan batas waktu respon (SLA), dan beban kerja tim teknisi.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-space-md py-2 rounded-lg bg-surface-container-lowest text-primary shadow-sm hover:bg-surface-container-low transition-all font-label-md text-label-md cursor-pointer border border-outline-variant/10">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak Rekap</span>
            </button>
        </div>
    </div>

    {{-- 4 Key Performance Metrics --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-space-md">
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Total Teknisi Aktif</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ $kpi['total_teknisi'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Personil</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Tiket Diselesaikan</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-success font-bold">{{ $kpi['total_diselesaikan'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Pekerjaan</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Kepatuhan SLA Rata-rata</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-secondary font-bold">{{ $kpi['rata_rata_sla'] }}%</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tepat Waktu</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Rata-rata Waktu Tanggap</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ $kpi['rata_rata_waktu'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">per Tiket</span>
            </div>
        </div>
    </div>

    {{-- Tabel Kinerja per Teknisi --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/10 flex flex-col">
        <div class="p-space-md border-b border-surface-container">
            <h2 class="font-title-md text-title-md text-primary font-semibold">Tabel Kinerja Tim Teknisi</h2>
            <p class="text-caption-sm text-on-surface-variant mt-0.5">Ringkasan penugasan dan evaluasi per personil teknisi asrama.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-body-md border-collapse">
                <thead class="bg-surface-container-low text-on-surface-variant text-[11px] uppercase font-semibold">
                    <tr>
                        <th class="py-2.5 px-space-md">Nama Teknisi</th>
                        <th class="py-2.5 px-space-md">Bidang Spesialisasi</th>
                        <th class="py-2.5 px-space-md text-center w-24">Total Tiket</th>
                        <th class="py-2.5 px-space-md text-center w-28">Selesai / Proses</th>
                        <th class="py-2.5 px-space-md text-center w-28">Kepatuhan SLA</th>
                        <th class="py-2.5 px-space-md text-center w-28">Rata-rata Waktu</th>
                        <th class="py-2.5 px-space-md text-center w-32">Status</th>
                        <th class="py-2.5 px-space-md text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @foreach ($teknisiList as $t)
                        <tr class="hover:bg-surface-container-low/30 transition-colors">
                            <td class="py-3 px-space-md">
                                <strong class="text-primary text-[13px] block">{{ $t['nama_lengkap'] }}</strong>
                                <span class="text-[11px] text-outline">{{ $t['kontak'] }}</span>
                            </td>
                            <td class="py-3 px-space-md text-caption-sm text-on-surface-variant">
                                {{ $t['spesialisasi'] }}
                            </td>
                            <td class="py-3 px-space-md text-center font-semibold text-primary text-[13px]">
                                {{ $t['total_tiket'] }}
                            </td>
                            <td class="py-3 px-space-md text-center text-caption-sm">
                                <span class="text-success font-semibold">{{ $t['selesai'] }}</span>
                                <span class="text-outline">/</span>
                                <span class="text-[#99620a] font-semibold">{{ $t['dalam_proses'] }}</span>
                            </td>
                            <td class="py-3 px-space-md text-center">
                                <span class="text-[13px] font-semibold text-secondary">{{ $t['kepatuhan_sla'] }}%</span>
                            </td>
                            <td class="py-3 px-space-md text-center text-caption-sm text-on-surface">
                                {{ $t['rata_waktu'] }}
                            </td>
                            <td class="py-3 px-space-md text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-medium {{ $t['status_class'] }}">
                                    {{ $t['status_tugas'] }}
                                </span>
                            </td>
                            <td class="py-3 px-space-md text-center">
                                <button type="button" 
                                    onclick='openTeknisiModal(@json($t))'
                                    class="px-2.5 py-1 text-[12px] font-medium text-primary bg-surface-container hover:bg-surface-container-high rounded transition-colors cursor-pointer">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Log Riwayat Pekerjaan Terkini --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/10 flex flex-col">
        <div class="p-space-md border-b border-surface-container">
            <h2 class="font-title-md text-title-md text-primary font-semibold">Riwayat Pekerjaan Selesai Terkini</h2>
            <p class="text-caption-sm text-on-surface-variant mt-0.5">Log audit penyelesaian pekerjaan fasilitas oleh teknisi.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-body-md border-collapse">
                <thead class="bg-surface-container-low text-on-surface-variant text-[11px] uppercase font-semibold">
                    <tr>
                        <th class="py-2.5 px-space-md w-28">No. Tiket</th>
                        <th class="py-2.5 px-space-md w-44">Teknisi</th>
                        <th class="py-2.5 px-space-md">Fasilitas &amp; Lokasi</th>
                        <th class="py-2.5 px-space-md w-32">Tgl Selesai</th>
                        <th class="py-2.5 px-space-md w-28 text-center">Durasi</th>
                        <th class="py-2.5 px-space-md text-right w-32">Kesesuaian SLA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @foreach ($riwayatPekerjaan as $log)
                        <tr class="hover:bg-surface-container-low/30 transition-colors">
                            <td class="py-3 px-space-md font-mono text-[13px] font-semibold text-secondary">
                                {{ $log['nomor_tiket'] }}
                            </td>
                            <td class="py-3 px-space-md text-[13px] font-medium text-primary">
                                {{ $log['teknisi_nama'] }}
                            </td>
                            <td class="py-3 px-space-md text-[13px]">
                                <div class="font-medium text-primary">{{ $log['fasilitas'] }}</div>
                                <div class="text-[11px] text-outline">{{ $log['lokasi'] }}</div>
                            </td>
                            <td class="py-3 px-space-md text-caption-sm text-on-surface">
                                {{ $log['tanggal_selesai'] }}
                            </td>
                            <td class="py-3 px-space-md text-center text-caption-sm text-on-surface">
                                {{ $log['durasi'] }}
                            </td>
                            <td class="py-3 px-space-md text-right">
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-success-container text-success">
                                    {{ $log['status_sla'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
        <h1 class="text-[13pt] font-bold uppercase underline tracking-wide">REKAPITULASI EVALUASI KINERJA TIM TEKNISI ASRAMA</h1>
        <p class="text-[9.5pt] text-gray-800 mt-1">Nomor Dokumen: AR/EVAL-TEKNISI/{{ date('Y/m') }}/01 &bull; Tanggal Cetak: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
    </div>

    {{-- I. Ringkasan Metrik Tim --}}
    <div class="mb-4">
        <h5 class="text-[10pt] font-bold uppercase mb-1.5">I. RINGKASAN PRODUKTIVITAS TIM TEKNISI</h5>
        <table class="print-table">
            <thead>
                <tr>
                    <th>Total Personil Teknisi</th>
                    <th>Pekerjaan Diselesaikan</th>
                    <th>Rata-rata Kepatuhan SLA (%)</th>
                    <th>Rata-rata Waktu Tanggap</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center font-medium">
                    <td>{{ $kpi['total_teknisi'] }} Personil</td>
                    <td>{{ $kpi['total_diselesaikan'] }} Pekerjaan</td>
                    <td><strong>{{ $kpi['rata_rata_sla'] }}% (Tepat Waktu)</strong></td>
                    <td>{{ $kpi['rata_rata_waktu'] }} / Tiket</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- II. Tabel Kinerja per Personil Teknisi --}}
    <div class="mb-4">
        <h5 class="text-[10pt] font-bold uppercase mb-1.5">II. EVALUASI KINERJA PER PERSONIL TEKNISI</h5>
        <table class="print-table">
            <thead>
                <tr>
                    <th class="w-8">No</th>
                    <th>Nama Teknisi</th>
                    <th>Bidang Spesialisasi</th>
                    <th class="w-20">Total Tiket</th>
                    <th class="w-20">Selesai</th>
                    <th class="w-20">Proses</th>
                    <th class="w-24">Kepatuhan SLA</th>
                    <th class="w-24">Rata Waktu</th>
                    <th class="w-28">Status Tugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teknisiList as $index => $t)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $t['nama_lengkap'] }}</strong><br>
                            <span class="text-[8.5pt] text-gray-700">{{ $t['kontak'] }}</span>
                        </td>
                        <td>{{ $t['spesialisasi'] }}</td>
                        <td class="text-center font-semibold">{{ $t['total_tiket'] }}</td>
                        <td class="text-center font-semibold">{{ $t['selesai'] }}</td>
                        <td class="text-center">{{ $t['dalam_proses'] }}</td>
                        <td class="text-center font-semibold">{{ $t['kepatuhan_sla'] }}%</td>
                        <td class="text-center">{{ $t['rata_waktu'] }}</td>
                        <td class="text-center text-[9pt]">{{ $t['status_tugas'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- III. Log Riwayat Pekerjaan Selesai --}}
    <div class="mb-6">
        <h5 class="text-[10pt] font-bold uppercase mb-1.5">III. LOG PENYELESAIAN PEKERJAAN TERAKHIR</h5>
        <table class="print-table">
            <thead>
                <tr>
                    <th class="w-7">No</th>
                    <th class="w-24">No. Tiket</th>
                    <th class="w-36">Teknisi Bertugas</th>
                    <th>Fasilitas &amp; Lokasi</th>
                    <th class="w-24">Tgl Selesai</th>
                    <th class="w-20">Durasi</th>
                    <th class="w-24">Kesesuaian SLA</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($riwayatPekerjaan as $index => $log)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center font-mono font-semibold text-[9pt]">{{ $log['nomor_tiket'] }}</td>
                        <td>{{ $log['teknisi_nama'] }}</td>
                        <td>
                            <strong>{{ $log['fasilitas'] }}</strong><br>
                            <span class="text-[8.5pt] text-gray-700">{{ $log['lokasi'] }}</span>
                        </td>
                        <td class="text-center text-[9pt]">{{ $log['tanggal_selesai'] }}</td>
                        <td class="text-center">{{ $log['durasi'] }}</td>
                        <td class="text-center font-semibold">{{ $log['status_sla'] }}</td>
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

{{-- Modal Detail Kinerja Teknisi (Read-Only Pimpinan) --}}
<div id="modal-detail-teknisi" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs print:hidden">
    <div class="bg-surface-container-lowest rounded-xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-xl border border-outline-variant/10 overflow-hidden">
        
        {{-- Header Modal --}}
        <div class="p-space-md border-b border-surface-container flex items-center justify-between bg-surface-container-low/40">
            <div>
                <h3 id="modal-teknisi-nama" class="font-title-md font-bold text-primary">Nama Teknisi</h3>
                <span id="modal-teknisi-spesialisasi" class="text-[12px] text-on-surface-variant">Bidang Spesialisasi</span>
            </div>
            <button type="button" onclick="closeTeknisiModal()" class="p-1 text-on-surface-variant hover:text-primary rounded-lg transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Body Modal (Scrollable) --}}
        <div class="p-space-md overflow-y-auto flex flex-col gap-space-md text-[13px]">
            {{-- Profil & Wilayah Tugas --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-surface-container-low/40 p-3 rounded-lg">
                <div>
                    <span class="text-caption-sm text-on-surface-variant block">Kontak / Telepon</span>
                    <strong id="modal-teknisi-kontak" class="text-primary block">-</strong>
                </div>
                <div>
                    <span class="text-caption-sm text-on-surface-variant block">Wilayah Tugas</span>
                    <strong id="modal-teknisi-wilayah" class="text-primary block">-</strong>
                </div>
                <div>
                    <span class="text-caption-sm text-on-surface-variant block">Status Beban Kerja</span>
                    <span id="modal-teknisi-status" class="inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-medium">-</span>
                </div>
            </div>

            {{-- Metrik Ringkas Kinerja --}}
            <div class="grid grid-cols-4 gap-2 text-center">
                <div class="p-2.5 bg-surface-container-low/30 rounded border border-surface-container/60">
                    <span class="text-[11px] text-on-surface-variant block uppercase">Total</span>
                    <strong id="modal-teknisi-total" class="text-[16px] text-primary block mt-0.5">-</strong>
                </div>
                <div class="p-2.5 bg-surface-container-low/30 rounded border border-surface-container/60">
                    <span class="text-[11px] text-on-surface-variant block uppercase">Selesai</span>
                    <strong id="modal-teknisi-selesai" class="text-[16px] text-success block mt-0.5">-</strong>
                </div>
                <div class="p-2.5 bg-surface-container-low/30 rounded border border-surface-container/60">
                    <span class="text-[11px] text-on-surface-variant block uppercase">Kepatuhan SLA</span>
                    <strong id="modal-teknisi-sla" class="text-[16px] text-secondary block mt-0.5">-</strong>
                </div>
                <div class="p-2.5 bg-surface-container-low/30 rounded border border-surface-container/60">
                    <span class="text-[11px] text-on-surface-variant block uppercase">Rata Durasi</span>
                    <strong id="modal-teknisi-waktu" class="text-[16px] text-primary block mt-0.5">-</strong>
                </div>
            </div>

            {{-- Riwayat Tiket yang Ditangani --}}
            <div>
                <span class="text-caption-sm text-on-surface-variant block font-medium mb-2">Riwayat Tiket yang Ditangani Personil Ini</span>
                <div class="overflow-x-auto border border-surface-container rounded-lg">
                    <table class="w-full text-left text-[12px] border-collapse">
                        <thead class="bg-surface-container-low text-on-surface-variant text-[11px] uppercase">
                            <tr>
                                <th class="py-2 px-3">Tiket</th>
                                <th class="py-2 px-3">Kerusakan &amp; Lokasi</th>
                                <th class="py-2 px-3">Tgl Lapor / Selesai</th>
                                <th class="py-2 px-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody id="modal-teknisi-tbody" class="divide-y divide-surface-container">
                            {{-- Dinamis diisi JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Notice Read-Only --}}
            <div class="p-2.5 bg-surface-container-low rounded text-[11px] text-on-surface-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-secondary">visibility</span>
                <span>Akses Mode Baca: Pimpinan mengevaluasi produktivitas personil tanpa mengubah penugasan kerja.</span>
            </div>
        </div>

        {{-- Footer Modal --}}
        <div class="p-3 border-t border-surface-container flex items-center justify-end bg-surface-container-low/40">
            <button type="button" onclick="closeTeknisiModal()" class="px-4 py-1.5 rounded-lg bg-surface-container-high text-primary hover:bg-surface-container-highest transition-colors text-[12px] font-medium cursor-pointer">
                Tutup
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
    const modalTeknisi = document.getElementById('modal-detail-teknisi');

    function openTeknisiModal(t) {
        document.getElementById('modal-teknisi-nama').textContent = t.nama_lengkap;
        document.getElementById('modal-teknisi-spesialisasi').textContent = t.spesialisasi;
        document.getElementById('modal-teknisi-kontak').textContent = t.kontak || '-';
        document.getElementById('modal-teknisi-wilayah').textContent = t.wilayah_tugas || 'Seluruh Area Asrama';

        const st = document.getElementById('modal-teknisi-status');
        st.textContent = t.status_tugas;
        st.className = 'inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-medium ' + t.status_class;

        document.getElementById('modal-teknisi-total').textContent = t.total_tiket;
        document.getElementById('modal-teknisi-selesai').textContent = t.selesai;
        document.getElementById('modal-teknisi-sla').textContent = t.kepatuhan_sla + '%';
        document.getElementById('modal-teknisi-waktu').textContent = t.rata_waktu;

        // Render riwayat tiket
        const tbody = document.getElementById('modal-teknisi-tbody');
        tbody.innerHTML = '';
        if (t.riwayat_tiket && Array.isArray(t.riwayat_tiket) && t.riwayat_tiket.length > 0) {
            t.riwayat_tiket.forEach(r => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-surface-container-low/30';
                tr.innerHTML = `
                    <td class="py-2.5 px-3 font-mono font-semibold text-secondary">${r.nomor_tiket}</td>
                    <td class="py-2.5 px-3">
                        <div class="font-medium text-primary">${r.jenis_kerusakan}</div>
                        <div class="text-[11px] text-outline">${r.lokasi}</div>
                    </td>
                    <td class="py-2.5 px-3 text-[11px] text-on-surface-variant">
                        <div>Lapor: ${r.tanggal_lapor}</div>
                        <div>Selesai: ${r.tanggal_selesai}</div>
                    </td>
                    <td class="py-2.5 px-3 text-right">
                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium ${r.status_badge}">
                            ${r.status}
                        </span>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="4" class="py-3 px-3 text-center text-outline">Belum ada riwayat tiket tercatat.</td>';
            tbody.appendChild(tr);
        }

        modalTeknisi.classList.remove('hidden');
    }

    function closeTeknisiModal() {
        modalTeknisi.classList.add('hidden');
    }

    modalTeknisi.addEventListener('click', function(e) {
        if (e.target === modalTeknisi) {
            closeTeknisiModal();
        }
    });
</script>
@endpush
@endsection
