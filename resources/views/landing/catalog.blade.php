@extends('layouts.landing')
@section('title', 'Jelajahi Hunian - Andalas Residence')
@section('content')
<section class="landing-inner-intro"><div class="landing-container"><a href="{{ route('home') }}" class="landing-breadcrumb">Beranda <span aria-hidden="true">/</span> Hunian</a><span class="landing-eyebrow">PILIHAN HUNIAN ANDALAS RESIDENCE</span><h1>Temukan ruang<br>untuk <em>ceritamu.</em></h1><p>Jelajahi gedung dan kenali tipe kamar beserta fasilitas yang tersedia.</p></div></section>
<section class="landing-container landing-catalog-content">
    <form method="GET" action="{{ route('hunian.index') }}" class="landing-catalog-filter"><div><label for="catalog-search">Cari hunian</label><input id="catalog-search" name="q" type="search" maxlength="100" value="{{ request('q') }}" placeholder="Nama gedung..."></div><div><label for="catalog-type">Peruntukan hunian</label><select id="catalog-type" name="peruntukan"><option value="">Semua peruntukan</option>@foreach (['putra' => 'Khusus Putra', 'putri' => 'Khusus Putri', 'campuran' => 'Campuran / Pascasarjana', 'tamu' => 'Guest House / Tamu'] as $value => $label)<option value="{{ $value }}" @selected(request('peruntukan') === $value)>{{ $label }}</option>@endforeach</select></div><button type="submit" class="landing-button">Cari Hunian <span class="material-symbols-outlined" aria-hidden="true">search</span></button><a href="{{ route('hunian.index') }}" class="landing-text-link">Reset</a></form>
    @if ($errors->any())<div role="alert" class="landing-period-notice">{{ $errors->first() }}</div>@endif
    <p class="landing-result-count">{{ $buildings->count() }} gedung ditemukan</p><div class="residence-grid">@forelse ($buildings as $building) @include('landing._building-card') @empty<div class="landing-empty"><span class="material-symbols-outlined" aria-hidden="true">search_off</span><h2>Hunian belum ditemukan</h2><p>Sesuaikan pencarian atau pilih semua peruntukan.</p></div>@endforelse</div>
</section>
@endsection
