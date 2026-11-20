@extends('layouts.app')

@section('title', 'Edit Gedung - Andalas Residence')

@section('content')
<div class="flex flex-col gap-space-xl max-w-[1200px] mx-auto w-full">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md">
        <div class="flex flex-col gap-space-xs">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-on-surface-variant font-caption-sm text-caption-sm">
                <span>Fasilitas Kampus</span>
                <span class="material-symbols-outlined text-[14px] text-outline">chevron_right</span>
                <a href="{{ route('gedung.index') }}" class="hover:text-secondary transition-colors">Manajemen Gedung</a>
                <span class="material-symbols-outlined text-[14px] text-outline">chevron_right</span>
                <span class="font-caption-sm-medium text-caption-sm-medium text-secondary">Edit {{ $gedung->nama }}</span>
            </nav>
            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight mt-1">Edit Data Gedung</h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                Perbarui informasi master data, status operasional, atau kapasitas gedung <strong>{{ $gedung->nama }}</strong>.
            </p>
        </div>

        <a class="inline-flex items-center gap-space-xs px-space-md py-2.5 rounded-lg bg-surface-container-lowest text-primary shadow-sm hover:bg-surface-container-low transition-all font-label-md text-label-md shrink-0" href="{{ route('gedung.index') }}">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Manajemen Gedung</span>
        </a>
    </div>

    <form method="POST" action="{{ route('gedung.update', $gedung) }}" enctype="multipart/form-data" class="flex flex-col gap-space-lg">
        @csrf
        @method('PUT')

        @include('gedung._form', ['gedung' => $gedung])

        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-sm text-on-surface-variant font-caption-sm text-caption-sm">
                <span class="material-symbols-outlined text-[18px] text-secondary">sync</span>
                <span>Perubahan data akan langsung memperbarui perhitungan Dashboard Okupansi.</span>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-space-sm w-full sm:w-auto">
                <a href="{{ route('gedung.index') }}" class="px-space-lg py-2.5 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-all font-label-md text-label-md">
                    Batal
                </a>
                <button class="px-space-xl py-2.5 rounded-lg bg-secondary text-on-primary hover:bg-on-secondary-fixed-variant shadow-sm hover:shadow transition-all font-label-md text-label-md flex items-center gap-space-xs cursor-pointer" type="submit">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
