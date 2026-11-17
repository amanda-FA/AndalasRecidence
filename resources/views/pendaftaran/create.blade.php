@extends('layouts.app')

@section('title', 'Pendaftaran Hunian - Andalas Residence')

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <span class="material-symbols-outlined text-secondary">
            assignment
        </span>
        <span class="font-label-md text-label-md">
            Layanan Hunian Client
        </span>
    </div>
@endsection

@section('content')
@php
    $inputClass = 'w-full rounded-xl bg-surface-container-low px-4 py-3 text-on-surface outline-none focus:ring-2 focus:ring-secondary';

    $genderProfil = match (strtolower($client->jenis_kelamin ?? '')) {
        'l', 'laki-laki', 'laki laki', 'putra' => 'laki-laki',
        'p', 'perempuan', 'putri' => 'perempuan',
        default => '',
    };

    $genderTerpilih = old('jenis_kelamin', $genderProfil);

    $bisaMengirim = $periodes->isNotEmpty()
        && (!$bolehMemilihTipe || $tipeKamars->isNotEmpty());
@endphp

<div class="w-full max-w-[1280px] mx-auto">
    {{-- Judul --}}
    <div class="mb-space-xl">
        <div class="flex items-center gap-space-xs text-secondary mb-space-sm">
            <span class="material-symbols-outlined text-[20px]">
                edit_document
            </span>
            <span class="font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider">
                Portal Pendaftaran Hunian
            </span>
        </div>

        <h1 class="font-headline-lg text-headline-lg text-on-surface">
            Formulir Pendaftaran Hunian
        </h1>

        <p class="text-on-surface-variant mt-space-sm max-w-3xl">
            Periksa identitas akun, lengkapi data diri dan pilihan
            pendaftaran, lalu lampirkan berkas untuk diverifikasi
            Admin Layanan.
        </p>
    </div>

    {{-- Kesalahan validasi --}}
    @if ($errors->any())
        <div role="alert"
             class="rounded-2xl bg-error-container text-on-error-container p-space-lg mb-space-lg">
            <p class="font-label-md text-label-md mb-space-sm">
                Pengajuan belum berhasil dikirim
            </p>

            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($periodes->isEmpty())
        <div role="status"
             class="rounded-xl bg-surface-container p-space-lg mb-space-lg">
            Belum ada periode yang sedang menerima pendaftaran.
        </div>
    @endif

    @if ($bolehMemilihTipe && $tipeKamars->isEmpty())
        <div role="status"
             class="rounded-xl bg-surface-container p-space-lg mb-space-lg">
            Pilihan tipe kamar belum tersedia.
        </div>
    @endif

    <form action="{{ route('pendaftaran.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="grid grid-cols-1 xl:grid-cols-3 gap-space-lg items-start">
        @csrf

        <div class="xl:col-span-2 min-w-0 flex flex-col gap-space-lg">
            {{-- Bagian A: Data diri --}}
            <section class="rounded-2xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="flex items-start justify-between gap-space-md p-space-lg bg-surface-container-low">
                    <div class="flex items-start gap-space-sm">
                        <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-primary-container text-on-primary shrink-0">
                            <span class="material-symbols-outlined">
                                badge
                            </span>
                        </span>

                        <div>
                            <h2 class="font-title-md text-title-md text-primary">
                                Bagian A: Data Diri Pendaftar
                            </h2>
                            <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                                Pastikan identitas dan alamat yang diisi benar.
                            </p>
                        </div>
                    </div>

                    <span class="hidden md:inline-flex rounded-full bg-surface-container-lowest px-3 py-1 text-secondary font-caption-sm text-caption-sm">
                        Profil Client
                    </span>
                </div>

                <div class="p-space-lg grid grid-cols-1 md:grid-cols-2 gap-space-md">
                    <div class="md:col-span-2">
                        <label for="nama_akun"
                               class="block font-label-md text-label-md mb-space-xs">
                            Nama Lengkap
                        </label>

                        <input id="nama_akun"
                               type="text"
                               readonly
                               value="{{ $client->pengguna->nama_lengkap }}"
                               class="{{ $inputClass }}">

                        <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                            Mengikuti identitas akun yang login.
                        </p>
                    </div>

                    <div>
                        <label for="nomor_identitas"
                               class="block font-label-md text-label-md mb-space-xs">
                            Nomor Identitas / NIM
                            <span class="text-error">*</span>
                        </label>

                        <input id="nomor_identitas"
                               name="nomor_identitas"
                               type="text"
                               maxlength="50"
                               required
                               value="{{ old('nomor_identitas', $client->nomor_identitas) }}"
                               class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="kategori_akun"
                               class="block font-label-md text-label-md mb-space-xs">
                            Kategori Pendaftar
                        </label>

                        <input id="kategori_akun"
                               type="text"
                               readonly
                               value="{{ $client->jenisClient?->jenis ?? 'Belum ditentukan' }}"
                               class="{{ $inputClass }}">
                    </div>

                    <fieldset>
                        <legend class="font-label-md text-label-md mb-space-xs">
                            Jenis Kelamin
                            <span class="text-error">*</span>
                        </legend>

                        <div class="flex flex-wrap gap-space-sm">
                            <label class="flex items-center gap-2 bg-surface-container-low rounded-xl px-4 py-3 cursor-pointer">
                                <input type="radio"
                                       name="jenis_kelamin"
                                       value="laki-laki"
                                       required
                                       @checked($genderTerpilih === 'laki-laki')
                                       class="accent-secondary">

                                <span>Laki-laki</span>
                            </label>

                            <label class="flex items-center gap-2 bg-surface-container-low rounded-xl px-4 py-3 cursor-pointer">
                                <input type="radio"
                                       name="jenis_kelamin"
                                       value="perempuan"
                                       required
                                       @checked($genderTerpilih === 'perempuan')
                                       class="accent-secondary">

                                <span>Perempuan</span>
                            </label>
                        </div>
                    </fieldset>

                    <div>
                        <label for="pekerjaan"
                               class="block font-label-md text-label-md mb-space-xs">
                            Pekerjaan (opsional)
                        </label>

                        <input id="pekerjaan"
                               name="pekerjaan"
                               type="text"
                               maxlength="100"
                               value="{{ old('pekerjaan', $client->pekerjaan) }}"
                               placeholder="Contoh: Mahasiswa"
                               class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="email_akun"
                               class="block font-label-md text-label-md mb-space-xs">
                            Email Akun
                        </label>

                        <input id="email_akun"
                               type="email"
                               readonly
                               value="{{ $client->pengguna->email }}"
                               class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="hp_akun"
                               class="block font-label-md text-label-md mb-space-xs">
                            Nomor HP Akun
                        </label>

                        <input id="hp_akun"
                               type="text"
                               readonly
                               value="{{ $client->pengguna->no_hp }}"
                               placeholder="Belum diisi"
                               class="{{ $inputClass }}">
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat_asal"
                               class="block font-label-md text-label-md mb-space-xs">
                            Alamat Asal
                            <span class="text-error">*</span>
                        </label>

                        <textarea id="alamat_asal"
                                  name="alamat_asal"
                                  rows="3"
                                  maxlength="2000"
                                  required
                                  class="{{ $inputClass }}">{{ old('alamat_asal', $client->alamat_asal) }}</textarea>
                    </div>
                </div>
            </section>

            {{-- Bagian B: Pilihan pendaftaran --}}
            <section class="rounded-2xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="flex items-start gap-space-sm p-space-lg bg-surface-container-low">
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-primary-container text-on-primary shrink-0">
                        <span class="material-symbols-outlined">
                            calendar_month
                        </span>
                    </span>

                    <div>
                        <h2 class="font-title-md text-title-md text-primary">
                            Bagian B: Pilihan Pendaftaran
                        </h2>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                            Pilih periode yang sedang menerima pengajuan.
                        </p>
                    </div>
                </div>

                <div class="p-space-lg flex flex-col gap-space-lg">
                    <div>
                        <label for="id_periode"
                               class="block font-label-md text-label-md mb-space-xs">
                            Periode Pendaftaran
                            <span class="text-error">*</span>
                        </label>

                        <select id="id_periode"
                                name="id_periode"
                                required
                                class="{{ $inputClass }}">
                            <option value="">Pilih periode</option>

                            @foreach ($periodes as $periode)
                                <option value="{{ $periode->id_periode }}"
                                        @selected((string) old('id_periode') === (string) $periode->id_periode)>
                                    {{ $periode->nama_periode }}
                                    — {{ $periode->tahun_akademik }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($bolehMemilihTipe)
                        <div>
                            <label for="id_tipeKamar"
                                   class="block font-label-md text-label-md mb-space-xs">
                                Preferensi Tipe Kamar
                                <span class="text-error">*</span>
                            </label>

                            <select id="id_tipeKamar"
                                    name="id_tipeKamar"
                                    required
                                    aria-describedby="petunjuk-tipe"
                                    class="{{ $inputClass }}">
                                <option value="">Pilih tipe kamar</option>

                                @foreach ($tipeKamars as $tipe)
                                    <option
                                        value="{{ $tipe->id_tipeKamar }}"
                                        data-gender="{{ implode(',', $genderTipe[$tipe->id_tipeKamar] ?? []) }}"
                                        @selected((string) old('id_tipeKamar') === (string) $tipe->id_tipeKamar)
                                    >
                                        {{ $tipe->nama_tipe }}

                                        @if (!str_contains(strtolower($tipe->nama_tipe), 'keluarga'))
                                            — kapasitas {{ $tipe->kapasitas }} orang
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            <p id="petunjuk-tipe"
                               class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-sm"
                               aria-live="polite">
                                Pilihan mengikuti kategori dan jenis kelamin pendaftar.
                            </p>

                            <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-xs">
                                Preferensi ini belum menetapkan kamar.
                                Penempatan akhir dilakukan oleh Admin Layanan.
                            </p>
                        </div>
                    @else
                        <div class="flex items-start gap-space-sm rounded-xl bg-surface-container-low p-space-md">
                            <span class="material-symbols-outlined text-secondary">
                                info
                            </span>

                            <div>
                                <p class="font-label-md text-label-md">
                                    Tipe kamar ditentukan Admin Layanan
                                </p>

                                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                                    Kategori {{ $client->jenisClient?->jenis }}
                                    dapat mengajukan pendaftaran tanpa
                                    memilih tipe kamar.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Bagian C: Berkas --}}
            <section class="rounded-2xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="flex items-start justify-between gap-space-md p-space-lg bg-surface-container-low">
                    <div class="flex items-start gap-space-sm">
                        <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-primary-container text-on-primary shrink-0">
                            <span class="material-symbols-outlined">
                                cloud_upload
                            </span>
                        </span>

                        <div>
                            <h2 class="font-title-md text-title-md text-primary">
                                Bagian C: Unggah Berkas Persyaratan
                            </h2>

                            <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                                PDF, JPG, JPEG, atau PNG.
                                Maksimal 10 MB per file.
                            </p>
                        </div>
                    </div>

                    <span id="jumlah-berkas"
                          class="shrink-0 rounded-full bg-surface-container-lowest px-3 py-1 text-secondary font-caption-sm text-caption-sm"
                          aria-live="polite">
                        0 dipilih
                    </span>
                </div>

                <div class="p-space-lg">
                    <p class="text-on-surface-variant mb-space-md">
                        Pilih 1–5 lampiran sesuai persyaratan yang
                        disampaikan pengelola. Berkas akan diperiksa
                        setelah pengajuan dikirim.
                    </p>

                    <label for="berkas"
                           class="block font-label-md text-label-md mb-space-sm">
                        Pilih atau Ganti Lampiran
                        <span class="text-error">*</span>
                    </label>

                    <input id="berkas"
                           name="berkas[]"
                           type="file"
                           accept=".pdf,.jpg,.jpeg,.png"
                           multiple
                           required
                           class="block w-full rounded-xl bg-surface-container-low p-space-md">

                    <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-sm">
                        Pilih beberapa file sekaligus.
                        Pemilihan baru menggantikan daftar sebelumnya.
                    </p>

                    <ul id="daftar-berkas"
                        class="flex flex-col gap-space-sm mt-space-lg"
                        aria-live="polite"></ul>

                    <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-md">
                        Jika pengajuan ditolak karena kesalahan input,
                        pilih kembali berkas sebelum mengirim ulang.
                    </p>
                </div>
            </section>

            {{-- Persetujuan --}}
            <section class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm">
                <label class="flex items-start gap-space-sm cursor-pointer">
                    <input type="checkbox"
                           name="persetujuan"
                           value="1"
                           required
                           @checked(old('persetujuan'))
                           class="mt-1 accent-secondary">

                    <span class="font-body-md text-body-md text-on-surface-variant">
                        Saya menyatakan bahwa informasi dan berkas yang
                        dikirim benar serta bersedia mengikuti proses
                        verifikasi pendaftaran hunian.
                    </span>
                </label>
            </section>
        </div>

        {{-- Ringkasan kanan --}}
        <aside class="min-w-0 xl:sticky xl:top-24 flex flex-col gap-space-lg">
            <section class="relative rounded-2xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="h-1 bg-secondary"></div>

                <div class="p-space-lg">
                    <h2 class="flex items-center gap-space-sm font-title-md text-title-md">
                        <span class="material-symbols-outlined text-secondary">
                            receipt_long
                        </span>
                        Ringkasan Pendaftaran
                    </h2>

                    <dl class="flex flex-col gap-space-md mt-space-lg">
                        <div>
                            <dt class="font-caption-sm text-caption-sm text-on-surface-variant">
                                Pendaftar
                            </dt>
                            <dd class="font-label-md text-label-md mt-1">
                                {{ $client->pengguna->nama_lengkap }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-caption-sm text-caption-sm text-on-surface-variant">
                                Kategori
                            </dt>
                            <dd class="mt-1">
                                {{ $client->jenisClient?->jenis ?? 'Belum ditentukan' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-caption-sm text-caption-sm text-on-surface-variant">
                                Periode
                            </dt>
                            <dd id="ringkasan-periode" class="mt-1">
                                Belum dipilih
                            </dd>
                        </div>

                        <div>
                            <dt class="font-caption-sm text-caption-sm text-on-surface-variant">
                                Preferensi Tipe Kamar
                            </dt>
                            <dd id="ringkasan-tipe" class="mt-1">
                                {{ $bolehMemilihTipe ? 'Belum dipilih' : 'Ditentukan Admin Layanan' }}
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-space-sm">
                            <dt class="font-caption-sm text-caption-sm text-on-surface-variant">
                                Lampiran
                            </dt>
                            <dd id="ringkasan-berkas"
                                class="font-label-md text-label-md text-secondary">
                                0 berkas
                            </dd>
                        </div>
                    </dl>

                    <div class="rounded-xl bg-surface-container-low p-space-md mt-space-lg text-on-surface-variant font-caption-sm text-caption-sm">
                        Pengiriman formulir belum menetapkan kamar
                        atau menjamin ketersediaan tempat tidur.
                        Pengajuan akan diperiksa oleh Admin Layanan.
                    </div>

                    <button type="submit"
                            id="kirim-pendaftaran"
                            data-bisa-mengirim="{{ $bisaMengirim ? '1' : '0' }}"
                            @disabled(!$bisaMengirim)
                            class="w-full flex items-center justify-center gap-space-xs mt-space-lg rounded-xl bg-secondary text-on-secondary px-4 py-3 font-label-md text-label-md hover:bg-secondary/90 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                        Kirim Pendaftaran
                        <span class="material-symbols-outlined text-[20px]">
                            arrow_forward
                        </span>
                    </button>
                </div>
            </section>

            <section class="rounded-2xl bg-surface-container-low p-space-lg">
                <h2 class="flex items-center gap-space-sm font-label-md text-label-md text-primary">
                    <span class="material-symbols-outlined text-secondary">
                        help_outline
                    </span>
                    Sebelum Mengirim
                </h2>

                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-space-sm">
                    Periksa identitas, pilihan periode, dan keterbacaan
                    lampiran. Hubungi Admin Layanan jika data kategori
                    pada akunmu belum sesuai.
                </p>
            </section>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const periode = document.getElementById('id_periode');
        const tipe = document.getElementById('id_tipeKamar');
        const petunjukTipe = document.getElementById('petunjuk-tipe');
        const genderInputs = document.querySelectorAll(
            'input[name="jenis_kelamin"]'
        );

        const inputBerkas = document.getElementById('berkas');
        const daftarBerkas = document.getElementById('daftar-berkas');
        const jumlahBerkas = document.getElementById('jumlah-berkas');

        const ringkasanPeriode = document.getElementById('ringkasan-periode');
        const ringkasanTipe = document.getElementById('ringkasan-tipe');
        const ringkasanBerkas = document.getElementById('ringkasan-berkas');

        const tombolKirim = document.getElementById('kirim-pendaftaran');
        const bisaMengirimDasar =
            tombolKirim.dataset.bisaMengirim === '1';

        function teksPilihan(select) {
            if (!select.value) return 'Belum dipilih';

            return select.selectedOptions[0].textContent
                .replace(/\s+/g, ' ')
                .trim();
        }

        function perbaruiRingkasan() {
            ringkasanPeriode.textContent = teksPilihan(periode);

            if (tipe) {
                ringkasanTipe.textContent = teksPilihan(tipe);
            }
        }

        function saringTipe() {
            if (!tipe) {
                tombolKirim.disabled = !bisaMengirimDasar;
                perbaruiRingkasan();
                return;
            }

            const gender = document.querySelector(
                'input[name="jenis_kelamin"]:checked'
            )?.value ?? '';

            let pilihanValid = false;
            let jumlahPilihan = 0;

            for (const option of tipe.options) {
                if (!option.value) continue;

                const genderDiizinkan = (
                    option.dataset.gender ?? ''
                ).split(',');

                const boleh = gender !== ''
                    && genderDiizinkan.includes(gender);

                option.hidden = !boleh;
                option.disabled = !boleh;

                if (boleh) {
                    jumlahPilihan++;
                }

                if (option.selected && boleh) {
                    pilihanValid = true;
                }
            }

            if (!pilihanValid) {
                tipe.value = '';
            }

            if (!gender) {
                tipe.options[0].textContent =
                    'Pilih jenis kelamin terlebih dahulu';

                petunjukTipe.textContent =
                    'Lengkapi jenis kelamin pada Bagian A untuk melihat pilihan tipe kamar.';
            } else if (jumlahPilihan === 0) {
                tipe.options[0].textContent =
                    'Belum ada tipe kamar yang sesuai';

                petunjukTipe.textContent =
                    'Belum tersedia pilihan tipe untuk kategori dan jenis kelamin ini. Hubungi Admin Layanan.';
            } else {
                tipe.options[0].textContent = 'Pilih tipe kamar';

                petunjukTipe.textContent =
                    'Pilihan mengikuti kategori dan peruntukan gedung yang sesuai jenis kelamin pendaftar.';
            }

            tombolKirim.disabled =
                !bisaMengirimDasar || jumlahPilihan === 0;

            perbaruiRingkasan();
        }

        periode.addEventListener('change', perbaruiRingkasan);
        tipe?.addEventListener('change', perbaruiRingkasan);

        genderInputs.forEach(input => {
            input.addEventListener('change', saringTipe);
        });

        inputBerkas.addEventListener('change', () => {
            const files = Array.from(inputBerkas.files);

            daftarBerkas.replaceChildren();
            jumlahBerkas.textContent = `${files.length} dipilih`;
            ringkasanBerkas.textContent = `${files.length} berkas`;

            for (const file of files) {
                const item = document.createElement('li');
                item.className =
                    'flex items-start gap-3 rounded-xl bg-surface-container-low p-4';

                const icon = document.createElement('span');
                icon.className =
                    'material-symbols-outlined text-secondary';
                icon.setAttribute('aria-hidden', 'true');
                icon.textContent = 'description';

                const detail = document.createElement('div');
                detail.className = 'min-w-0 flex-1';

                const nama = document.createElement('p');
                nama.className =
                    'font-label-md text-label-md break-all';
                nama.textContent = file.name;

                const ukuran = document.createElement('p');
                ukuran.className =
                    'font-caption-sm text-caption-sm text-on-surface-variant mt-1';
                ukuran.textContent =
                    `${(file.size / 1024 / 1024).toFixed(2)} MB · Dipilih untuk dikirim`;

                detail.append(nama, ukuran);
                item.append(icon, detail);
                daftarBerkas.appendChild(item);
            }

            if (files.length > 5) {
                inputBerkas.setCustomValidity(
                    'Pilih maksimal lima berkas.'
                );
            } else if (
                files.some(file => file.size > 10 * 1024 * 1024)
            ) {
                inputBerkas.setCustomValidity(
                    'Ukuran setiap berkas maksimal 10 MB.'
                );
            } else {
                inputBerkas.setCustomValidity('');
            }
        });

        saringTipe();
    })();
</script>
@endpush