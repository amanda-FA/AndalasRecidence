@extends('layouts.app')

@section('title', 'Laporan Kerusakan - Teknisi Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary text-[22px]">build</span>
        <span class="font-label-md text-label-md font-semibold">Laporan Kerusakan</span>
    </div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto w-full flex flex-col gap-space-lg">

    {{-- Title & Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Laporan Kerusakan Fasilitas</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar penugasan perbaikan fasilitas hunian asrama.</p>
        </div>
        <button type="button" onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-space-md py-2 rounded-lg bg-surface-container-lowest text-primary shadow-sm hover:bg-surface-container-low transition-all font-label-md text-label-md cursor-pointer self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">sync</span>
            <span>Muat Ulang</span>
        </button>
    </div>

    {{-- 4 Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-space-md">
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Total Laporan Ditangani</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ $kpi['total_ditangani'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tiket</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Menunggu Penanganan</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-secondary font-bold">{{ $kpi['menunggu'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tiket</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Dalam Perbaikan Lapangan</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-[#99620a] font-bold">{{ $kpi['dalam_pengerjaan'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tiket</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <span class="font-caption-sm-medium text-caption-sm-medium text-on-surface-variant uppercase tracking-wider">Laporan Tertunda</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-error font-bold">{{ $kpi['tertunda'] }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">Tiket</span>
            </div>
        </div>
    </div>

    {{-- Main Table Container --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col">
        
        {{-- Navigation Tabs --}}
        <div class="px-space-md pt-2 border-b border-surface-container flex items-center gap-1 overflow-x-auto text-label-md">
            <button type="button" data-filter-tab="semua" class="tab-btn px-4 py-2.5 border-b-2 border-secondary text-secondary font-semibold cursor-pointer whitespace-nowrap">
                Semua Penugasan ({{ $reports->count() }})
            </button>
            <button type="button" data-filter-tab="tindakan_cepat" class="tab-btn px-4 py-2.5 border-b-2 border-transparent text-on-surface-variant hover:text-primary font-medium cursor-pointer whitespace-nowrap">
                Kebutuhan Tindakan Cepat
            </button>
            <button type="button" data-filter-tab="pengerjaan" class="tab-btn px-4 py-2.5 border-b-2 border-transparent text-on-surface-variant hover:text-primary font-medium cursor-pointer whitespace-nowrap">
                Dalam Pengerjaan
            </button>
            <button type="button" data-filter-tab="tertunda" class="tab-btn px-4 py-2.5 border-b-2 border-transparent text-on-surface-variant hover:text-primary font-medium cursor-pointer whitespace-nowrap">
                Tertunda
            </button>
            <button type="button" data-filter-tab="selesai" class="tab-btn px-4 py-2.5 border-b-2 border-transparent text-on-surface-variant hover:text-primary font-medium cursor-pointer whitespace-nowrap">
                Riwayat Selesai
            </button>
        </div>

        {{-- Filter Toolbar --}}
        <div class="p-space-md flex flex-col md:flex-row items-center justify-between gap-space-sm border-b border-surface-container">
            <div class="relative w-full md:max-w-xs">
                <span class="material-symbols-outlined text-[18px] text-outline absolute left-3 top-1/2 -translate-y-1/2">search</span>
                <input id="teknisi-search" type="search" placeholder="Cari kamar, nomor tiket..." class="w-full pl-9 pr-3 py-1.5 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline outline-none focus:ring-2 focus:ring-secondary/20">
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <select id="filter-gedung" class="px-3 py-1.5 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none cursor-pointer">
                    <option value="semua">Semua Gedung</option>
                    @foreach ($gedungs as $gedung)
                        <option value="{{ $gedung->nama_gedung }}">{{ $gedung->nama_gedung }}</option>
                    @endforeach
                </select>

                <select id="filter-urgensi" class="px-3 py-1.5 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none cursor-pointer">
                    <option value="semua">Semua Urgensi</option>
                    <option value="Darurat">Darurat</option>
                    <option value="Tinggi">Tinggi</option>
                    <option value="Sedang">Sedang</option>
                    <option value="Rendah">Rendah</option>
                </select>

                <select id="filter-status" class="px-3 py-1.5 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none cursor-pointer">
                    <option value="semua">Semua Status</option>
                    <option value="diajukan">Menunggu</option>
                    <option value="dalam_pengerjaan">Dalam Pengerjaan</option>
                    <option value="tertunda">Tertunda</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-body-md border-collapse">
                <thead class="bg-surface-container-low text-on-surface-variant text-[11px] uppercase font-semibold">
                    <tr>
                        <th class="py-2.5 px-space-md w-28">ID Tiket</th>
                        <th class="py-2.5 px-space-md w-28">Tgl Lapor</th>
                        <th class="py-2.5 px-space-md">Kerusakan &amp; Fasilitas</th>
                        <th class="py-2.5 px-space-md w-44">Lokasi Hunian</th>
                        <th class="py-2.5 px-space-md w-36">Pelapor</th>
                        <th class="py-2.5 px-space-md w-24">Tingkat Urgensi</th>
                        <th class="py-2.5 px-space-md w-32">Status Perbaikan</th>
                        <th class="py-2.5 px-space-md text-right w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody id="teknisi-report-rows" class="divide-y divide-surface-container">
                    @foreach ($reports as $item)
                        <tr class="report-row hover:bg-surface-container-low/30 transition-colors"
                            data-ticket="{{ strtolower($item['nomor_tiket']) }}"
                            data-gedung="{{ strtolower($item['gedung_nama']) }}"
                            data-kamar="{{ strtolower($item['kamar_nomor']) }}"
                            data-urgensi="{{ $item['urgensi'] }}"
                            data-status="{{ $item['status_perbaikan'] }}"
                            data-fasilitas="{{ strtolower($item['fasilitas_nama'] . ' ' . $item['deskripsi']) }}"
                            data-item='@json($item)'>

                            <td class="py-3 px-space-md font-mono text-[13px] font-semibold text-secondary">
                                {{ $item['nomor_tiket'] }}
                            </td>

                            <td class="py-3 px-space-md text-[13px] text-on-surface">
                                <div>{{ $item['tanggal_lapor'] }}</div>
                                <div class="text-[11px] text-outline">{{ $item['waktu_lapor'] }}</div>
                            </td>

                            <td class="py-3 px-space-md">
                                <div class="font-medium text-primary text-[13px]">{{ $item['fasilitas_nama'] }}</div>
                                <div class="text-[12px] text-on-surface-variant line-clamp-1">{{ $item['deskripsi'] }}</div>
                            </td>

                            <td class="py-3 px-space-md text-[13px]">
                                <div class="text-primary font-medium">{{ $item['gedung_nama'] }}</div>
                                <div class="text-[11px] text-outline">{{ $item['kamar_nomor'] }} &middot; Lt. {{ $item['lantai'] }}</div>
                            </td>

                            <td class="py-3 px-space-md text-[13px]">
                                <div class="text-primary">{{ $item['pelapor_nama'] }}</div>
                                <div class="text-[11px] text-outline">{{ $item['pelapor_telepon'] }}</div>
                            </td>

                            <td class="py-3 px-space-md">
                                @if ($item['urgensi'] === 'Darurat')
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-error-container text-error">Darurat</span>
                                @elseif ($item['urgensi'] === 'Tinggi')
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-[#ffe4cc] text-[#c25e00]">Tinggi</span>
                                @elseif ($item['urgensi'] === 'Sedang')
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-[#fff3df] text-[#99620a]">Sedang</span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-surface-container text-on-surface-variant">Rendah</span>
                                @endif
                            </td>

                            <td class="py-3 px-space-md">
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium {{ $item['status_badge']['class'] }}">
                                    {{ $item['status_badge']['label'] }}
                                </span>
                            </td>

                            <td class="py-3 px-space-md text-right">
                                <button type="button" onclick="openDetailModal(this)" class="px-2.5 py-1 rounded bg-surface-container text-primary hover:bg-secondary hover:text-on-secondary text-[12px] font-medium transition-colors cursor-pointer">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Empty State --}}
        <div id="teknisi-empty-state" hidden class="text-center py-8 text-on-surface-variant text-caption-sm">
            Tidak ada laporan kerusakan yang cocok dengan filter pencarian.
        </div>

        {{-- Table Footer Summary & SLA Note --}}
        <div class="px-space-md py-2.5 border-t border-surface-container flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-on-surface-variant gap-2 bg-surface-container-lowest">
            <span>Menampilkan <strong id="visible-count" class="text-primary">{{ $reports->count() }}</strong> laporan</span>
            <div class="text-outline">
                <span class="font-medium text-on-surface-variant">Standar Operasional Respon Teknisi (SLA):</span>
                <span>Darurat &lt; 4 Jam</span> &middot;
                <span>Tinggi &lt; 24 Jam</span> &middot;
                <span>Sedang &lt; 48 Jam (2 Hari)</span> &middot;
                <span>Rendah &lt; 72 Jam (3 Hari)</span>
            </div>
        </div>
    </div>

    {{-- Dialog Modal: Detail Laporan Kerusakan --}}
    <dialog id="teknisi-detail-dialog" class="w-[min(640px,calc(100%-2rem))] m-auto rounded-xl p-0 border-0 bg-white shadow-xl overflow-hidden backdrop:bg-primary-container/40">
        <div class="flex flex-col max-h-[85vh]">
            <div class="px-space-md py-3 border-b border-surface-container flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-2">
                    <span id="modal-ticket-id" class="font-mono text-label-md font-bold text-secondary"></span>
                    <span id="modal-status-badge" class="px-2 py-0.5 rounded text-[11px] font-medium"></span>
                </div>
                <button type="button" onclick="document.getElementById('teknisi-detail-dialog').close()" class="p-1 rounded text-on-surface-variant hover:bg-surface-container cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="p-space-md overflow-y-auto space-y-3 text-caption-sm">
                <div class="grid grid-cols-2 gap-2 p-3 bg-surface-container-low rounded-lg">
                    <div>
                        <span class="text-outline block">Lokasi:</span>
                        <strong id="modal-lokasi" class="text-primary block text-[13px]"></strong>
                    </div>
                    <div>
                        <span class="text-outline block">Pelapor:</span>
                        <strong id="modal-pelapor" class="text-primary block text-[13px]"></strong>
                    </div>
                    <div>
                        <span class="text-outline block">Fasilitas:</span>
                        <span id="modal-fasilitas" class="font-medium text-primary"></span>
                    </div>
                    <div>
                        <span class="text-outline block">Target SLA:</span>
                        <span id="modal-sla" class="font-medium text-primary"></span>
                    </div>
                </div>

                <div>
                    <span class="font-medium text-primary block mb-1">Deskripsi Kerusakan:</span>
                    <p id="modal-deskripsi" class="p-2.5 bg-surface-container-low rounded text-on-surface leading-relaxed"></p>
                </div>

                <div id="modal-photo-box" hidden>
                    <span class="font-medium text-primary block mb-1">Foto Kondisi:</span>
                    <img id="modal-photo-img" src="" alt="Dokumentasi Kerusakan" class="max-h-48 rounded object-cover border border-surface-container">
                </div>

                <div>
                    <span class="font-medium text-primary block mb-1">Catatan Pengerjaan:</span>
                    <p id="modal-catatan" class="text-on-surface-variant p-2 bg-surface-container-low rounded"></p>
                </div>
            </div>

            <div class="px-space-md py-2.5 border-t border-surface-container flex items-center justify-between bg-surface-container-lowest">
                <button type="button" onclick="document.getElementById('teknisi-detail-dialog').close()" class="px-3 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface text-caption-sm cursor-pointer">
                    Tutup
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" id="modal-btn-cetak" class="px-3 py-1.5 rounded border border-outline-variant hover:bg-surface-container-low text-primary text-caption-sm font-medium cursor-pointer">
                        Cetak Surat Tugas PK (.PDF)
                    </button>
                    <button type="button" id="modal-btn-ubah-status" class="px-3.5 py-1.5 rounded bg-secondary hover:bg-on-secondary-fixed-variant text-on-secondary text-caption-sm font-medium cursor-pointer">
                        Ubah Status Perbaikan
                    </button>
                </div>
            </div>
        </div>
    </dialog>

</div>

@push('scripts')
<script>
    const searchInput = document.getElementById('teknisi-search');
    const filterGedung = document.getElementById('filter-gedung');
    const filterUrgensi = document.getElementById('filter-urgensi');
    const filterStatus = document.getElementById('filter-status');
    const tabButtons = document.querySelectorAll('.tab-btn');
    const rows = document.querySelectorAll('.report-row');
    const emptyState = document.getElementById('teknisi-empty-state');
    const visibleCountSpan = document.getElementById('visible-count');

    let activeTab = 'semua';

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            tabButtons.forEach(b => {
                b.classList.remove('border-secondary', 'text-secondary', 'font-semibold');
                b.classList.add('border-transparent', 'text-on-surface-variant', 'font-medium');
            });
            btn.classList.add('border-secondary', 'text-secondary', 'font-semibold');
            btn.classList.remove('border-transparent', 'text-on-surface-variant', 'font-medium');

            activeTab = btn.dataset.filterTab;
            applyFilters();
        });
    });

    [searchInput, filterGedung, filterUrgensi, filterStatus].forEach(el => {
        el.addEventListener('input', applyFilters);
        el.addEventListener('change', applyFilters);
    });

    function applyFilters() {
        const query = searchInput.value.trim().toLowerCase();
        const selectedGedung = filterGedung.value.toLowerCase();
        const selectedUrgensi = filterUrgensi.value;
        const selectedStatus = filterStatus.value.toLowerCase();

        let visibleCount = 0;

        rows.forEach(row => {
            const ticket = row.dataset.ticket;
            const gedung = row.dataset.gedung;
            const kamar = row.dataset.kamar;
            const urgensi = row.dataset.urgensi;
            const status = row.dataset.status;
            const fasilitas = row.dataset.fasilitas;

            let matchTab = true;
            if (activeTab === 'tindakan_cepat') {
                matchTab = (urgensi === 'Darurat' || urgensi === 'Tinggi') && status !== 'selesai';
            } else if (activeTab === 'pengerjaan') {
                matchTab = status === 'dalam_pengerjaan' || status === 'diproses';
            } else if (activeTab === 'tertunda') {
                matchTab = status === 'tertunda';
            } else if (activeTab === 'selesai') {
                matchTab = status === 'selesai';
            }

            const matchSearch = !query || ticket.includes(query) || kamar.includes(query) || fasilitas.includes(query) || gedung.includes(query);
            const matchGedung = selectedGedung === 'semua' || gedung.includes(selectedGedung);
            const matchUrgensi = selectedUrgensi === 'semua' || urgensi === selectedUrgensi;
            const matchStatus = selectedStatus === 'semua' || status === selectedStatus;

            if (matchTab && matchSearch && matchGedung && matchUrgensi && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        visibleCountSpan.textContent = visibleCount;
        emptyState.hidden = (visibleCount > 0);
    }

    const detailDialog = document.getElementById('teknisi-detail-dialog');

    function openDetailModal(btn) {
        const row = btn.closest('.report-row');
        if (!row) return;

        const item = JSON.parse(row.dataset.item);

        document.getElementById('modal-ticket-id').textContent = item.nomor_tiket;
        
        const statusBadge = document.getElementById('modal-status-badge');
        statusBadge.textContent = item.status_badge.label;
        statusBadge.className = 'px-2 py-0.5 rounded text-[11px] font-medium ' + item.status_badge.class;

        document.getElementById('modal-lokasi').textContent = item.lokasi_lengkap;
        document.getElementById('modal-pelapor').textContent = item.pelapor_nama + ' (' + item.pelapor_telepon + ')';
        document.getElementById('modal-fasilitas').textContent = item.fasilitas_nama;
        document.getElementById('modal-sla').textContent = item.sla_target;

        document.getElementById('modal-deskripsi').textContent = item.deskripsi;

        const photoBox = document.getElementById('modal-photo-box');
        const photoImg = document.getElementById('modal-photo-img');
        if (item.foto) {
            photoBox.hidden = false;
            photoImg.src = item.foto;
        } else {
            photoBox.hidden = true;
        }

        document.getElementById('modal-catatan').textContent = item.catatan_teknisi || 'Belum ada catatan pekerjaan.';

        document.getElementById('modal-btn-cetak').onclick = function() {
            window.open(`/teknisi/laporan-kerusakan/${item.id_laporan}/cetak-pk`, '_blank');
        };

        document.getElementById('modal-btn-ubah-status').onclick = function() {
            window.location.href = `/teknisi/laporan-kerusakan/${item.id_laporan}/ubah-status`;
        };

        detailDialog.showModal();
    }
</script>
@endpush
@endsection
