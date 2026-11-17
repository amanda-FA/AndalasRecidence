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
    // Data contoh untuk preview frontend.
    $rincian = [
        ['nama' => 'Sewa hunian', 'keterangan' => 'Contoh nominal sewa', 'nominal' => 700000],
        ['nama' => 'Deposit fasilitas', 'keterangan' => 'Contoh komponen tagihan', 'nominal' => 50000],
        ['nama' => 'Administrasi pembayaran', 'keterangan' => 'Contoh biaya administrasi', 'nominal' => 3000],
    ];

    $total = array_sum(array_column($rincian, 'nominal'));
@endphp

<div class="w-full max-w-[1280px] mx-auto flex flex-col gap-space-lg">
    {{-- Informasi simulasi --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md rounded-xl bg-surface-container p-space-md">
        <div class="flex items-start gap-space-sm">
            <span class="material-symbols-outlined text-secondary">
                info
            </span>
            <div>
                <p class="font-label-md text-label-md">
                    Preview Frontend Pembayaran
                </p>
                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                    Nominal dan kode di halaman ini adalah contoh.
                    Belum terhubung ke layanan pembayaran.
                </p>
            </div>
        </div>

        <div class="shrink-0">
            <label for="preview-status"
                   class="block font-caption-sm text-caption-sm mb-1">
                Preview status
            </label>
            <select id="preview-status"
                    class="w-full rounded-lg bg-surface-container-lowest px-3 py-2 outline-none focus:ring-2 focus:ring-secondary">
                <option value="menunggu">Menunggu Pembayaran</option>
                <option value="lunas">Lunas</option>
                <option value="gagal">Gagal</option>
                <option value="kedaluwarsa">Kedaluwarsa</option>
            </select>
        </div>
    </div>

    {{-- Hero --}}
    <section class="rounded-2xl bg-primary-container text-on-primary p-space-lg lg:p-space-xl shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-lg">
            <div>
                <span id="status-badge"
                      role="status"
                      class="inline-flex items-center gap-1 rounded-full bg-surface-container-lowest text-primary px-3 py-1 font-caption-sm-medium text-caption-sm-medium">
                    <span id="status-icon"
                          class="material-symbols-outlined text-[16px]">
                        hourglass_top
                    </span>
                    <span id="status-label">Menunggu Pembayaran</span>
                </span>

                <h1 class="font-headline-lg text-headline-lg mt-space-md">
                    Pembayaran Sewa Hunian
                </h1>

                <p class="text-secondary-fixed-dim mt-space-sm">
                    Kode tagihan contoh:
                    <strong>DEMO-INV-0001</strong>
                </p>

                <p id="status-description"
                   class="text-secondary-fixed-dim mt-space-xs max-w-2xl">
                    Tinjau rincian tagihan dan pilihan metode pembayaran.
                </p>
            </div>

            <div class="rounded-xl bg-primary/40 p-space-lg lg:min-w-56">
                <p class="font-caption-sm text-caption-sm text-secondary-fixed-dim">
                    Total Tagihan Contoh
                </p>
                <p class="font-headline-md text-headline-md mt-1">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </p>
                <p class="font-caption-sm text-caption-sm text-secondary-fixed-dim mt-space-sm">
                    Batas pembayaran belum diterbitkan
                </p>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-space-lg items-start">
        <div class="xl:col-span-3 min-w-0 flex flex-col gap-space-lg">
            {{-- Rincian tagihan --}}
            <section class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm">
                <h2 class="flex items-center gap-space-sm font-title-md text-title-md">
                    <span class="material-symbols-outlined text-secondary">
                        receipt_long
                    </span>
                    Rincian Tagihan
                </h2>

                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                    Seluruh komponen di bawah merupakan data simulasi.
                </p>

                <div class="flex flex-col gap-space-sm mt-space-lg">
                    @foreach ($rincian as $item)
                        <div class="flex items-start justify-between gap-space-md rounded-xl bg-surface-container-low p-space-md">
                            <div>
                                <p class="font-label-md text-label-md">
                                    {{ $item['nama'] }}
                                </p>
                                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                                    {{ $item['keterangan'] }}
                                </p>
                            </div>

                            <p class="whitespace-nowrap font-label-md text-label-md">
                                Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between gap-space-md rounded-xl bg-surface-container p-space-lg mt-space-md">
                    <div>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">
                            Total Pembayaran Contoh
                        </p>
                        <p class="font-headline-md text-headline-md text-primary mt-1">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </p>
                    </div>

                    <span class="rounded-full bg-surface-container-lowest px-3 py-1 text-secondary font-caption-sm text-caption-sm">
                        Simulasi
                    </span>
                </div>
            </section>

            {{-- Metode pembayaran --}}
            <section class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm">
                <h2 class="flex items-center gap-space-sm font-title-md text-title-md">
                    <span class="material-symbols-outlined text-secondary">
                        payments
                    </span>
                    Metode Pembayaran
                </h2>

                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                    Preview kanal pembayaran yang akan diintegrasikan.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 bg-surface-container-low rounded-xl p-1 mt-space-lg"
                     role="tablist"
                     aria-label="Metode pembayaran">
                    <button type="button"
                            id="tab-va"
                            role="tab"
                            aria-selected="true"
                            aria-controls="panel-va"
                            data-method="va"
                            class="method-tab flex items-center justify-center gap-2 rounded-lg px-3 py-3 font-label-md text-label-md bg-secondary text-on-secondary">
                        <span class="material-symbols-outlined text-[18px]">
                            account_balance
                        </span>
                        Virtual Account
                    </button>

                    <button type="button"
                            id="tab-qris"
                            role="tab"
                            aria-selected="false"
                            aria-controls="panel-qris"
                            data-method="qris"
                            class="method-tab flex items-center justify-center gap-2 rounded-lg px-3 py-3 font-label-md text-label-md text-on-surface-variant">
                        <span class="material-symbols-outlined text-[18px]">
                            qr_code_scanner
                        </span>
                        QRIS
                    </button>

                    <button type="button"
                            id="tab-loket"
                            role="tab"
                            aria-selected="false"
                            aria-controls="panel-loket"
                            data-method="loket"
                            class="method-tab flex items-center justify-center gap-2 rounded-lg px-3 py-3 font-label-md text-label-md text-on-surface-variant">
                        <span class="material-symbols-outlined text-[18px]">
                            storefront
                        </span>
                        Loket
                    </button>
                </div>

                {{-- Panel VA --}}
                <div id="panel-va"
                     role="tabpanel"
                     aria-labelledby="tab-va"
                     class="method-panel mt-space-lg">
                    <label for="pilihan-bank"
                           class="block font-label-md text-label-md mb-space-sm">
                        Contoh Pilihan Bank
                    </label>

                    <select id="pilihan-bank"
                            class="w-full rounded-xl bg-surface-container-low px-4 py-3 outline-none focus:ring-2 focus:ring-secondary">
                        <option value="NAGARI">Bank Nagari</option>
                        <option value="BNI">BNI</option>
                        <option value="MANDIRI">Mandiri</option>
                        <option value="BRI">BRI</option>
                        <option value="BSI">BSI</option>
                    </select>

                    <div class="rounded-xl bg-surface-container-low p-space-lg mt-space-md">
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">
                            Kode Demo — Bukan Nomor Virtual Account
                        </p>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md mt-space-sm">
                            <code id="kode-demo"
                                  class="font-semibold text-primary break-all">
                                DEMO-NAGARI-0001
                            </code>

                            <button id="salin-kode"
                                    type="button"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-secondary text-on-secondary px-3 py-2 font-label-md text-label-md">
                                <span class="material-symbols-outlined text-[18px]">
                                    content_copy
                                </span>
                                Salin Kode Contoh
                            </button>
                        </div>

                        <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-md">
                            Nomor VA dan bank yang tersedia akan mengikuti
                            hasil integrasi layanan pembayaran.
                        </p>
                    </div>
                </div>

                {{-- Panel QRIS --}}
                <div id="panel-qris"
                     role="tabpanel"
                     aria-labelledby="tab-qris"
                     class="method-panel mt-space-lg"
                     hidden>
                    <div class="rounded-xl bg-surface-container-low p-space-xl text-center">
                        <span class="material-symbols-outlined text-secondary text-[72px]"
                              aria-hidden="true">
                            qr_code_2
                        </span>

                        <h3 class="font-title-md text-title-md mt-space-sm">
                            Area Kode QRIS
                        </h3>

                        <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-sm">
                            Ini ikon placeholder, bukan kode pembayaran.
                            QR pembayaran akan ditampilkan setelah integrasi.
                        </p>
                    </div>
                </div>

                {{-- Panel loket --}}
                <div id="panel-loket"
                     role="tabpanel"
                     aria-labelledby="tab-loket"
                     class="method-panel mt-space-lg"
                     hidden>
                    <div class="rounded-xl bg-surface-container-low p-space-lg">
                        <h3 class="flex items-center gap-space-sm font-title-md text-title-md">
                            <span class="material-symbols-outlined text-secondary">
                                storefront
                            </span>
                            Pembayaran Melalui Loket
                        </h3>

                        <p class="text-on-surface-variant mt-space-sm">
                            Informasi lokasi, jadwal layanan, dan ketentuan
                            pembayaran akan mengikuti arahan pengelola.
                        </p>

                        <p class="font-label-md text-label-md text-secondary mt-space-md">
                            Kode invoice contoh: DEMO-INV-0001
                        </p>
                    </div>
                </div>

                <p id="pesan-salin"
                   role="status"
                   class="font-caption-sm text-caption-sm text-secondary mt-space-md"></p>
            </section>

            {{-- Panduan --}}
            <section class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm">
                <h2 class="flex items-center gap-space-sm font-title-md text-title-md mb-space-lg">
                    <span class="material-symbols-outlined text-secondary">
                        help_outline
                    </span>
                    Panduan Pembayaran
                </h2>

                <div class="flex flex-col gap-space-sm">
                    <details class="rounded-xl bg-surface-container-low p-space-md" open>
                        <summary class="font-label-md text-label-md cursor-pointer">
                            Virtual Account
                        </summary>
                        <p class="text-on-surface-variant mt-space-sm">
                            Setelah tagihan diterbitkan, nomor VA dan
                            petunjuk sesuai bank akan ditampilkan di sini.
                        </p>
                    </details>

                    <details class="rounded-xl bg-surface-container-low p-space-md">
                        <summary class="font-label-md text-label-md cursor-pointer">
                            QRIS
                        </summary>
                        <p class="text-on-surface-variant mt-space-sm">
                            Kode QR, nominal, dan batas pembayaran akan
                            mengikuti data transaksi dari layanan pembayaran.
                        </p>
                    </details>

                    <details class="rounded-xl bg-surface-container-low p-space-md">
                        <summary class="font-label-md text-label-md cursor-pointer">
                            Status Pembayaran
                        </summary>
                        <p class="text-on-surface-variant mt-space-sm">
                            Pada versi terintegrasi, status pembayaran
                            diperbarui setelah backend memverifikasi
                            informasi transaksi.
                        </p>
                    </details>
                </div>
            </section>
        </div>

        <div class="xl:col-span-2 min-w-0 flex flex-col gap-space-lg">
            {{-- Preview konfirmasi --}}
            <section class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm">
                <h2 class="flex items-center gap-space-sm font-title-md text-title-md">
                    <span class="material-symbols-outlined text-secondary">
                        cloud_upload
                    </span>
                    Konfirmasi Pembayaran
                </h2>

                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-sm">
                    Preview formulir konfirmasi manual.
                    Berkas belum diunggah atau disimpan ke server.
                </p>

                <form id="form-konfirmasi"
                      onsubmit="return false"
                      class="flex flex-col gap-space-md mt-space-lg">
                    <div>
                        <label for="nama-penyetor"
                               class="block font-label-md text-label-md mb-space-xs">
                            Nama Pemilik Rekening / Penyetor
                        </label>
                        <input id="nama-penyetor"
                               type="text"
                               maxlength="150"
                               required
                               placeholder="Masukkan nama penyetor"
                               class="w-full rounded-xl bg-surface-container-low px-4 py-3 outline-none focus:ring-2 focus:ring-secondary">
                    </div>

                    <div>
                        <label for="waktu-transfer"
                               class="block font-label-md text-label-md mb-space-xs">
                            Tanggal dan Waktu Transfer
                        </label>
                        <input id="waktu-transfer"
                               type="datetime-local"
                               required
                               class="w-full rounded-xl bg-surface-container-low px-4 py-3 outline-none focus:ring-2 focus:ring-secondary">
                    </div>

                    <div>
                        <label for="bukti-transfer"
                               class="block font-label-md text-label-md mb-space-xs">
                            Pilih Bukti Transfer
                        </label>
                        <input id="bukti-transfer"
                               type="file"
                               accept=".pdf,.jpg,.jpeg,.png"
                               required
                               class="block w-full rounded-xl bg-surface-container-low p-space-md">

                        <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-sm">
                            PDF, JPG, JPEG, PNG. Maksimal 10 MB.
                        </p>

                        <p id="nama-bukti"
                           class="font-caption-sm text-caption-sm text-secondary mt-space-sm break-all"
                           aria-live="polite"></p>
                    </div>

                    <button id="tombol-konfirmasi"
                            type="submit"
                            class="flex items-center justify-center gap-space-xs rounded-xl bg-secondary text-on-secondary px-4 py-3 font-label-md text-label-md disabled:opacity-40 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined text-[20px]">
                            task_alt
                        </span>
                        Preview Konfirmasi
                    </button>

                    <p id="pesan-konfirmasi"
                       role="status"
                       class="font-caption-sm text-caption-sm text-secondary"></p>
                </form>
            </section>

            {{-- Identitas akun --}}
            <section class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm">
                <h2 class="font-title-md text-title-md mb-space-lg">
                    Data Pendaftar
                </h2>

                <dl class="flex flex-col gap-space-md">
                    <div>
                        <dt class="font-caption-sm text-caption-sm text-on-surface-variant">
                            Nama Akun
                        </dt>
                        <dd class="font-label-md text-label-md mt-1">
                            {{ auth()->user()->nama_lengkap }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-caption-sm text-caption-sm text-on-surface-variant">
                            Email
                        </dt>
                        <dd class="mt-1 break-all">
                            {{ auth()->user()->email }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-caption-sm text-caption-sm text-on-surface-variant">
                            Kamar
                        </dt>
                        <dd class="mt-1">
                            Belum dihubungkan ke data penempatan
                        </dd>
                    </div>
                </dl>

                <img src="{{ asset('images/asramaunand.png') }}"
                     alt="Kawasan asrama Universitas Andalas"
                     class="w-full h-40 object-cover rounded-xl mt-space-lg"
                     loading="lazy">
            </section>

            <section class="rounded-2xl bg-surface-container-low p-space-lg">
                <h2 class="flex items-center gap-space-sm font-label-md text-label-md">
                    <span class="material-symbols-outlined text-secondary">
                        support_agent
                    </span>
                    Bantuan Pembayaran
                </h2>

                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-sm">
                    Hubungi Admin Layanan untuk pertanyaan mengenai
                    tagihan. Kontak resmi akan ditambahkan setelah
                    dikonfirmasi pengelola.
                </p>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const tabs = Array.from(
            document.querySelectorAll('.method-tab')
        );

        function pilihMetode(method, fokus = false) {
            tabs.forEach(tab => {
                const aktif = tab.dataset.method === method;

                tab.setAttribute('aria-selected', String(aktif));
                tab.tabIndex = aktif ? 0 : -1;

                tab.classList.toggle('bg-secondary', aktif);
                tab.classList.toggle('text-on-secondary', aktif);
                tab.classList.toggle('text-on-surface-variant', !aktif);

                document.getElementById(
                    `panel-${tab.dataset.method}`
                ).hidden = !aktif;

                if (aktif && fokus) tab.focus();
            });
        }

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                pilihMetode(tab.dataset.method);
            });

            tab.addEventListener('keydown', event => {
                let target = index;

                if (event.key === 'ArrowRight') {
                    target = (index + 1) % tabs.length;
                } else if (event.key === 'ArrowLeft') {
                    target = (index - 1 + tabs.length) % tabs.length;
                } else if (event.key === 'Home') {
                    target = 0;
                } else if (event.key === 'End') {
                    target = tabs.length - 1;
                } else {
                    return;
                }

                event.preventDefault();
                pilihMetode(tabs[target].dataset.method, true);
            });
        });

        pilihMetode('va');

        const bank = document.getElementById('pilihan-bank');
        const kode = document.getElementById('kode-demo');
        const pesanSalin = document.getElementById('pesan-salin');

        bank.addEventListener('change', () => {
            kode.textContent = `DEMO-${bank.value}-0001`;
            pesanSalin.textContent = '';
        });

        document.getElementById('salin-kode')
            .addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(
                        kode.textContent.trim()
                    );

                    pesanSalin.textContent =
                        'Kode contoh disalin. Ini bukan nomor pembayaran.';
                } catch {
                    pesanSalin.textContent =
                        'Tidak dapat menyalin otomatis. Pilih teks kode dan salin secara manual.';
                }
            });

        const bukti = document.getElementById('bukti-transfer');
        const namaBukti = document.getElementById('nama-bukti');
        const pesanKonfirmasi = document.getElementById('pesan-konfirmasi');

        bukti.addEventListener('change', () => {
            const file = bukti.files[0];

            bukti.setCustomValidity('');
            namaBukti.textContent = '';
            pesanKonfirmasi.textContent = '';

            if (!file) return;

            namaBukti.textContent =
                `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;

            if (!/\.(pdf|jpe?g|png)$/i.test(file.name)) {
                bukti.setCustomValidity(
                    'Pilih file PDF, JPG, JPEG, atau PNG.'
                );
            } else if (file.size > 10 * 1024 * 1024) {
                bukti.setCustomValidity(
                    'Ukuran file maksimal 10 MB.'
                );
            }
        });

        document.getElementById('form-konfirmasi')
            .addEventListener('submit', event => {
                event.preventDefault();

                pesanKonfirmasi.textContent =
                    'Preview berhasil. Data dan berkas belum dikirim ke server; status pembayaran tidak berubah.';
            });

        const statusSelect = document.getElementById('preview-status');
        const statusLabel = document.getElementById('status-label');
        const statusIcon = document.getElementById('status-icon');
        const statusDescription = document.getElementById('status-description');
        const tombolKonfirmasi = document.getElementById('tombol-konfirmasi');

        const statusData = {
            menunggu: {
                label: 'Menunggu Pembayaran',
                icon: 'hourglass_top',
                deskripsi:
                    'Tinjau rincian tagihan dan pilihan metode pembayaran.',
            },
            lunas: {
                label: 'Lunas — Simulasi',
                icon: 'check_circle',
                deskripsi:
                    'Contoh tampilan pembayaran lunas. Tidak ada transaksi yang diproses.',
            },
            gagal: {
                label: 'Gagal — Simulasi',
                icon: 'error_outline',
                deskripsi:
                    'Contoh tampilan transaksi gagal. Petunjuk percobaan ulang akan mengikuti layanan pembayaran.',
            },
            kedaluwarsa: {
                label: 'Kedaluwarsa — Simulasi',
                icon: 'timer_off',
                deskripsi:
                    'Contoh tampilan tagihan melewati batas pembayaran. Belum ada tagihan baru yang diterbitkan.',
            },
        };

        statusSelect.addEventListener('change', () => {
            const value = statusSelect.value;
            const data = statusData[value];

            statusLabel.textContent = data.label;
            statusIcon.textContent = data.icon;
            statusDescription.textContent = data.deskripsi;

            tombolKonfirmasi.disabled =
                value === 'lunas' || value === 'kedaluwarsa';

            pesanKonfirmasi.textContent = '';
        });
    })();
</script>
@endpush