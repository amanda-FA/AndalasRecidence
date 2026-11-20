@extends('layouts.app')
@section('title', 'Pilih Gedung dan Kamar - Verifikasi Pendaftaran')
@section('content')
<div class="max-w-[1500px] mx-auto space-y-6">
    <a href="{{ route('verifikasi-pendaftaran.show',$registration) }}" class="inline-flex items-center gap-2 font-semibold text-secondary"><span class="material-symbols-outlined">arrow_back</span>Kembali ke detail verifikasi</a>
    <header><p class="text-caption-sm uppercase text-secondary font-semibold">Verifikasi dan Penempatan Hunian</p><h1 class="font-headline-md text-primary mt-2">Pilih Gedung, Kamar &amp; Bed</h1><p class="text-on-surface-variant mt-2">{{ $registration->client->pengguna->nama_lengkap }} / {{ $registration->client->jenisClient->jenis }}</p></header>
    @if ($errors->any())<div class="rounded-xl bg-error-container text-on-error-container p-4" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <section class="bg-white rounded-2xl border border-outline-variant/20 p-6">
        <h2 class="font-title-md text-primary mb-5">1. Atur Periode Hunian</h2>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-end">
            <div><label for="tanggal_masuk" class="block text-caption-sm mb-2">Tanggal Masuk (Check-in)</label><input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ $start }}" required min="{{ today()->toDateString() }}" class="w-full rounded-xl border border-outline-variant/40 p-3"></div>
            <div><label for="rencana_keluar" class="block text-caption-sm mb-2">Tanggal Keluar (Check-out)</label><input type="date" id="rencana_keluar" name="rencana_keluar" value="{{ $end }}" required class="w-full rounded-xl border border-outline-variant/40 p-3"></div>
            <div><label for="id_gedung" class="block text-caption-sm mb-2">Gedung</label><select id="id_gedung" name="id_gedung" required class="w-full rounded-xl border border-outline-variant/40 p-3">@forelse ($buildings as $item)<option value="{{ $item->id_gedung }}" @selected($building?->id_gedung === $item->id_gedung)>{{ $item->nama_gedung }}</option>@empty<option value="">Belum ada gedung yang sesuai</option>@endforelse</select></div>
            <div class="flex flex-wrap items-center gap-3"><label class="text-caption-sm"><input type="checkbox" name="tersedia" value="1" @checked(request()->boolean('tersedia'))> Hanya tersedia</label><button class="bg-secondary text-white rounded-xl px-4 py-3 font-semibold cursor-pointer">Terapkan</button></div>
        </form>
        <p class="text-caption-sm text-on-surface-variant mt-4">Ketersediaan bed mengikuti rentang tanggal yang diterapkan. Gedung disaring sesuai peruntukan jenis kelamin client.</p>
    </section>
    <form method="POST" action="{{ route('verifikasi-pendaftaran.assign',$registration) }}" class="space-y-6">
        @csrf
        <input type="hidden" name="id_gedung" value="{{ $building?->id_gedung }}"><input type="hidden" name="tanggal_masuk" value="{{ $start }}"><input type="hidden" name="rencana_keluar" value="{{ $end }}">
        @include('verifikasi-pendaftaran._jadwal-kunci')
        <section class="bg-white rounded-2xl border border-outline-variant/20 overflow-hidden">
            <div class="p-6 bg-surface-container-low flex flex-wrap justify-between items-center gap-3"><h2 class="font-title-md text-primary">3. Pilih Kamar &amp; Posisi Bed</h2><span class="text-caption-sm text-secondary font-semibold">{{ $building?->nama_gedung ?? 'Belum ada gedung' }}</span></div>
            <div class="p-6 space-y-8">
            @forelse ($groups as $rooms)
                @php $type = $rooms->first()->getRelation('tipeKamar'); $capacity = (int)($type?->kapasitas ?? 0); @endphp
                <div><div class="flex flex-wrap items-center justify-between gap-2 mb-4 border-b border-surface-container pb-3"><h3 class="font-semibold text-primary">{{ $type?->nama_tipe ?? 'Tipe belum tercatat' }}</h3><span class="text-caption-sm text-on-surface-variant">Kapasitas {{ $capacity }} penghuni per kamar</span></div><p class="text-caption-sm text-on-surface-variant mb-4">{{ $type?->deskripsi }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    @foreach ($rooms as $room)
                    @php $occupied = $room->occupied_beds; $available = $room->status_kamar === 'tersedia' && count($occupied) < $capacity; @endphp
                    <article class="rounded-2xl border-2 {{ $available ? 'border-outline-variant/40 bg-white' : 'border-outline-variant/20 bg-surface-container-low' }} p-4 has-checked:border-secondary has-checked:bg-surface-container-low has-checked:ring-2 has-checked:ring-secondary/20">
                        <div class="flex justify-between gap-2 items-center mb-4"><h4 class="font-headline-sm text-primary">{{ $room->nomor_kamar }}</h4><span class="text-[10px] rounded-lg bg-surface-container px-2 py-1 text-secondary font-semibold">{{ $room->unrecorded_beds ? 'Perlu Pendataan' : ($room->status_kamar !== 'tersedia' ? ucfirst($room->status_kamar) : ($available ? 'Sisa '.max(0,$capacity-count($occupied)).' bed' : 'Penuh')) }}</span></div>
                        @if($room->unrecorded_beds)<p class="text-caption-sm text-on-surface-variant mb-3">Posisi bed penghuni lama belum dicatat.</p>@endif<div class="space-y-2">@for ($bed=1; $bed <= $capacity; $bed++)
                            @php $free = $room->status_kamar === 'tersedia' && !in_array($bed,$occupied,true) && $available; @endphp
                            <label class="flex items-center justify-between gap-2 p-2.5 rounded-lg text-caption-sm {{ $free ? 'bg-surface-container-low text-secondary cursor-pointer hover:bg-surface-container' : 'bg-surface-container/60 text-outline cursor-not-allowed' }} has-checked:bg-secondary has-checked:text-white"><span class="flex items-center gap-2"><input type="radio" name="pilihan_bed" value="{{ $room->id_kamar }}:{{ $bed }}" @disabled(!$free) @checked(old('pilihan_bed') === $room->id_kamar.':'.$bed) required class="accent-secondary">Bed {{ chr(64+$bed) }}</span><span class="font-semibold">{{ $room->unrecorded_beds ? 'Belum tercatat' : ($free ? 'Tersedia' : ($room->status_kamar !== 'tersedia' ? 'Tidak tersedia' : 'Terisi')) }}</span></label>
                        @endfor</div>
                    </article>
                    @endforeach
                </div></div>
            @empty<div class="text-center p-10 text-on-surface-variant">Tidak ada kamar yang sesuai filter ini. Coba pilih gedung atau rentang tanggal lain.</div>@endforelse
            </div>
        </section>
        <div class="bg-white rounded-2xl border border-outline-variant/20 p-5 flex flex-wrap justify-between gap-4 items-center"><p class="text-caption-sm text-on-surface-variant">Verifikasi dan penempatan disimpan bersama setelah pengecekan ulang ketersediaan bed.</p><button @disabled(!$building || !$hasAvailableBed) class="px-6 py-3 rounded-xl bg-secondary text-white font-semibold cursor-pointer disabled:opacity-50">Simpan Penempatan &amp; Verifikasi</button></div>
    </form>
</div>
@endsection
