@extends('layouts.app')

@section('title', 'Tambah Kamar Baru - Andalas Residence')

@section('content')
<div class="max-w-6xl mx-auto w-full flex flex-col gap-space-lg">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
        <nav class="flex items-center gap-2 text-outline font-caption-sm-medium text-caption-sm-medium">
            <span>Fasilitas Kampus</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a class="hover:text-primary transition-colors text-on-surface-variant" href="{{ route('kamar.index') }}">Manajemen Kamar</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-secondary font-body-md-medium">Tambah Kamar Baru</span>
        </nav>
        <a class="inline-flex items-center gap-space-xs px-space-md py-2 rounded-lg bg-surface-container-lowest text-primary shadow-sm hover:bg-surface-container-low transition-all font-label-md text-label-md self-start sm:self-auto" href="{{ route('kamar.index') }}">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Manajemen Kamar</span>
        </a>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl p-space-lg md:p-space-xl shadow-sm">
        <!-- <div class="inline-flex items-center px-space-sm py-1 rounded-full bg-surface-container-high w-fit text-secondary font-caption-sm-medium text-caption-sm-medium mb-2">
            <span>Registrasi Unit Asrama Mahasiswa</span>
        </div> -->
        <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Tambah Data Kamar Baru</h1>
        <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mt-1">
            Lengkapi formulir spesifikasi unit kamar, peruntukan lantai, kapasitas bed, serta kelengkapan fasilitas residensial.
        </p>
    </div>

    <form method="POST" action="{{ route('kamar.store') }}" enctype="multipart/form-data" class="flex flex-col gap-space-lg">
        @csrf

        @include('kamar._form')

        <div class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-sm flex flex-col sm:flex-row items-center justify-between gap-space-md">
            <p class="text-outline font-caption-sm-medium text-caption-sm-medium">
                Data kamar akan langsung terhubung ke perhitungan okupansi gedung.
            </p>
            <div class="flex items-center gap-space-sm w-full sm:w-auto justify-end">
                <a href="{{ route('kamar.index') }}" class="px-space-md py-2.5 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors font-label-md text-label-md">
                    Batal
                </a>
                <button class="px-space-lg py-2.5 rounded-lg bg-secondary text-on-secondary hover:bg-secondary-container transition-all font-label-md text-label-md shadow-sm cursor-pointer" type="submit">
                    Simpan &amp; Daftarkan Kamar Baru
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
