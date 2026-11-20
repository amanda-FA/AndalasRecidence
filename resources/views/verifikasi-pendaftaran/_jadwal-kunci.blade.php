<section class="bg-white rounded-2xl border border-outline-variant/20 p-6">
    <h2 class="font-title-md text-primary mb-5">2. Jadwal Pengambilan Kunci</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach (['jadwal_kunci_mulai' => ['Mulai Pengambilan Kunci', 'datetime-local', $start.'T09:00'], 'jadwal_kunci_selesai' => ['Selesai Pengambilan Kunci', 'datetime-local', $start.'T12:00'], 'lokasi_kunci' => ['Lokasi Pengambilan Kunci', 'text', ''], 'penanggung_jawab' => ['Penanggung Jawab', 'text', ''], 'kontak_penanggung_jawab' => ['Kontak Penanggung Jawab (WhatsApp)', 'tel', '']] as $name => [$label,$type,$default])
            @php
                $saved = isset($scheduleRecord) ? $scheduleRecord->{$name} : null;
                if ($saved instanceof \DateTimeInterface) $saved = $saved->format('Y-m-d\TH:i');
            @endphp
            <div><label for="{{ $name }}" class="block text-caption-sm mb-2">{{ $label }} *</label>
                <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $saved ?? $default) }}" required maxlength="{{ $name === 'lokasi_kunci' ? 255 : ($name === 'penanggung_jawab' ? 150 : 30) }}" class="w-full rounded-xl border border-outline-variant/40 p-3" @error($name) aria-invalid="true" @enderror>
                @error($name)<p class="text-error text-caption-sm mt-1">{{ $message }}</p>@enderror
            </div>
        @endforeach
    </div>
    <p class="text-caption-sm text-on-surface-variant mt-4">Seluruh jadwal menggunakan WIB.</p>
</section>
