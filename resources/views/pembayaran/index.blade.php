@extends('layouts.app')

@section('title', 'Pembayaran Hunian - Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary">
            payments
        </span>
        <span class="font-label-md text-label-md">
            Pembayaran Hunian
        </span>
    </div>
@endsection

@section('content')

@php
    $terverifikasi =
        $pendaftaran?->status_pendaftaran === 'terverifikasi';

    $total = $terverifikasi
        ? $pembayaran?->jumlah
        : null;

    $statusBayar = $pembayaran?->status_pembayaran;

    $pembiayaanKhusus = in_array($status, [
        'pembiayaan_kipk',
        'pembiayaan_pihak_ketiga',
        'pembiayaan_penyelenggara',
    ], true);

    $namaKategori = match ($kategori) {
        'reguler' => 'Maba Lokal NON KIP-K',
        'kipk' => 'Maba Lokal KIP-K',
        's1_non_maba' => 'Mahasiswa S1 Non Maba',
        'international_private' => 'Internasional/S2/S3 pribadi',
        'international_funded' => 'Internasional/S2/S3 ditanggung',
        'guest' => 'Non Mahasiswa (UMUM)',
        'summer_course' => 'Summer Course',
        default => 'Belum diketahui',
    };

    $label = match (true) {
        !$pendaftaran => 'Belum Mendaftar',
        !$terverifikasi => ucfirst(str_replace('_', ' ', $status)),

        $statusBayar === 'menunggu_kipk' =>
            'Menunggu Pembiayaan KIP-K',

        $status === 'pembiayaan_kipk' =>
            'Pembiayaan KIP-K',

        $status === 'pembiayaan_pihak_ketiga' =>
            'Pembiayaan Pihak Ketiga',

        $status === 'pembiayaan_penyelenggara' =>
            'Pembiayaan Penyelenggara',

        $status === 'pembayaran_belum_diaktifkan' =>
            'Pembayaran Belum Diaktifkan',

        $status === 'kategori_belum_dikenali' =>
            'Kategori Belum Dikenali',

        $statusBayar === 'simulasi_lunas' =>
            'Lunas (Simulasi)',

        $statusBayar === 'simulasi_gagal' =>
            'Gagal (Simulasi)',

        $statusBayar === 'menunggu' =>
            'Menunggu Pembayaran',

        $pembayaran !== null =>
            ucfirst(str_replace('_', ' ', $statusBayar)),

        $status === 'tarif_belum_tersedia' =>
            'Tarif Belum Tersedia',

        default => 'Tagihan Belum Diterbitkan',
    };

    $ikon = match (true) {
        !$pendaftaran => 'assignment',
        !$terverifikasi => 'pending_actions',
        $pembiayaanKhusus => 'verified_user',
        $status === 'pembayaran_belum_diaktifkan' => 'schedule',
        $status === 'kategori_belum_dikenali' => 'help',
        $statusBayar === 'simulasi_lunas' => 'check_circle',
        $statusBayar === 'simulasi_gagal' => 'error',
        $statusBayar === 'menunggu' => 'hourglass_top',
        $status === 'tarif_belum_tersedia' => 'warning',
        default => 'receipt_long',
    };

    $warnaBadge = match (true) {
        !$terverifikasi => 'bg-slate-100 text-slate-700',
        $pembiayaanKhusus => 'bg-blue-100 text-blue-800',
        $status === 'pembayaran_belum_diaktifkan' =>
            'bg-amber-100 text-amber-800',
        $statusBayar === 'simulasi_lunas' =>
            'bg-green-100 text-green-800',
        $statusBayar === 'simulasi_gagal' =>
            'bg-red-100 text-red-800',
        $statusBayar === 'menunggu' =>
            'bg-amber-100 text-amber-800',
        default => 'bg-surface-container-lowest text-primary',
    };

    $deskripsi = match (true) {
        !$pendaftaran =>
            'Silakan melakukan pendaftaran hunian terlebih dahulu.',

        !$terverifikasi =>
            'Pembayaran tersedia setelah pendaftaran terverifikasi.',

        $statusBayar === 'simulasi_lunas' =>
            'Simulasi pembayaran berhasil. Tidak ada transaksi uang sungguhan.',

        $statusBayar === 'simulasi_gagal' =>
            'Simulasi pembayaran gagal. Silakan lakukan pengujian kembali.',

        $statusBayar === 'menunggu' =>
            'Tagihan telah diterbitkan dan menunggu simulasi pembayaran.',

        $statusBayar === 'menunggu_kipk' =>
            'Invoice Rp0 sementara. Pembiayaan KIP-K belum dinyatakan lunas.',

        $pesan !== null => $pesan,

        default =>
            'Nominal pembayaran mengikuti tarif hunian yang berlaku.',
    };

    $namaMetode = match ($pembayaran?->metode_bayar) {
        'simulasi_va' => 'Virtual Account (Simulasi)',
        'simulasi_qris' => 'QRIS (Simulasi)',
        'simulasi_loket' => 'Loket (Simulasi)',
        'belum_dipilih' => 'Belum dipilih',
        'kipk' => 'Pembiayaan KIP-K',
        default => $pembayaran?->metode_bayar ?? 'Belum tersedia',
    };
@endphp

<div class="w-full max-w-[1280px] mx-auto flex flex-col gap-space-lg">
    @if (session('success'))
        <div role="status" class="rounded-xl bg-green-50 text-green-800 p-4">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div role="status" class="rounded-xl bg-amber-50 text-amber-800 p-4">{{ session('warning') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="rounded-xl bg-red-50 text-red-800 p-4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif


    {{-- Informasi pendaftaran --}}
    <div class="rounded-xl bg-surface-container p-space-md
                flex items-start gap-3">

        <span class="material-symbols-outlined text-secondary">
            info
        </span>

        <div>
            @if (!$pendaftaran)

                Silakan
                <a href="{{ route('pendaftaran.create') }}"
                   class="underline text-secondary">
                    mendaftar hunian
                </a>
                terlebih dahulu.

            @elseif (!$terverifikasi)

                Tagihan belum tersedia karena pendaftaran
                belum terverifikasi.

            @elseif ($pesan)

                {{ $pesan }}

            @else

                Tarif hunian ditentukan berdasarkan gedung,
                tipe kamar, kategori penghuni, dan satuan
                penyewaan yang berlaku.

            @endif
        </div>
    </div>

    {{-- Hero pembayaran --}}
    <section class="rounded-2xl bg-primary-container
                    text-on-primary p-space-lg lg:p-space-xl shadow-sm">

        <div class="flex flex-col lg:flex-row lg:items-center
                    justify-between gap-space-lg">

            <div>

                <span id="status-badge"
                      role="status"
                      class="inline-flex items-center gap-1 rounded-full
                             px-3 py-1 font-caption-sm-medium
                             text-caption-sm-medium {{ $warnaBadge }}">

                    <span id="status-icon"
                          class="material-symbols-outlined text-[16px]">
                        {{ $ikon }}
                    </span>

                    <span id="status-label">
                        {{ $label }}
                    </span>
                </span>

                <h1 class="font-headline-lg text-headline-lg mt-space-md">
                    Pembayaran Sewa Hunian
                </h1>

                <p class="text-secondary-fixed-dim mt-space-sm">
                    Referensi pembayaran:
                    <strong>
                        {{ $pembayaran && $terverifikasi
                            ? $pembayaran->id_pembayaran
                            : 'Belum tersedia' }}
                    </strong>
                </p>

                <p id="status-description"
                   class="text-secondary-fixed-dim mt-space-xs max-w-2xl">
                    {{ $deskripsi }}
                </p>

            </div>

            <div class="rounded-xl bg-primary/40 p-space-lg lg:min-w-56">

                <p class="font-caption-sm text-caption-sm
                          text-secondary-fixed-dim">
                    {{ $statusBayar === 'menunggu_kipk' && $terverifikasi ? 'Invoice Sementara KIP-K' : 'Total Tagihan Tercatat' }}
                </p>

                <p class="font-headline-md text-headline-md mt-1">

                    @if ($total !== null)

                        Rp {{ number_format($total, 0, ',', '.') }}

                    @elseif ($pembiayaanKhusus)

                        Skema Khusus

                    @else

                        Belum diterbitkan

                    @endif

                </p>

                <p class="font-caption-sm text-caption-sm
                          text-secondary-fixed-dim mt-space-sm">
                    Kanal pembayaran belum terintegrasi
                </p>

            </div>

        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-5
                gap-space-lg items-start">

        {{-- Kolom utama --}}
        <div class="xl:col-span-3 min-w-0 flex flex-col gap-space-lg">

            {{-- Rincian tagihan --}}
            <section class="rounded-2xl bg-surface-container-lowest
                            p-space-lg shadow-sm">

                <h2 class="flex items-center gap-space-sm
                           font-title-md text-title-md">

                    <span class="material-symbols-outlined text-secondary">
                        receipt_long
                    </span>

                    Rincian Tagihan
                </h2>

                <div class="mt-space-lg divide-y divide-outline-variant">

                    <div class="flex justify-between gap-4 py-3">
                        <span class="text-on-surface-variant">
                            Kategori penghuni
                        </span>
                        <span class="font-medium text-right">
                            {{ $namaKategori }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 py-3">
                        <span class="text-on-surface-variant">
                            Gedung
                        </span>
                        <span class="font-medium text-right">
                            {{ $pendaftaran?->kamar?->gedung?->nama_gedung
                                ?? 'Belum tersedia' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 py-3">
                        <span class="text-on-surface-variant">
                            Tipe kamar
                        </span>
                        <span class="font-medium text-right">
                            {{ $pendaftaran?->tipeKamar?->nama_tipe
                                ?? 'Belum tersedia' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 py-3">
                        <span class="text-on-surface-variant">
                            Nomor kamar
                        </span>
                        <span class="font-medium text-right">
                            {{ $pendaftaran?->kamar?->nomor_kamar
                                ?? 'Belum tersedia' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 py-3">
                        <span class="text-on-surface-variant">
                            Skema pembayaran
                        </span>
                        <span class="font-medium text-right">

                            {{ $skema['satuan'] ?? 'Menunggu ketentuan' }}

                        </span>
                    </div>

                </div>

                @if ($kategori === 'guest' && $terverifikasi)
                    <div class="mt-space-md text-sm text-on-surface-variant">
                        <p>Tanggal masuk: {{ $pendaftaran->tanggal_masuk ?? 'Belum tersedia' }}</p>
                        <p>Rencana keluar: {{ $pendaftaran->rencana_keluar ?? 'Belum tersedia' }}</p>
                    </div>
                @endif

                <div class="rounded-xl bg-surface-container-low
                            p-space-md mt-space-lg">

                    <p class="font-label-md text-label-md">
                        {{ $total !== null
                            ? ($statusBayar === 'menunggu_kipk' ? 'Invoice Sementara KIP-K' : 'Nominal Tagihan')
                            : 'Estimasi Biaya Hunian' }}
                    </p>

                    <p class="font-headline-md text-headline-md
                              text-primary mt-2">

                        @if ($total !== null)

                            Rp {{ number_format($total, 0, ',', '.') }}

                        @elseif ($tarif !== null)

                            Rp {{ number_format($tarif, 0, ',', '.') }}

                        @elseif ($pembiayaanKhusus)

                            Pembiayaan Khusus

                        @else

                            Belum tersedia

                        @endif

                    </p>

                    <p class="text-on-surface-variant mt-2">

                        @if ($total !== null)

                            @if ($statusBayar === 'menunggu_kipk')
                                Nominal Rp0 bersifat sementara, bukan bukti pelunasan.
                            @else
                                Nominal mengikuti tagihan yang telah tersimpan dalam sistem.
                            @endif

                        @elseif ($pembiayaanKhusus)

                            {{ $pesan }}

                        @elseif ($tarif !== null)

                            Nominal berdasarkan tarif gedung
                            dan tipe kamar yang berlaku.

                        @else

                            Biaya hunian belum dapat ditampilkan.

                        @endif

                    </p>

                </div>

            </section>

            {{-- Metode pembayaran --}}
            <section class="rounded-2xl bg-surface-container-lowest
                            p-space-lg shadow-sm">

                <h2 class="flex items-center gap-space-sm
                           font-title-md text-title-md">

                    <span class="material-symbols-outlined text-secondary">
                        payments
                    </span>

                    Metode Pembayaran
                </h2>

                @if ($pembiayaanKhusus)

                    <div class="mt-space-lg rounded-xl bg-blue-50
                                text-blue-800 p-4 flex items-start gap-3">

                        <span class="material-symbols-outlined">
                            verified_user
                        </span>

                        <div>
                            <p class="font-semibold">
                                {{ $label }}
                            </p>

                            <p class="mt-1">
                                {{ $pesan }}
                            </p>

                            <p class="mt-2 text-sm">
                                Hubungi Admin Layanan untuk
                                konfirmasi ketentuan pembiayaan.
                            </p>
                        </div>

                    </div>

                    @if ($kategori === 'kipk' && $dapatTerbitkanTagihan && $isLocal)
                        <form method="POST" action="{{ route('pembayaran.terbitkan') }}" class="mt-space-lg">
                            @csrf
                            <input type="hidden" name="id_pendaftaran" value="{{ $pendaftaran->id_pendaftaran }}">
                            <button type="submit" class="w-full rounded-xl bg-secondary text-on-secondary px-4 py-3 font-medium">
                                Terbitkan Invoice Sementara KIP-K (Pengujian)
                            </button>
                        </form>
                    @endif

                @elseif ($status === 'pembayaran_belum_diaktifkan')

                    <div class="mt-space-lg rounded-xl bg-amber-50
                                text-amber-800 p-4">

                        <p class="font-semibold">
                            Pembayaran belum tersedia
                        </p>

                        <p class="mt-1">
                            {{ $pesan }}
                        </p>

                    </div>

                @elseif (!$terverifikasi)

                    <div class="mt-space-lg rounded-xl
                                bg-surface-container-low p-4">

                        Pembayaran belum dapat dilakukan sebelum
                        pendaftaran terverifikasi.

                    </div>

                @elseif ($status === 'tarif_belum_tersedia'
                    || $status === 'kategori_belum_dikenali')

                    <div class="mt-space-lg rounded-xl bg-amber-50
                                text-amber-800 p-4">

                        {{ $pesan ?? 'Pembayaran belum tersedia.' }}

                    </div>

                @elseif ($statusBayar === 'simulasi_lunas')

                    <div class="mt-space-lg rounded-xl bg-green-50
                                text-green-800 p-4 flex items-start gap-3">

                        <span class="material-symbols-outlined text-3xl">
                            check_circle
                        </span>

                        <div>
                            <p class="font-semibold">
                                Simulasi Pembayaran Berhasil
                            </p>

                            <p class="mt-1">
                                Hasil simulasi telah tersimpan
                                dalam sistem.
                            </p>

                            <p class="mt-2 text-sm">
                                Ini bukan bukti pelunasan resmi.
                                Tidak ada uang yang ditransfer.
                            </p>
                        </div>

                    </div>

                @elseif (!$isLocal)
                    <div class="mt-space-lg rounded-xl bg-surface-container-low p-4">
                        Kanal pembayaran belum terintegrasi. Simulasi hanya tersedia di lingkungan lokal.
                    </div>

                @elseif ($dapatTerbitkanTagihan)

                    <div class="mt-space-lg rounded-xl bg-amber-50
                                text-amber-800 p-4">

                        Mode pengujian. Jangan melakukan
                        transfer uang sungguhan.

                    </div>

                    <form method="POST"
                          action="{{ route('pembayaran.terbitkan') }}"
                          class="mt-space-lg">

                        @csrf

                        <input type="hidden"
                               name="id_pendaftaran"
                               value="{{ $pendaftaran->id_pendaftaran }}">

                        <button type="submit"
                                class="w-full rounded-xl bg-secondary
                                       text-on-secondary px-4 py-3
                                       font-medium flex items-center
                                       justify-center gap-2">

                            <span class="material-symbols-outlined">
                                receipt_long
                            </span>

                            Terbitkan Tagihan Pengujian

                        </button>

                    </form>

                @elseif ($dapatSimulasi)

                    @if ($statusBayar === 'simulasi_gagal')

                        <div class="mt-space-lg rounded-xl bg-red-50
                                    text-red-800 p-4">

                            Simulasi sebelumnya gagal.
                            Kamu dapat mencoba kembali.

                        </div>

                    @endif

                    <div class="mt-space-lg rounded-xl bg-amber-50
                                text-amber-800 p-4">

                        Pembayaran ini hanya simulasi.
                        Sistem belum terhubung ke bank
                        atau Payment Gateway.

                    </div>

                    <form method="POST"
                          action="{{ route('pembayaran.simulasi') }}"
                          class="flex flex-col gap-space-md mt-space-lg">

                        @csrf

                        <input type="hidden"
                               name="id_pendaftaran"
                               value="{{ $pendaftaran->id_pendaftaran }}">

                        <label for="metode" class="font-medium">
                            Metode pembayaran simulasi
                        </label>

                        <select id="metode"
                                name="metode"
                                required
                                class="rounded-xl bg-surface-container-low
                                       px-4 py-3">

                            <option value="va">
                                Virtual Account
                            </option>

                            <option value="qris">
                                QRIS
                            </option>

                            <option value="loket">
                                Loket
                            </option>

                        </select>

                        <label for="hasil" class="font-medium">
                            Skenario pengujian
                        </label>

                        <select id="hasil"
                                name="hasil"
                                required
                                class="rounded-xl bg-surface-container-low
                                       px-4 py-3">

                            <option value="berhasil">
                                Berhasil
                            </option>

                            <option value="gagal">
                                Gagal
                            </option>

                        </select>

                        <button type="submit"
                                class="rounded-xl bg-secondary
                                       text-on-secondary px-4 py-3
                                       font-medium flex items-center
                                       justify-center gap-2">

                            <span class="material-symbols-outlined">
                                payments
                            </span>

                            Simulasikan Pembayaran

                        </button>

                    </form>

                @else

                    <div class="mt-space-lg rounded-xl
                                bg-surface-container-low p-4">

                        Tidak ada tindakan pembayaran
                        yang tersedia saat ini.

                    </div>

                @endif

                @if ($pembayaran && $terverifikasi)

                    <div class="mt-space-lg border-t
                                border-outline-variant pt-4">

                        <p class="text-on-surface-variant">
                            Metode pembayaran tercatat
                        </p>

                        <p class="font-medium mt-1">
                            {{ $namaMetode }}
                        </p>

                    </div>

                @endif

            </section>

            {{-- Panduan pembayaran --}}
            @if (($skema['jenis'] ?? null) === 'mandiri')

                <section class="rounded-2xl bg-surface-container-lowest
                                p-space-lg shadow-sm">

                    <h2 class="flex items-center gap-space-sm
                               font-title-md text-title-md mb-space-lg">

                        <span class="material-symbols-outlined text-secondary">
                            help_outline
                        </span>

                        Panduan Pembayaran

                    </h2>

                    <div class="flex flex-col gap-space-sm">

                        <details class="rounded-xl
                                        bg-surface-container-low p-space-md">

                            <summary class="font-label-md text-label-md
                                            cursor-pointer">
                                Virtual Account
                            </summary>

                            <p class="text-on-surface-variant mt-space-sm">
                                Nomor Virtual Account akan tersedia
                                setelah integrasi layanan pembayaran.
                                Saat ini hanya tersedia simulasi.
                            </p>

                        </details>

                        <details class="rounded-xl
                                        bg-surface-container-low p-space-md">

                            <summary class="font-label-md text-label-md
                                            cursor-pointer">
                                QRIS
                            </summary>

                            <p class="text-on-surface-variant mt-space-sm">
                                Kode QR pembayaran akan diterbitkan
                                oleh layanan pembayaran setelah
                                integrasi dilakukan.
                            </p>

                        </details>

                        <details class="rounded-xl
                                        bg-surface-container-low p-space-md">

                            <summary class="font-label-md text-label-md
                                            cursor-pointer">
                                Pembayaran melalui Loket
                            </summary>

                            <p class="text-on-surface-variant mt-space-sm">
                                Pembayaran melalui loket masih
                                berupa simulasi pengujian sistem.
                            </p>

                        </details>

                        <details class="rounded-xl
                                        bg-surface-container-low p-space-md">

                            <summary class="font-label-md text-label-md
                                            cursor-pointer">
                                Status Pembayaran
                            </summary>

                            <p class="text-on-surface-variant mt-space-sm">
                                Status simulasi tidak menunjukkan
                                pelunasan resmi. Pada versi terintegrasi,
                                status pembayaran akan diperbarui
                                setelah transaksi diverifikasi.
                            </p>

                        </details>

                    </div>

                </section>

            @endif

        </div>

        {{-- Kolom informasi pendaftar --}}
        <div class="xl:col-span-2 min-w-0 flex flex-col gap-space-lg">

            <section class="rounded-2xl bg-surface-container-lowest
                            p-space-lg shadow-sm">

                <h2 class="font-title-md text-title-md mb-space-lg">
                    Data Pendaftar
                </h2>

                <dl class="flex flex-col gap-space-md">

                    <div>
                        <dt class="font-caption-sm text-caption-sm
                                   text-on-surface-variant">
                            Nama Akun
                        </dt>

                        <dd class="font-label-md text-label-md mt-1">
                            {{ auth()->user()->nama_lengkap }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-caption-sm text-caption-sm
                                   text-on-surface-variant">
                            Email
                        </dt>

                        <dd class="mt-1 break-all">
                            {{ auth()->user()->email }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-caption-sm text-caption-sm
                                   text-on-surface-variant">
                            Nomor Identitas
                        </dt>

                        <dd class="mt-1">
                            {{ $client->nomor_identitas ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-caption-sm text-caption-sm
                                   text-on-surface-variant">
                            Kategori Penghuni
                        </dt>

                        <dd class="mt-1">
                            {{ $namaKategori }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-caption-sm text-caption-sm
                                   text-on-surface-variant">
                            Kamar
                        </dt>

                        <dd class="mt-1">
                            {{ $pendaftaran?->kamar?->gedung?->nama_gedung
                                ?? 'Belum tersedia' }}

                            —

                            {{ $pendaftaran?->kamar?->nomor_kamar
                                ?? 'Belum tersedia' }}
                        </dd>
                    </div>

                </dl>

                <img src="{{ asset('images/asramaunand.png') }}"
                     alt="Kawasan asrama Universitas Andalas"
                     class="w-full h-40 object-cover rounded-xl mt-space-lg"
                     loading="lazy">

            </section>

            {{-- Bantuan --}}
            <section class="rounded-2xl bg-surface-container-low
                            p-space-lg">

                <h2 class="flex items-center gap-space-sm
                           font-label-md text-label-md">

                    <span class="material-symbols-outlined text-secondary">
                        support_agent
                    </span>

                    Bantuan Pembayaran

                </h2>

                <p class="font-caption-sm text-caption-sm
                          text-on-surface-variant mt-space-sm">

                    Hubungi Admin Layanan untuk informasi
                    mengenai tarif, tagihan, dan ketentuan
                    pembiayaan hunian.

                </p>

            </section>

        </div>

    </div>

</div>

@endsection