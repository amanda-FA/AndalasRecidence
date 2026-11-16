@php
    $gedung = $gedung ?? null;
    $fasilitasList = [
        ['label' => 'Dapur / Pantry', 'icon' => 'soup_kitchen'],
        ['label' => 'Musholla Terpadu', 'icon' => 'mosque'],
        ['label' => 'Ruang Belajar / Cowork', 'icon' => 'local_library'],
        ['label' => 'Parkir Berkanopi', 'icon' => 'garage_home'],
        ['label' => 'CCTV 24 Jam Aktif', 'icon' => 'videocam'],
        ['label' => 'WiFi Eduroam / IdREN', 'icon' => 'wifi'],
        ['label' => 'Sistem Hydrant & Tanggap Darurat', 'icon' => 'fire_extinguisher'],
    ];
    $selectedFasilitas = old('fasilitas', $gedung?->fasilitas ?? []);
@endphp

<div class="flex flex-col gap-space-lg">
    {{-- Bagian 1: Identitas & Informasi Umum --}}
    <section class="bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col gap-space-lg">
        <div class="pb-space-sm border-b border-surface-container">
            <h2 class="font-title-md text-title-md text-primary">Identitas &amp; Informasi Umum</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant">Konfigurasi label resmi dan kode unik inventaris gedung.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
            <div class="flex flex-col gap-1.5 md:col-span-2">
                <label class="font-label-md text-label-md text-primary flex items-center justify-between" for="nama">
                    <span>Nama Gedung Resmi <span class="text-error">*</span></span>
                    <span class="font-caption-sm text-caption-sm text-outline">Maks. 100 Karakter</span>
                </label>
                <input class="w-full px-space-md py-3 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest outline-none focus:ring-2 focus:ring-secondary/30 transition-all placeholder:text-outline"
                       id="nama" name="nama" value="{{ old('nama', $gedung?->nama) }}" placeholder="Contoh: Gedung Rusunawa Mandiri" required type="text">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="kode">
                    Kode Gedung Unik <span class="text-error">*</span>
                </label>
                <input class="w-full px-space-md py-3 rounded-lg bg-surface-container-low text-on-surface font-body-md-medium text-body-md-medium focus:bg-surface-container-lowest outline-none focus:ring-2 focus:ring-secondary/30 uppercase tracking-wider transition-all placeholder:text-outline"
                       id="kode" name="kode" value="{{ old('kode', $gedung?->kode) }}" placeholder="Contoh: GD-UNAND-E" required type="text">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="peruntukan">
                    Peruntukan Hunian <span class="text-error">*</span>
                </label>
                <div class="relative">
                    @php $peruntukanVal = old('peruntukan', $gedung?->peruntukan ?? 'putra'); @endphp
                    <select class="w-full px-space-md py-3 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest outline-none focus:ring-2 focus:ring-secondary/30 transition-all appearance-none cursor-pointer" id="peruntukan" name="peruntukan">
                        <option value="putra" {{ $peruntukanVal === 'putra' ? 'selected' : '' }}>Khusus Putra</option>
                        <option value="putri" {{ $peruntukanVal === 'putri' ? 'selected' : '' }}>Khusus Putri</option>
                        <option value="campuran" {{ $peruntukanVal === 'campuran' ? 'selected' : '' }}>Campuran / Pascasarjana &amp; Peneliti</option>
                        <option value="tamu" {{ $peruntukanVal === 'tamu' ? 'selected' : '' }}>Guest House / Tamu Kehormatan Kampus</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">expand_more</span>
                </div>
            </div>

            <div class="flex flex-col gap-1.5 md:col-span-2">
                <label class="font-label-md text-label-md text-primary mb-1">
                    Status Operasional <span class="text-error">*</span>
                </label>
                @php $statusVal = old('status', $gedung?->status ?? 'aktif'); @endphp
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-sm">
                    <label class="flex items-center gap-space-sm p-space-sm rounded-lg bg-surface-container-low hover:bg-surface-container transition-all cursor-pointer has-[:checked]:bg-secondary-fixed">
                        <input {{ $statusVal === 'aktif' ? 'checked' : '' }} class="accent-secondary w-4 h-4 cursor-pointer" name="status" type="radio" value="aktif">
                        <div class="flex flex-col">
                            <span class="font-body-md-medium text-body-md-medium text-primary">Aktif Operasional</span>
                            <span class="font-caption-sm text-caption-sm text-on-surface-variant">Dapat dihuni segera</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-space-sm p-space-sm rounded-lg bg-surface-container-low hover:bg-surface-container transition-all cursor-pointer has-[:checked]:bg-secondary-fixed">
                        <input {{ $statusVal === 'persiapan' ? 'checked' : '' }} class="accent-secondary w-4 h-4 cursor-pointer" name="status" type="radio" value="persiapan">
                        <div class="flex flex-col">
                            <span class="font-body-md-medium text-body-md-medium text-primary">Persiapan / Renovasi</span>
                            <span class="font-caption-sm text-caption-sm text-on-surface-variant">Maintenance fisik</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-space-sm p-space-sm rounded-lg bg-surface-container-low hover:bg-surface-container transition-all cursor-pointer has-[:checked]:bg-secondary-fixed">
                        <input {{ $statusVal === 'draf' ? 'checked' : '' }} class="accent-secondary w-4 h-4 cursor-pointer" name="status" type="radio" value="draf">
                        <div class="flex flex-col">
                            <span class="font-body-md-medium text-body-md-medium text-primary">Draf Tersimpan</span>
                            <span class="font-caption-sm text-caption-sm text-on-surface-variant">Pencatatan awal</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </section>

    {{-- Bagian 2: Spesifikasi Fisik & Kapasitas --}}
    <section class="bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col gap-space-lg">
        <div class="pb-space-sm border-b border-surface-container">
            <h2 class="font-title-md text-title-md text-primary">Spesifikasi Fisik &amp; Kapasitas</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant">Parameter volumetrik daya tampung dan lokasi geografis klaster.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-md">
            <div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-4">
                <label class="font-label-md text-label-md text-primary" for="klaster_wilayah">
                    Lokasi / Klaster Wilayah Kampus <span class="text-error">*</span>
                </label>
                @php $klasterVal = old('klaster_wilayah', $gedung?->klaster_wilayah ?? 'Sektor Kampus Limau Manis - Sayap Timur (Pusat Hunian)'); @endphp
                <div class="relative">
                    <select class="w-full px-space-md py-3 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest outline-none focus:ring-2 focus:ring-secondary/30 transition-all appearance-none cursor-pointer" id="klaster_wilayah" name="klaster_wilayah">
                        @foreach ([
                            'Sektor Kampus Limau Manis - Sayap Timur (Pusat Hunian)',
                            'Sektor Kampus Limau Manis - Sayap Barat',
                            'Kampus Jati Padang (Fakultas Kedokteran & Keperawatan)',
                            'Kampus II Payakumbuh',
                            'Kampus III Dharmasraya',
                        ] as $lokasi)
                            <option value="{{ $lokasi }}" {{ $klasterVal === $lokasi ? 'selected' : '' }}>{{ $lokasi }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">expand_more</span>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="jumlah_lantai">Jumlah Lantai <span class="text-error">*</span></label>
                <div class="relative">
                    <input class="w-full pl-space-md pr-10 py-3 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest outline-none focus:ring-2 focus:ring-secondary/30 transition-all" id="jumlah_lantai" name="jumlah_lantai" max="20" min="1" required type="number" value="{{ old('jumlah_lantai', $gedung?->jumlah_lantai ?? 4) }}">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 font-caption-sm text-caption-sm text-outline">Lt</span>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="estimasi_kamar">Estimasi Kamar <span class="text-error">*</span></label>
                <div class="relative">
                    <input class="w-full pl-space-md pr-12 py-3 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest outline-none focus:ring-2 focus:ring-secondary/30 transition-all" id="estimasi_kamar" name="estimasi_kamar" min="1" required type="number" value="{{ old('estimasi_kamar', $gedung?->estimasi_kamar ?? 40) }}">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 font-caption-sm text-caption-sm text-outline">Unit</span>
                </div>
            </div>

            <div class="flex flex-col gap-1.5 lg:col-span-2">
                <label class="font-label-md text-label-md text-primary" for="kapasitas_bed">Kapasitas Tempat Tidur (Bed) <span class="text-error">*</span></label>
                <div class="relative">
                    <input class="w-full pl-space-md pr-16 py-3 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest outline-none focus:ring-2 focus:ring-secondary/30 transition-all" id="kapasitas_bed" name="kapasitas_bed" min="1" required type="number" value="{{ old('kapasitas_bed', $gedung?->kapasitas_bed ?? 120) }}">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 font-caption-sm text-caption-sm text-outline">Orang</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Bagian 3: Fasilitas Bersama --}}
    <section class="bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col gap-space-lg">
        <div class="pb-space-sm border-b border-surface-container">
            <h2 class="font-title-md text-title-md text-primary">Fasilitas Bersama &amp; Proteksi</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant">Centang semua amenitas gedung yang siap difungsikan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach ($fasilitasList as $item)
                <label class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low hover:bg-surface-container transition-all cursor-pointer select-none">
                    <input name="fasilitas[]" value="{{ $item['label'] }}" {{ in_array($item['label'], $selectedFasilitas) ? 'checked' : '' }} class="w-4 h-4 rounded text-secondary accent-secondary cursor-pointer" type="checkbox">
                    <span class="font-body-md-medium text-body-md-medium text-primary">{{ $item['label'] }}</span>
                </label>
            @endforeach
        </div>
    </section>

    {{-- Bagian 4: Foto & Deskripsi --}}
    <section class="bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col gap-space-lg">
        <div class="pb-space-sm border-b border-surface-container">
            <h2 class="font-title-md text-title-md text-primary">Foto Tampak Depan &amp; Deskripsi Gedung</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant">Opsional: unggah foto tampak depan (JPG/PNG maks. 5MB) dan catatan gedung.</p>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md text-primary" for="foto">Foto Tampak Depan Gedung</label>
            <input class="w-full p-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-secondary file:text-on-secondary file:font-label-md file:cursor-pointer" id="foto" name="foto" type="file" accept="image/*">
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md text-primary" for="deskripsi">Deskripsi Ringkas Gedung</label>
            <textarea class="w-full p-space-md rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest outline-none focus:ring-2 focus:ring-secondary/30 transition-all placeholder:text-outline" id="deskripsi" name="deskripsi" placeholder="Tuliskan spesifikasi umum bangunan, peruntukan semester mahasiswa, atau ketentuan khusus penghuni..." rows="4">{{ old('deskripsi', $gedung?->deskripsi) }}</textarea>
        </div>
    </section>
</div>
