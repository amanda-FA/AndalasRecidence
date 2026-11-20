@php
    $oldFacilities = session()->hasOldInput('fasilitas_present') ? old('fasilitas', []) : ($selectedFacilityIds ?? []);
    $selectedIds = array_map('strval', is_array($oldFacilities) ? $oldFacilities : []);
@endphp
<section data-facility-selector data-facility-scope="{{ $facilityScope }}" data-store-url="{{ route('pilihan-fasilitas.store') }}" class="bg-surface-container-lowest rounded-2xl p-space-lg md:p-space-xl shadow-sm flex flex-col gap-space-md" aria-labelledby="facility-selection-title">
    <header class="flex flex-wrap items-center justify-between gap-3 pb-space-md border-b border-surface-container">
        <div><h2 id="facility-selection-title" class="font-headline-sm text-headline-sm text-primary">{{ $facilityTitle }}</h2><p class="text-caption-sm text-on-surface-variant mt-1">{{ $facilityDescription }}</p></div>
        <button type="button" data-add-facility class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-secondary text-on-secondary text-label-md cursor-pointer hover:bg-secondary/90"><span class="material-symbols-outlined text-[20px]" aria-hidden="true">add</span>Tambah Fasilitas</button>
    </header>
    <input type="hidden" name="fasilitas_present" value="1">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <label class="inline-flex items-center gap-2.5 cursor-pointer text-label-md text-primary font-medium"><input type="checkbox" data-facility-all class="w-4 h-4 rounded-full accent-secondary cursor-pointer">Pilih semua</label>
        <span data-facility-count class="text-caption-sm text-on-surface-variant" aria-live="polite"></span>
    </div>
    <div data-facility-options class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ($facilityOptions as $option)
            <label class="flex items-center gap-3 p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-colors cursor-pointer select-none">
                <input type="checkbox" name="fasilitas[]" value="{{ $option->id_fasilitas }}" @checked(in_array((string) $option->id_fasilitas, $selectedIds, true)) class="w-5 h-5 shrink-0 rounded accent-secondary cursor-pointer">
                <span class="text-body-md font-medium text-primary break-words">{{ $option->nama_fasilitas }}</span>
            </label>
        @endforeach
    </div>
    <p data-facility-empty @if ($facilityOptions->isNotEmpty()) hidden @endif class="text-caption-sm text-on-surface-variant">Belum ada pilihan fasilitas. Tambahkan nama fasilitas terlebih dahulu.</p>
    <p data-facility-message class="text-caption-sm text-success" role="status" hidden></p>
    <template data-facility-option-template><label class="flex items-center gap-3 p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-colors cursor-pointer select-none"><input type="checkbox" name="fasilitas[]" class="w-5 h-5 shrink-0 rounded accent-secondary cursor-pointer"><span class="text-body-md font-medium text-primary break-words"></span></label></template>
    <dialog data-facility-dialog aria-labelledby="add-facility-title" class="w-[min(480px,calc(100%_-_2rem))] m-auto p-0 rounded-2xl bg-white text-on-surface shadow-2xl backdrop:bg-primary/40 backdrop:backdrop-blur-sm">
        <div class="p-space-lg sm:p-space-xl flex flex-col gap-5">
            <header class="flex items-start justify-between gap-3"><div><h3 id="add-facility-title" class="font-headline-sm text-headline-sm text-primary">Tambah Nama Fasilitas</h3><p class="text-caption-sm text-on-surface-variant mt-2">Nama baru akan muncul pada daftar fasilitas {{ $facilityScope }}.</p></div><button type="button" data-close-facility class="w-8 h-8 rounded-lg bg-surface-container-low text-secondary inline-flex items-center justify-center shrink-0 cursor-pointer" aria-label="Tutup modal fasilitas"><span class="material-symbols-outlined text-[20px]" aria-hidden="true">close</span></button></header>
            <div><label for="new-facility-name" class="block text-label-md font-medium text-primary mb-2">Nama Fasilitas <span class="text-error">*</span></label><input id="new-facility-name" data-facility-name type="text" maxlength="100" autocomplete="off" placeholder="Contoh: Dispenser Air Minum" class="w-full rounded-xl bg-surface-container-low px-4 py-3 outline-none focus:ring-2 focus:ring-secondary" aria-describedby="facility-modal-error"><p id="facility-modal-error" data-facility-error class="text-caption-sm text-error mt-2" role="alert" hidden></p></div>
            <div class="flex justify-end gap-2"><button type="button" data-close-facility class="rounded-xl bg-surface-container-low px-4 py-2.5 text-label-md cursor-pointer">Batal</button><button type="button" data-save-facility class="rounded-xl bg-secondary text-on-secondary px-4 py-2.5 text-label-md cursor-pointer disabled:opacity-60 disabled:cursor-wait">Tambah &amp; Pilih</button></div>
        </div>
    </dialog>
</section>
