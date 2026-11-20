@extends('layouts.app')
@section('title', 'Atur Periode Hunian dan Pengambilan Kunci - Andalas Residence')
@section('content')
<div class="max-w-[1400px] mx-auto space-y-6">
    <a href="{{ route('verifikasi-pendaftaran.show',$registration) }}" class="inline-flex items-center gap-2 font-semibold text-secondary"><span class="material-symbols-outlined">arrow_back</span>Kembali ke detail verifikasi</a>
    <header><p class="text-caption-sm uppercase tracking-wider text-secondary font-semibold">Verifikasi Pendaftaran</p><h1 class="font-headline-md text-primary mt-2">Atur Periode Hunian &amp; Pengambilan Kunci</h1><p class="text-on-surface-variant mt-2">{{ $registration->client->pengguna->nama_lengkap }} / {{ $registration->client->jenisClient->jenis }}</p></header>
    <section class="bg-white rounded-2xl border border-outline-variant/20 overflow-hidden" aria-labelledby="selected-room-title">
        <div class="flex items-center gap-3 bg-surface-container-low px-6 py-4"><span class="material-symbols-outlined text-secondary" aria-hidden="true">apartment</span><h2 id="selected-room-title" class="font-title-md text-primary">Gedung dan Kamar Pilihan Client</h2></div>
        <dl class="grid grid-cols-1 md:grid-cols-3 gap-5 p-6">
            <div><dt class="text-caption-sm text-on-surface-variant">Gedung</dt><dd class="font-semibold text-primary mt-2">{{ $registration->kamar->gedung->nama_gedung }}</dd></div>
            <div><dt class="text-caption-sm text-on-surface-variant">Nomor Kamar</dt><dd class="font-headline-sm text-primary mt-2">{{ $registration->kamar->nomor_kamar }}</dd></div>
            <div><dt class="text-caption-sm text-on-surface-variant">Tipe Kamar</dt><dd class="font-semibold text-primary mt-2">{{ $registration->kamar->getRelation('tipeKamar')?->nama_tipe ?? '-' }}</dd></div>
        </dl>
    </section>
    <form method="POST" action="{{ route('verifikasi-pendaftaran.jadwal.store',$registration) }}" class="space-y-6">
        @csrf
        <section class="bg-white rounded-2xl border border-outline-variant/20 p-6">
            <h2 class="font-title-md text-primary mb-5">1. Atur Periode Hunian</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label for="tanggal_masuk" class="block text-caption-sm mb-2">Tanggal Masuk (Check-in) *</label><input id="tanggal_masuk" name="tanggal_masuk" type="date" value="{{ old('tanggal_masuk',$start) }}" min="{{ today()->toDateString() }}" required class="w-full rounded-xl border border-outline-variant/40 p-3">@error('tanggal_masuk')<p class="text-error text-caption-sm mt-1">{{ $message }}</p>@enderror</div>
                <div><label for="rencana_keluar" class="block text-caption-sm mb-2">Tanggal Keluar (Check-out) *</label><input id="rencana_keluar" name="rencana_keluar" type="date" value="{{ old('rencana_keluar',$end) }}" required class="w-full rounded-xl border border-outline-variant/40 p-3">@error('rencana_keluar')<p class="text-error text-caption-sm mt-1">{{ $message }}</p>@enderror</div>
            </div>
        </section>
        @include('verifikasi-pendaftaran._jadwal-kunci', ['scheduleRecord' => $registration])
        <div class="rounded-2xl border border-outline-variant/20 bg-white p-5 flex flex-wrap justify-between items-center gap-4"><p class="text-caption-sm text-on-surface-variant">Periksa periode hunian dan informasi pengambilan kunci sebelum menyelesaikan verifikasi.</p><button class="rounded-xl bg-secondary text-on-secondary font-semibold px-6 py-3 cursor-pointer">Simpan Jadwal &amp; Verifikasi</button></div>
    </form>
</div>
@endsection
