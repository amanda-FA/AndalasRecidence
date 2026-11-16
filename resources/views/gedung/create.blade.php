@extends('layouts.app')

@section('title', 'Tambah Gedung Baru - Andalas Residence')

@section('content')
<div class="flex flex-col gap-space-xl max-w-[1200px] mx-auto w-full">
    {{-- Top Navigation & Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md">
        <div class="flex flex-col gap-space-xs">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-on-surface-variant font-caption-sm text-caption-sm">
                <span>Fasilitas Kampus</span>
                <span class="material-symbols-outlined text-[14px] text-outline">chevron_right</span>
                <a href="{{ route('gedung.index') }}" class="hover:text-secondary transition-colors">Manajemen Gedung</a>
                <span class="material-symbols-outlined text-[14px] text-outline">chevron_right</span>
                <span class="font-caption-sm-medium text-caption-sm-medium text-secondary">Tambah Gedung Baru</span>
            </nav>
            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight mt-1">Tambah Data Gedung Baru</h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                Lengkapi formulir pendaftaran identitas fisik dan master data gedung hunian asrama sebelum mengalokasikan kamar dan fasilitas.
            </p>
        </div>

        <a class="inline-flex items-center gap-space-xs px-space-md py-2.5 rounded-lg bg-surface-container-lowest text-primary shadow-sm hover:bg-surface-container-low transition-all font-label-md text-label-md shrink-0" href="{{ route('gedung.index') }}">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Manajemen Gedung</span>
        </a>
    </div>

    <form method="POST" action="{{ route('gedung.store') }}" enctype="multipart/form-data" class="flex flex-col gap-space-lg">
        @csrf

        @include('gedung._form')

        {{-- Action Footer --}}
        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between gap-space-md">
            <p class="text-on-surface-variant font-caption-sm text-caption-sm">
                Pastikan kode gedung unik dan kapasitas bed sesuai standar operasional.
            </p>
            <div class="flex flex-wrap items-center justify-end gap-space-sm w-full sm:w-auto">
                <a href="{{ route('gedung.index') }}" class="px-space-lg py-2.5 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-all font-label-md text-label-md">
                    Batal
                </a>
                <button class="px-space-xl py-2.5 rounded-lg bg-secondary text-on-primary hover:bg-[#4650C7] shadow-sm hover:shadow transition-all font-label-md text-label-md cursor-pointer" type="submit">
                    Simpan &amp; Daftarkan Gedung Baru
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
