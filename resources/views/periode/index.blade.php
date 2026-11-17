@extends('layouts.app')

@section('title', 'Periode Pendaftaran - Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary">
            calendar_month
        </span>
        <span class="font-label-md text-label-md">
            Layanan Pendaftaran Hunian
        </span>
    </div>
@endsection

@push('styles')
<style>
    #periode-dialog {
        width: min(720px, calc(100% - 2rem));
        max-height: 90vh;
        margin: auto;
        border: none;
        padding: 0;
        overflow-y: auto;
    }

    #periode-dialog::backdrop {
        background: rgba(18, 32, 86, 0.5);
        backdrop-filter: blur(4px);
    }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-space-xl">
    {{-- Header --}}
    <section class="bg-surface-container-lowest rounded-2xl p-space-lg lg:p-space-xl shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-space-lg">
            <div>
                <div class="flex items-center gap-space-xs text-secondary mb-space-sm">
                    <span class="material-symbols-outlined text-[18px]">
                        calendar_month
                    </span>
                    <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider">
                        Portal Admin Layanan
                    </span>
                </div>

                <h1 class="font-headline-lg text-headline-lg text-on-surface">
                    Kelola Periode Pendaftaran Hunian
                </h1>

                <p class="mt-space-sm text-on-surface-variant max-w-2xl">
                    Kelola jadwal penerimaan, pantau jumlah pendaftar,
                    dan atur status pembukaan pendaftaran hunian.
                </p>
            </div>

            <button type="button"
                    id="tambah-periode"
                    class="flex items-center justify-center gap-space-xs px-space-lg py-3 bg-secondary text-on-secondary rounded-xl font-label-md text-label-md shadow-sm hover:bg-secondary/90 whitespace-nowrap cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">
                    add_circle
                </span>
                Tambah Periode
            </button>
        </div>
    </section>

    {{-- Ringkasan --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-space-lg"
             aria-label="Ringkasan periode">
        @php
            $cards = [
                [
                    'label' => 'Total Periode',
                    'nilai' => $ringkasan['total'],
                    'ikon' => 'calendar_month',
                    'keterangan' => 'Seluruh periode yang tersimpan',
                ],
                [
                    'label' => 'Pendaftaran Dibuka',
                    'nilai' => $ringkasan['aktif'],
                    'ikon' => 'event_available',
                    'keterangan' => 'Status dibuka dan dalam rentang tanggal',
                ],
                [
                    'label' => 'Total Pendaftar',
                    'nilai' => $ringkasan['pendaftar'],
                    'ikon' => 'group',
                    'keterangan' => 'Akumulasi pendaftaran seluruh periode',
                ],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm">
                <div class="flex items-center justify-between gap-space-sm">
                    <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider text-on-surface-variant">
                        {{ $card['label'] }}
                    </span>
                    <span class="w-11 h-11 rounded-xl bg-surface-container-low text-secondary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined">
                            {{ $card['ikon'] }}
                        </span>
                    </span>
                </div>

                <div class="font-headline-xl text-headline-xl text-primary mt-space-sm">
                    {{ number_format($card['nilai'], 0, ',', '.') }}
                </div>

                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-sm">
                    {{ $card['keterangan'] }}
                </p>
            </div>
        @endforeach
    </section>

    {{-- Banner dan lokasi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-space-lg">
        <section class="lg:col-span-2 relative overflow-hidden rounded-2xl bg-primary-container text-on-primary shadow-sm">
            <div class="flex flex-col md:flex-row h-full">
                <div class="flex-1 p-space-lg lg:p-space-xl">
                    <div class="flex items-center gap-space-xs text-secondary-fixed-dim mb-space-sm">
                        <span class="material-symbols-outlined text-[18px]">
                            apartment
                        </span>
                        <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider">
                            Andalas Residence
                        </span>
                    </div>

                    <h2 class="font-headline-md text-headline-md">
                        Penerimaan Penghuni Asrama
                    </h2>

                    <p class="mt-space-sm text-secondary-fixed-dim font-body-md text-body-md">
                        Kelola pembukaan periode pendaftaran hunian
                        melalui portal Andalas Residence.
                    </p>

                    <div class="flex flex-wrap gap-space-sm mt-space-lg">
                        <span class="inline-flex items-center gap-space-xs rounded-lg bg-primary/30 px-3 py-2 font-caption-sm text-caption-sm">
                            <span class="material-symbols-outlined text-[18px]">
                                calendar_month
                            </span>
                            Jadwal Pendaftaran
                        </span>

                        <span class="inline-flex items-center gap-space-xs rounded-lg bg-primary/30 px-3 py-2 font-caption-sm text-caption-sm">
                            <span class="material-symbols-outlined text-[18px]">
                                groups
                            </span>
                            Informasi Pendaftar
                        </span>
                    </div>
                </div>

                <div class="md:w-2/5 min-h-48">
                    <img src="{{ asset('images/asramaunand.png') }}"
                         alt="Pemandangan kawasan asrama"
                         class="w-full h-56 md:h-full object-cover"
                         loading="lazy">
                </div>
            </div>
        </section>

        <section class="flex flex-col rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm">
            <div class="flex items-center justify-between gap-space-sm mb-space-md">
                <h2 class="font-title-md text-title-md text-on-surface">
                    Ringkasan Lokasi
                </h2>
                <span class="w-9 h-9 rounded-xl bg-surface-container-low text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">
                        location_on
                    </span>
                </span>
            </div>

            <img src="{{ asset('images/asramaunand.png') }}"
                 alt="Foto kawasan asrama"
                 class="w-full h-36 object-cover rounded-xl"
                 loading="lazy">

            <div class="mt-space-md">
                <p class="font-label-md text-label-md text-on-surface">
                    Kawasan Asrama Universitas Andalas
                </p>
                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                    Kampus Limau Manis, Padang
                </p>
            </div>

            <a href="https://www.bing.com/maps/search?name=Asrama+Bright+UNAND&amp;mepi=0%7E%7EEmbedded%7ELargeMapLink&amp;FORM=MPSRPL&amp;style=r&amp;ss=id.ypid%3AB2B2538D1F239573&amp;q=Asrama+Hijau+Universitas+Andalas&amp;ppois=-0.9335286617279053_100.43058776855469_Asrama+Bright+UNAND&amp;cp=-0.917797%7E100.454987&amp;lvl=18.6"
               target="_blank"
               rel="noopener noreferrer"
               class="flex items-center justify-center gap-space-xs mt-space-lg rounded-xl bg-surface-container-low text-secondary px-space-md py-3 font-label-md text-label-md hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[20px]">
                    location_on
                </span>
                Lihat Lokasi Asrama
                <span class="material-symbols-outlined text-[16px]">
                    open_in_new
                </span>
            </a>
        </section>
    </div>

    {{-- Informasi --}}
    <section class="bg-primary-container text-on-primary rounded-2xl p-space-lg shadow-sm">
        <div class="flex items-start gap-space-md">
            <span class="material-symbols-outlined text-secondary-fixed-dim text-[28px]">
                info
            </span>
            <div>
                <h2 class="font-title-md text-title-md">
                    Atur jadwal pembukaan periode
                </h2>
                <p class="text-secondary-fixed-dim mt-1">
                    Label Dibuka muncul ketika status periode dibuka dan
                    tanggal hari ini berada dalam rentang pendaftaran.
                    Periode bertanggal mendatang berlabel Terjadwal.
                </p>
            </div>
        </div>
    </section>

    {{-- Daftar periode --}}
    <section class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
        <div class="p-space-lg">
            <h2 class="font-headline-sm text-headline-sm">
                Daftar Periode Pendaftaran
            </h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                Cari dan kelola periode pendaftaran hunian.
            </p>
        </div>

        {{-- Pencarian dan filter --}}
        <form action="{{ route('periode.index') }}"
              method="GET"
              class="grid grid-cols-1 md:grid-cols-4 gap-space-sm px-space-lg pb-space-lg">
            <div>
                <label for="cari-periode" class="sr-only">
                    Cari nama periode
                </label>
                <input id="cari-periode"
                       type="search"
                       name="q"
                       maxlength="100"
                       value="{{ request('q') }}"
                       placeholder="Cari nama periode..."
                       class="w-full bg-surface-container-low rounded-xl p-3 outline-none focus:ring-2 focus:ring-secondary">
            </div>

            <div>
                <label for="filter-tahun" class="sr-only">
                    Tahun akademik
                </label>
                <select id="filter-tahun"
                        name="tahun"
                        class="w-full bg-surface-container-low rounded-xl p-3 outline-none focus:ring-2 focus:ring-secondary">
                    <option value="">Semua Tahun Akademik</option>
                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}"
                                @selected(request('tahun') === $tahun)>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter-akses" class="sr-only">
                    Akses pendaftaran
                </label>
                <select id="filter-akses"
                        name="akses"
                        class="w-full bg-surface-container-low rounded-xl p-3 outline-none focus:ring-2 focus:ring-secondary">
                    <option value="">Semua Akses Pendaftaran</option>
                    @foreach ([
                        'dibuka' => 'Dibuka',
                        'terjadwal' => 'Terjadwal',
                        'ditutup' => 'Ditutup',
                        'berakhir' => 'Berakhir',
                    ] as $nilai => $label)
                        <option value="{{ $nilai }}"
                                @selected(request('akses') === $nilai)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-space-sm">
                <button type="submit"
                        class="flex-1 rounded-xl bg-secondary text-on-secondary px-4 py-3 font-label-md text-label-md cursor-pointer">
                    Cari
                </button>
                <a href="{{ route('periode.index') }}"
                   class="rounded-xl bg-surface-container-low text-secondary px-4 py-3">
                    Reset
                </a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-on-surface-variant font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="p-space-md">Nama Periode</th>
                        <th scope="col" class="p-space-md">Tahun Akademik</th>
                        <th scope="col" class="p-space-md">Tanggal Buka</th>
                        <th scope="col" class="p-space-md">Tanggal Tutup</th>
                        <th scope="col" class="p-space-md">Pendaftar</th>
                        <th scope="col" class="p-space-md">Akses</th>
                        <th scope="col" class="p-space-md">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-surface-container">
                    @forelse ($periodes as $periode)
                        @php
                            if ($periode->status !== 'dibuka') {
                                $labelAkses = 'Ditutup';
                                $warnaAkses = 'bg-surface-container text-on-surface-variant';
                            } elseif (today()->lt($periode->tanggal_buka)) {
                                $labelAkses = 'Terjadwal';
                                $warnaAkses = 'bg-secondary-fixed text-on-secondary-fixed-variant';
                            } elseif (today()->gt($periode->tanggal_tutup)) {
                                $labelAkses = 'Berakhir';
                                $warnaAkses = 'bg-surface-container text-on-surface-variant';
                            } else {
                                $labelAkses = 'Dibuka';
                                $warnaAkses = 'bg-secondary text-on-secondary';
                            }

                            $bolehHapus = $periode->status === 'ditutup'
                                && (int) $periode->pendaftaran_hunians_count === 0;
                        @endphp

                        <tr class="hover:bg-surface-container-low/60">
                            <td class="p-space-md">
                                <div class="flex items-center gap-space-sm">
                                    <span class="w-9 h-9 rounded-lg bg-surface-container-low text-secondary flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">
                                            date_range
                                        </span>
                                    </span>
                                    <span class="font-label-md text-label-md">
                                        {{ $periode->nama_periode }}
                                    </span>
                                </div>
                            </td>

                            <td class="p-space-md">
                                {{ $periode->tahun_akademik }}
                            </td>

                            <td class="p-space-md whitespace-nowrap">
                                {{ $periode->tanggal_buka->format('d/m/Y') }}
                            </td>

                            <td class="p-space-md whitespace-nowrap">
                                {{ $periode->tanggal_tutup->format('d/m/Y') }}
                            </td>

                            <td class="p-space-md font-label-md text-label-md text-secondary">
                                {{ number_format($periode->pendaftaran_hunians_count, 0, ',', '.') }}
                            </td>

                            <td class="p-space-md">
                                <span class="inline-flex px-3 py-1 rounded-full whitespace-nowrap font-caption-sm-medium text-caption-sm-medium {{ $warnaAkses }}">
                                    {{ $labelAkses }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="p-space-md">
                                <div class="flex items-center gap-space-sm">
                                    {{-- Buka / tutup --}}
                                    <form action="{{ route('periode.status', $periode) }}"
                                          method="POST"
                                          @if ($periode->status === 'dibuka')
                                              onsubmit="return confirm('Tutup periode ini? Status periode akan diubah menjadi Ditutup. Data pendaftar yang sudah ada tetap tersimpan.')"
                                          @endif>
                                        @csrf
                                        @method('PATCH')

                                        <input type="hidden"
                                               name="status"
                                               value="{{ $periode->status === 'dibuka' ? 'ditutup' : 'dibuka' }}">

                                        <button type="submit"
                                                class="rounded-lg bg-surface-container-low text-secondary px-3 py-2 font-label-md text-label-md whitespace-nowrap hover:bg-surface-container cursor-pointer">
                                            {{ $periode->status === 'dibuka' ? 'Tutup Periode' : 'Buka Periode' }}
                                        </button>
                                    </form>

                                    {{-- Hapus --}}
                                    @if ($bolehHapus)
                                        <form action="{{ route('periode.destroy', $periode) }}"
                                              method="POST"
                                              data-confirm-title="Hapus Periode Pendaftaran"
                                              data-confirm="Hapus periode {{ $periode->nama_periode }}? Data periode ini akan dihapus permanen.">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    aria-label="Hapus periode {{ $periode->nama_periode }}"
                                                    title="Hapus periode"
                                                    class="flex items-center justify-center rounded-lg bg-error-container text-on-error-container p-2 hover:bg-error hover:text-on-error transition-colors cursor-pointer">
                                                <span class="material-symbols-outlined text-[20px]">
                                                    delete
                                                </span>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button"
                                                disabled
                                                aria-label="Hapus tidak tersedia: periode harus ditutup dan belum memiliki pendaftar"
                                                title="Hanya periode Ditutup tanpa pendaftar yang dapat dihapus"
                                                class="flex items-center justify-center rounded-lg bg-surface-container text-on-surface-variant p-2 opacity-40 cursor-not-allowed">
                                            <span class="material-symbols-outlined text-[20px]">
                                                delete
                                            </span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"
                                class="p-space-xl text-center text-on-surface-variant">
                                <span class="material-symbols-outlined block text-[36px] mb-space-sm">
                                    search_off
                                </span>
                                Tidak ada periode yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-space-lg bg-surface-container-low/50">
            <p class="font-caption-sm text-caption-sm text-on-surface-variant mb-space-md">
                Menampilkan {{ $periodes->firstItem() ?? 0 }}–{{ $periodes->lastItem() ?? 0 }}
                dari {{ $periodes->total() }} periode
            </p>
            {{ $periodes->links() }}
        </div>
    </section>
</div>

{{-- Popup tambah periode --}}
<dialog id="periode-dialog"
        aria-labelledby="periode-dialog-title"
        class="rounded-2xl bg-surface-container-lowest text-on-surface shadow-2xl">
    <div class="flex items-center justify-between gap-space-sm bg-primary-container text-on-primary p-space-lg">
        <div>
            <h2 id="periode-dialog-title"
                class="font-headline-sm text-headline-sm">
                Tambah Periode Pendaftaran
            </h2>
            <p class="font-caption-sm text-caption-sm text-secondary-fixed-dim mt-1">
                Isi informasi dan tentukan status periode.
            </p>
        </div>

        <button type="button"
                data-close-periode
                aria-label="Tutup formulir"
                class="p-2 rounded-lg hover:bg-secondary cursor-pointer">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <form action="{{ route('periode.store') }}"
          method="POST"
          class="p-space-lg">
        @csrf

        @if ($errors->any())
            <div role="alert"
                 class="bg-error-container text-on-error-container rounded-xl p-space-md mb-space-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
            <div class="md:col-span-2 flex flex-col gap-space-xs">
                <label for="nama_periode" class="font-label-md text-label-md">
                    Nama Periode
                </label>
                <input id="nama_periode"
                       name="nama_periode"
                       type="text"
                       maxlength="100"
                       required
                       value="{{ old('nama_periode') }}"
                       placeholder="Pendaftaran Hunian Semester Ganjil"
                       class="w-full bg-surface-container-low rounded-xl p-3 outline-none focus:ring-2 focus:ring-secondary">
            </div>

            <div class="flex flex-col gap-space-xs">
                <label for="tahun_akademik" class="font-label-md text-label-md">
                    Tahun Akademik
                </label>
                <input id="tahun_akademik"
                       name="tahun_akademik"
                       type="text"
                       maxlength="20"
                       required
                       value="{{ old('tahun_akademik') }}"
                       placeholder="2026/2027"
                       class="w-full bg-surface-container-low rounded-xl p-3 outline-none focus:ring-2 focus:ring-secondary">
            </div>

            <div class="flex flex-col gap-space-xs">
                <label for="status" class="font-label-md text-label-md">
                    Status Periode
                </label>
                <select id="status"
                        name="status"
                        required
                        class="w-full bg-surface-container-low rounded-xl p-3 outline-none focus:ring-2 focus:ring-secondary">
                    <option value="ditutup"
                            @selected(old('status', 'ditutup') === 'ditutup')>
                        Ditutup
                    </option>
                    <option value="dibuka"
                            @selected(old('status') === 'dibuka')>
                        Dibuka
                    </option>
                </select>
            </div>

            <div class="flex flex-col gap-space-xs">
                <label for="tanggal_buka" class="font-label-md text-label-md">
                    Tanggal Buka
                </label>
                <input id="tanggal_buka"
                       name="tanggal_buka"
                       type="date"
                       required
                       value="{{ old('tanggal_buka') }}"
                       class="w-full bg-surface-container-low rounded-xl p-3 outline-none focus:ring-2 focus:ring-secondary">
            </div>

            <div class="flex flex-col gap-space-xs">
                <label for="tanggal_tutup" class="font-label-md text-label-md">
                    Tanggal Tutup
                </label>
                <input id="tanggal_tutup"
                       name="tanggal_tutup"
                       type="date"
                       required
                       value="{{ old('tanggal_tutup') }}"
                       class="w-full bg-surface-container-low rounded-xl p-3 outline-none focus:ring-2 focus:ring-secondary">
            </div>
        </div>

        <div class="bg-surface-container-low rounded-xl p-space-md mt-space-lg text-on-surface-variant">
            Status Dibuka mengikuti rentang tanggal yang diisi.
            Jika tanggal buka belum tiba, periode ditampilkan sebagai Terjadwal.
        </div>

        <div class="flex justify-end gap-space-sm mt-space-lg">
            <button type="button"
                    data-close-periode
                    class="px-space-md py-3 rounded-xl bg-surface-container text-on-surface font-label-md text-label-md cursor-pointer">
                Batal
            </button>

            <button type="submit"
                    class="flex items-center gap-space-xs px-space-lg py-3 rounded-xl bg-secondary text-on-secondary font-label-md text-label-md cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">
                    save
                </span>
                Simpan Periode
            </button>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
    (() => {
        const dialog = document.getElementById('periode-dialog');
        const openButton = document.getElementById('tambah-periode');
        const tanggalBuka = document.getElementById('tanggal_buka');
        const tanggalTutup = document.getElementById('tanggal_tutup');

        openButton.addEventListener('click', () => dialog.showModal());

        document.querySelectorAll('[data-close-periode]').forEach(button => {
            button.addEventListener('click', () => dialog.close());
        });

        function sesuaikanTanggal() {
            tanggalTutup.min = tanggalBuka.value;
        }

        tanggalBuka.addEventListener('change', sesuaikanTanggal);
        sesuaikanTanggal();

        @if ($errors->any())
            dialog.showModal();
        @endif
    })();
</script>
@endpush