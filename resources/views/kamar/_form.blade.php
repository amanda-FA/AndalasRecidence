@php
    $kamar = $kamar ?? null;
    $selectedGedung = old('gedung_id', $kamar?->gedung_id ?? ($selectedGedungId ?? ''));
    $fasilitasOptions = [
        ['label' => 'AC Pendingin Ruangan', 'desc' => 'Daikin Inverter / Standar', 'icon' => 'mode_fan'],
        ['label' => 'Kamar Mandi Dalam', 'desc' => 'Private En-suite Shower', 'icon' => 'bathtub'],
        ['label' => 'Pemanas Air Listrik', 'desc' => 'Water Heater', 'icon' => 'water_heater'],
        ['label' => 'Meja & Kursi Belajar', 'desc' => 'Ergonomis + Lampu Meja', 'icon' => 'desk'],
        ['label' => 'Lemari Pakaian 2 Pintu', 'desc' => 'Loker individual berkunci', 'icon' => 'inventory'],
        ['label' => 'Kasur Springbed Single', 'desc' => 'Ukuran 100x200 + Bantal', 'icon' => 'single_bed'],
        ['label' => 'WiFi Eduroam High-Speed', 'desc' => 'Coverage Access Point', 'icon' => 'wifi'],
        ['label' => 'Balkon Pribadi', 'desc' => 'Akses ventilasi teras luar', 'icon' => 'deck'],
    ];
    $selectedFasilitas = old('fasilitas', $kamar ? $kamar->fasilitas->pluck('nama_fasilitas')->all() : []);
@endphp

<div class="flex flex-col gap-space-lg">
    {{-- Bagian 1: Identitas & Penempatan Kamar --}}
    <div class="bg-surface-container-lowest rounded-2xl p-space-lg md:p-space-xl shadow-sm flex flex-col gap-space-lg">
        <div class="pb-space-sm border-b border-surface-container">
            <h2 class="font-headline-sm text-headline-sm text-primary">Identitas &amp; Penempatan Kamar</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant">Tentukan penempatan gedung, lantai, dan jenis tata letak ruang kamar.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-md">
            <div class="flex flex-col gap-1.5 lg:col-span-2">
                <label class="font-label-md text-label-md text-primary" for="gedung_id">
                    Pilih Gedung Residensial <span class="text-error">*</span>
                </label>
                <div class="relative">
                    <select id="gedung_id" name="gedung_id" required class="w-full appearance-none bg-surface-container-low hover:bg-surface-container px-space-md py-3 rounded-lg font-body-md text-body-md text-primary outline-none focus:bg-surface-container-lowest transition-colors pr-10 cursor-pointer">
                        <option value="">-- Pilih Kompleks Gedung --</option>
                        @foreach ($gedungs as $g)
                            <option value="{{ $g->id }}" {{ (string) $selectedGedung === (string) $g->id ? 'selected' : '' }}>{{ $g->nama }} ({{ $g->peruntukan_label }})</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">expand_more</span>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="nomor_kamar">
                    Nomor Kamar <span class="text-error">*</span>
                </label>
                <input id="nomor_kamar" name="nomor_kamar" value="{{ old('nomor_kamar', $kamar?->nomor_kamar) }}" required placeholder="misal: 206" type="text" class="w-full bg-surface-container-low hover:bg-surface-container px-space-md py-3 rounded-lg font-body-md text-body-md text-primary outline-none focus:bg-surface-container-lowest transition-colors placeholder:text-outline">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="lantai">
                    Tingkat Lantai <span class="text-error">*</span>
                </label>
                <input id="lantai" name="lantai" value="{{ old('lantai', $kamar?->lantai ?? 1) }}" min="1" max="20" required type="number" class="w-full bg-surface-container-low hover:bg-surface-container px-space-md py-3 rounded-lg font-body-md text-body-md text-primary outline-none focus:bg-surface-container-lowest transition-colors">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="sayap">Sayap / Zona Koridor</label>
                @php $sayapVal = old('sayap', $kamar?->sayap ?? 'Sayap Barat'); @endphp
                <div class="relative">
                    <select id="sayap" name="sayap" class="w-full appearance-none bg-surface-container-low hover:bg-surface-container px-space-md py-3 rounded-lg font-body-md text-body-md text-primary outline-none focus:bg-surface-container-lowest transition-colors pr-10 cursor-pointer">
                        @foreach (['Sayap Barat', 'Sayap Timur', 'Sayap Selatan', 'Koridor Utama'] as $s)
                            <option value="{{ $s }}" {{ $sayapVal === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">expand_more</span>
                </div>
            </div>

            <div class="flex flex-col gap-1.5 lg:col-span-2">
                <label class="font-label-md text-label-md text-primary" for="tipe_kamar">
                    Konfigurasi Tipe Kamar <span class="text-error">*</span>
                </label>
                @php $tipeVal = old('tipe_kamar', $kamar?->tipe_kamar ?? 'Double Shared'); @endphp
                <div class="relative">
                    <select id="tipe_kamar" name="tipe_kamar" required class="w-full appearance-none bg-surface-container-low hover:bg-surface-container px-space-md py-3 rounded-lg font-body-md text-body-md text-primary outline-none focus:bg-surface-container-lowest transition-colors pr-10 cursor-pointer">
                        @foreach (['Reguler', 'Single Deluxe', 'Double Shared', 'Triple Standard', 'Quad Standard', 'Wisma'] as $tipe)
                            <option value="{{ $tipe }}" {{ $tipeVal === $tipe ? 'selected' : '' }}>{{ $tipe }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">expand_more</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Bagian 2: Kapasitas, Tarif & Status Hunian --}}
    <div class="bg-surface-container-lowest rounded-2xl p-space-lg md:p-space-xl shadow-sm flex flex-col gap-space-lg">
        <div class="pb-space-sm border-b border-surface-container">
            <h2 class="font-headline-sm text-headline-sm text-primary">Kapasitas, Tarif &amp; Status Hunian</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant">Atur kapasitas alokasi bed, jumlah terisi saat ini, tarif, serta kontrol akses.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-space-md">
            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="kapasitas_bed">Kapasitas Bed <span class="text-error">*</span></label>
                <input id="kapasitas_bed" name="kapasitas_bed" value="{{ old('kapasitas_bed', $kamar?->kapasitas_bed ?? 2) }}" min="1" max="20" required type="number" class="w-full bg-surface-container-low px-space-md py-3 rounded-lg font-body-md text-body-md text-primary outline-none">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="bed_terisi">Bed Terisi <span class="text-error">*</span></label>
                <input id="bed_terisi" name="bed_terisi" value="{{ old('bed_terisi', $kamar?->bed_terisi ?? 0) }}" min="0" max="20" required type="number" class="w-full bg-surface-container-low px-space-md py-3 rounded-lg font-body-md text-body-md text-primary outline-none">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="tarif">Tarif / Bulan (Rp)</label>
                <input id="tarif" name="tarif" value="{{ old('tarif', $kamar?->tarif ?? 1250000) }}" min="0" type="number" class="w-full bg-surface-container-low px-space-md py-3 rounded-lg font-body-md text-body-md text-primary outline-none">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="status">Status Kamar <span class="text-error">*</span></label>
                @php $statusVal = old('status', $kamar?->status ?? 'tersedia'); @endphp
                <select id="status" name="status" required class="w-full bg-surface-container-low px-space-md py-3 rounded-lg font-body-md text-body-md text-primary outline-none cursor-pointer">
                    <option value="tersedia" {{ $statusVal === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="penuh" {{ $statusVal === 'penuh' ? 'selected' : '' }}>Penuh</option>
                    <option value="perbaikan" {{ $statusVal === 'perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary">Sistem Akses Pintu</label>
                <label class="flex items-center justify-between p-2.5 px-space-md rounded-xl bg-surface-container-low h-[48px] cursor-pointer">
                    <span class="font-caption-sm-medium text-caption-sm-medium text-primary">Tag RFID</span>
                    <input type="checkbox" name="rfid_enabled" value="1" {{ old('rfid_enabled', $kamar?->rfid_enabled ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-secondary cursor-pointer">
                </label>
            </div>
        </div>
    </div>

    {{-- Bagian 3: Kelengkapan Fasilitas Kamar --}}
    <div class="bg-surface-container-lowest rounded-2xl p-space-lg md:p-space-xl shadow-sm flex flex-col gap-space-lg">
        <div class="pb-space-sm border-b border-surface-container">
            <h2 class="font-headline-sm text-headline-sm text-primary">Kelengkapan Fasilitas Kamar</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant">Pilih inventaris dan sarana yang terpasang di kamar ini.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
            @foreach ($fasilitasOptions as $item)
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all cursor-pointer">
                    <input name="fasilitas[]" value="{{ $item['label'] }}" {{ in_array($item['label'], $selectedFasilitas) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded text-secondary accent-secondary cursor-pointer" type="checkbox">
                    <div class="flex flex-col">
                        <span class="font-body-md-medium text-body-md-medium text-primary">{{ $item['label'] }}</span>
                        <span class="font-caption-sm text-caption-sm text-outline">{{ $item['desc'] }}</span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Bagian 4: Catatan Teknis --}}
    <div class="bg-surface-container-lowest rounded-2xl p-space-lg md:p-space-xl shadow-sm flex flex-col gap-space-lg">
        <div class="pb-space-sm border-b border-surface-container">
            <h2 class="font-headline-sm text-headline-sm text-primary">Catatan Teknis</h2>
            <p class="font-caption-sm text-caption-sm text-on-surface-variant">Catatan penting bagi teknisi atau penghuni kamar.</p>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md text-primary" for="catatan">Deskripsi Khusus &amp; Catatan Fasilitator</label>
            <textarea id="catatan" name="catatan" rows="3" placeholder="Tuliskan keterangan sirkulasi udara, daya MCB, atau catatan teknis..." class="w-full bg-surface-container-low p-space-md rounded-xl font-body-md text-body-md text-primary outline-none focus:bg-surface-container-lowest transition-colors">{{ old('catatan', $kamar?->catatan) }}</textarea>
        </div>
    </div>
</div>
