@extends('layouts.app')
@section('title', 'Verifikasi Pendaftaran - Andalas Residence')
@section('content')
<div class="space-y-6">
    <header><h1 class="font-headline-md text-primary">Verifikasi Pendaftaran</h1><p class="text-on-surface-variant mt-2">Tinjau data dan berkas calon penghuni, lalu proses verifikasi pendaftarannya.</p></header>
    @php
        $summaryCards = [
            'semua' => ['label' => 'Total Masuk', 'icon' => 'inbox', 'color' => 'text-primary', 'background' => 'bg-surface-container-low text-secondary'],
            'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'icon' => 'pending_actions', 'color' => 'text-secondary', 'background' => 'bg-surface-container-low text-secondary'],
            'perlu_perbaikan' => ['label' => 'Perlu Perbaikan', 'icon' => 'playlist_remove', 'color' => 'text-[#99620a]', 'background' => 'bg-[#fff3df] text-[#99620a]'],
            'terverifikasi' => ['label' => 'Terverifikasi', 'icon' => 'task_alt', 'color' => 'text-success', 'background' => 'bg-success-container text-success'],
            'ditolak' => ['label' => 'Ditolak / Batal', 'icon' => 'cancel', 'color' => 'text-error', 'background' => 'bg-error-container/50 text-error'],
        ];
    @endphp
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4" aria-label="Ringkasan verifikasi pendaftaran">
        @foreach ($summaryCards as $key => $card)
            @php
                $value = match ($key) {
                    'semua' => $counts->sum(),
                    'ditolak' => $counts->get('ditolak', 0) + $counts->get('batal', 0),
                    'menunggu_verifikasi' => $counts->get($key, 0) + $counts->get('menunggu', 0) + $counts->get('diajukan', 0),
                    default => $counts->get($key, 0),
                };
            @endphp
            <a href="{{ route('verifikasi-pendaftaran.index', array_merge(request()->except(['status','page','antrean_page','riwayat_page']), $key === 'semua' ? [] : ['status' => $key === 'ditolak' ? 'ditolak_batal' : $key])) }}"
               class="rounded-2xl bg-surface-container-lowest border border-outline-variant/10 p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between gap-3 min-w-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary">
                <div class="min-w-0">
                    <p class="text-[11px] leading-[17px] font-medium uppercase tracking-wider text-on-surface-variant min-h-[34px]">{{ $card['label'] }}</p>
                    <p class="text-[30px] leading-9 font-semibold mt-2 {{ $card['color'] }}">{{ number_format($value, 0, ',', '.') }}</p>
                </div>
                <span class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $card['background'] }}">
                    <span class="material-symbols-outlined text-[24px]" aria-hidden="true">{{ $card['icon'] }}</span>
                </span>
            </a>
        @endforeach
    </section>
        <form method="GET" class="bg-white rounded-2xl border border-outline-variant/20 p-5 flex flex-wrap items-end gap-3">
            <div class="min-w-44 flex-1"><label for="q" class="block text-caption-sm mb-2">Cari client</label><input id="q" name="q" value="{{ request('q') }}" maxlength="100" placeholder="Nama client" class="w-full rounded-xl bg-surface-container-low p-3"></div>
            <div><label for="jenis" class="block text-caption-sm mb-2">Jalur</label><select id="jenis" name="jenis" class="rounded-xl bg-surface-container-low p-3"><option value="">Semua Jalur</option>@foreach ($kinds as $kind)<option value="{{ $kind->id_jenis_client }}" @selected(request('jenis') == $kind->id_jenis_client)>{{ $kind->jenis }}</option>@endforeach</select></div>
            <div><label for="periode" class="block text-caption-sm mb-2">Periode</label><select id="periode" name="periode" class="rounded-xl bg-surface-container-low p-3"><option value="">Semua Periode</option>@foreach ($periods as $period)<option value="{{ $period->id_periode }}" @selected(request('periode') == $period->id_periode)>{{ $period->nama_periode }}</option>@endforeach</select></div>
            <div><label for="status" class="block text-caption-sm mb-2">Status</label><select id="status" name="status" class="rounded-xl bg-surface-container-low p-3"><option value="">Semua Status</option>@foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
            <button class="bg-secondary text-white rounded-xl px-5 py-3 cursor-pointer">Terapkan</button><a href="{{ route('verifikasi-pendaftaran.index') }}" class="rounded-xl bg-surface-container-low px-4 py-3">Reset</a>
        </form>
    @include('verifikasi-pendaftaran._table', ['records' => $waiting, 'tableId' => 'waiting', 'title' => 'Menunggu Verifikasi', 'description' => 'Urutan pendaftaran paling lama ke paling baru.', 'isHistory' => false])
    @include('verifikasi-pendaftaran._table', ['records' => $history, 'tableId' => 'history', 'title' => 'Riwayat Verifikasi', 'description' => 'Hasil pemeriksaan terbaru ditampilkan paling atas.', 'isHistory' => true])
</div>
@endsection
