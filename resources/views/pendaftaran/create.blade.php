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
        && (!$bolehMemilihTipe || $roomChoices->isNotEmpty())
        && $jenisBerkas->isNotEmpty();
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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div><label for="id_gedung" class="block font-label-md text-label-md mb-2">Gedung Hunian <span class="text-error">*</span></label><select id="id_gedung" name="id_gedung" required class="{{ $inputClass }}"><option value="">Pilih gedung</option>@foreach ($roomChoices->unique('building_id') as $room)<option value="{{ $room['building_id'] }}" @selected((string)old('id_gedung') === (string)$room['building_id'])>{{ $room['building'] }}</option>@endforeach</select></div>
                            <div><label for="id_tipeKamar" class="block font-label-md text-label-md mb-2">Tipe Kamar <span class="text-error">*</span></label><select id="id_tipeKamar" name="id_tipeKamar" required class="{{ $inputClass }}"><option value="">Pilih tipe kamar</option>@foreach ($tipeKamars as $type)<option value="{{ $type->id_tipeKamar }}" @selected((string)old('id_tipeKamar') === (string)$type->id_tipeKamar)>{{ $type->nama_tipe }}</option>@endforeach</select></div>
                            <div><label for="id_kamar" class="block font-label-md text-label-md mb-2">Nomor Kamar <span class="text-error">*</span></label><select id="id_kamar" name="id_kamar" required class="{{ $inputClass }}"><option value="">Pilih kamar</option>@foreach ($roomChoices as $room)<option value="{{ $room['id'] }}" @selected((string)old('id_kamar') === (string)$room['id'])>{{ $room['building'] }} / {{ $room['number'] }}</option>@endforeach</select></div>
                        </div>
                        <p id="petunjuk-tipe" class="text-caption-sm text-on-surface-variant mt-3">Pilihan kamar mengikuti gedung, tipe, jenis kelamin, dan kapasitas yang tersedia.</p>
                    @else
                        <div class="flex items-start gap-space-sm rounded-xl bg-surface-container-low p-space-md">
                            <span class="material-symbols-outlined text-secondary">
                                info
                            </span>

                            <div>
                                <p class="font-label-md text-label-md">
                                    Gedung dan kamar ditentukan Admin Layanan
                                </p>

                                <p class="font-caption-sm text-caption-sm text-on-surface-variant mt-1">
                                    Kategori {{ $client->jenisClient?->jenis }}
                                    dapat mengajukan pendaftaran tanpa
                                    memilih gedung dan kamar.
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
                        Lengkapi unggahan FC Kartu Keluarga, FC KTP, dan Hasil Tes Kesehatan. Berkas akan diperiksa setelah pengajuan dikirim.
                    </p>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        @foreach ($jenisBerkas as $jenis)
                        <div class="rounded-xl border border-outline-variant/40 bg-surface-container-low p-4">
                            <label for="berkas-{{ $jenis->id_jenis_berkas }}" class="block font-semibold text-primary mb-3">{{ $jenis->nama_berkas }} <span class="text-error">*</span></label>
                            <input type="hidden" name="id_jenis_berkas[{{ $jenis->id_jenis_berkas }}]" value="{{ $jenis->id_jenis_berkas }}">
                            <input id="berkas-{{ $jenis->id_jenis_berkas }}" name="berkas[{{ $jenis->id_jenis_berkas }}]" type="file" accept=".pdf,.jpg,.jpeg,.png" required data-registration-document class="block w-full text-caption-sm file:bg-secondary file:text-on-secondary file:border-0 file:rounded-lg file:px-3 file:py-2 file:mr-2">
                            <p class="text-caption-sm text-on-surface-variant mt-3">PDF, JPG, JPEG, PNG. Maksimal 10 MB.</p>
                            @error('berkas.'.$jenis->id_jenis_berkas)<p class="text-caption-sm text-error mt-2">{{ $message }}</p>@enderror
                        </div>
                        @endforeach
                    </div>
                    @if ($jenisBerkas->isEmpty())<p class="text-error mt-3">Jenis berkas belum tersedia. Hubungi Admin Layanan.</p>@endif
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
                                Pilihan Kamar
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
        const gedung = document.getElementById('id_gedung');
        const kamar = document.getElementById('id_kamar');
        const roomChoices = @json($roomChoices);
        const petunjukTipe = document.getElementById('petunjuk-tipe');
        const genderInputs = document.querySelectorAll(
            'input[name="jenis_kelamin"]'
        );

        const inputsBerkas = [...document.querySelectorAll('[data-registration-document]')];
        const jumlahBerkas = document.getElementById('jumlah-berkas');
        const jenisBerkas = @json($jenisBerkas);

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
                ringkasanTipe.textContent = kamar?.value ? `${teksPilihan(gedung)} / ${teksPilihan(kamar)}` : 'Belum dipilih';
                tombolKirim.disabled = !bisaMengirimDasar || !kamar?.value;
            }
        }

        function setOptions(select, items, placeholder, selected) {
            select.replaceChildren(new Option(placeholder, ''));
            items.forEach(item => select.add(new Option(item.label, String(item.value))));
            select.value = items.some(item => String(item.value) === String(selected)) ? String(selected) : '';
            select.disabled = items.length === 0;
        }

        function saringTipe() {
            if (!tipe) { tombolKirim.disabled = !bisaMengirimDasar; perbaruiRingkasan(); return; }
            const gender = document.querySelector('input[name="jenis_kelamin"]:checked')?.value;
            const allowed = gender === 'laki-laki' ? ['putra','campuran','tamu'] : gender === 'perempuan' ? ['putri','campuran','tamu'] : [];
            const options = roomChoices.filter(room => allowed.includes(room.gender));
            const keepBuilding = gedung.value, keepType = tipe.value, keepRoom = kamar.value;
            const buildings = [...new Map(options.map(room => [room.building_id, {value:room.building_id,label:room.building}])).values()];
            setOptions(gedung, buildings, gender ? 'Pilih gedung' : 'Pilih jenis kelamin dahulu', keepBuilding);
            const inBuilding = options.filter(room => String(room.building_id) === gedung.value);
            const types = [...new Map(inBuilding.map(room => [room.type_id, {value:room.type_id,label:room.type}])).values()];
            setOptions(tipe, types, 'Pilih tipe kamar', keepType);
            const rooms = inBuilding.filter(room => String(room.type_id) === tipe.value).map(room => ({value:room.id,label:`Kamar ${room.number} (${room.available} tempat tersedia)`}));
            setOptions(kamar, rooms, 'Pilih nomor kamar', keepRoom);
            petunjukTipe.textContent = options.length ? 'Pilihan kamar disimpan dalam pendaftaran untuk diperiksa Admin Layanan.' : 'Belum ada kamar yang tersedia untuk kategori dan jenis kelamin ini.';
            perbaruiRingkasan();
        }

        periode.addEventListener('change', perbaruiRingkasan);
        gedung?.addEventListener('change', saringTipe);
        tipe?.addEventListener('change', saringTipe);
        kamar?.addEventListener('change', perbaruiRingkasan);

        genderInputs.forEach(input => {
            input.addEventListener('change', saringTipe);
        });

        inputsBerkas.forEach(input => input.addEventListener('change', () => {
            const count = inputsBerkas.filter(item => item.files.length).length;
            jumlahBerkas.textContent = `${count} dipilih`;
            ringkasanBerkas.textContent = `${count} berkas`;
            input.setCustomValidity(input.files[0]?.size > 10 * 1024 * 1024 ? 'Ukuran berkas maksimal 10 MB.' : '');
        }));

        saringTipe();
    })();
</script>
@endpush
