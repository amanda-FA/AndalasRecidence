<!DOCTYPE html>
<html lang="id" class="landing-document">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Temukan informasi hunian, fasilitas, kamar, dan periode pendaftaran Andalas Residence di lingkungan Universitas Andalas.">
    <title>@yield('title', 'Andalas Residence — Hunian untuk Langkah Besarmu')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-page" data-landing-page>
    <a class="landing-skip" href="#main-content">Langsung ke konten</a>
    <header id="landing-header" class="landing-header {{ request()->routeIs('home') ? '' : 'is-scrolled' }}" data-hero="{{ request()->routeIs('home') ? 'true' : 'false' }}">
        <div class="landing-container landing-nav-row">
            <a href="{{ route('home') }}" class="landing-brand" aria-label="Andalas Residence, Beranda"><img src="{{ asset('images/logo_andalasresidence.png') }}" alt="Logo Andalas Residence UNAND" class="landing-brand-logo" width="1208" height="1302" decoding="async"><span><strong>Andalas<span class="landing-brand-dot">.</span></strong><small>RESIDENCE</small></span></a>
            <nav id="landing-navigation" class="landing-navigation" aria-label="Navigasi utama">
                @foreach (['beranda' => 'Beranda', 'hunian' => 'Hunian', 'pendaftaran' => 'Pendaftaran Hunian', 'fitur' => 'Fitur Unggulan', 'tentang' => 'Tentang Kami'] as $id => $label)
                    <a href="{{ route('home') }}#{{ $id }}" data-section="{{ $id }}" @if ($id === 'hunian' && request()->routeIs('hunian.*')) aria-current="location" @endif>{{ $label }}</a>
                @endforeach
                <div class="landing-mobile-auth">@auth<a href="{{ route('portal') }}">Buka Portal</a>@else<a href="{{ route('login') }}">Masuk</a><a href="{{ route('register') }}">Daftar Akun</a>@endauth</div>
            </nav>
            <div class="landing-header-actions">@auth<a class="landing-button landing-button-small" href="{{ route('portal') }}">Buka Portal <span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span></a>@else<a class="landing-login" href="{{ route('login') }}">Masuk</a><a class="landing-button landing-button-small" href="{{ route('register') }}">Daftar <span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span></a>@endauth</div>
            <button id="landing-menu-toggle" type="button" class="landing-menu-toggle" aria-expanded="false" aria-controls="landing-navigation" aria-label="Buka menu navigasi"><span class="material-symbols-outlined" aria-hidden="true">menu</span></button>
        </div>
    </header>
    <main id="main-content">@yield('content')</main>
    <footer class="landing-footer">
        <div class="landing-container landing-footer-grid">
            <div><a href="{{ route('home') }}" class="landing-brand"><img src="{{ asset('images/logo_andalasresidence.png') }}" alt="Logo Andalas Residence UNAND" class="landing-brand-logo" width="1208" height="1302" loading="lazy" decoding="async"><span><strong>Andalas<span class="landing-brand-dot">.</span></strong><small>RESIDENCE</small></span></a><p>Tempat beristirahat, bertumbuh,<br>dan memulai cerita di kampus.</p><span class="landing-footer-university">Universitas Andalas</span></div>
            <div><h2>Jelajahi</h2><a href="{{ route('hunian.index') }}">Gedung &amp; kamar</a><a href="{{ route('home') }}#pendaftaran">Pendaftaran hunian</a><a href="{{ route('home') }}#fitur">Layanan penghuni</a></div>
            <div><h2>Akses Portal</h2>@auth<a href="{{ route('portal') }}">Buka sistem</a>@else<a href="{{ route('login') }}">Masuk ke akun</a><a href="{{ route('register') }}">Buat akun Client</a>@endauth<a href="{{ route('home') }}#tentang">Tentang Andalas Residence</a></div>
            <div><h2>Temui Kami</h2><p>Lingkungan Universitas Andalas<br>Limau Manis, Padang<br>Sumatera Barat</p><a href="mailto:asrama@unand.ac.id" class="landing-footer-email">asrama@unand.ac.id <span aria-hidden="true">↗</span></a></div>
        </div>
        <div class="landing-container landing-footer-bottom"><p>© {{ date('Y') }} Andalas Residence. Universitas Andalas.</p><p>Sistem Informasi Manajemen Kamar &amp; Layanan Hunian</p><a href="{{ route('home') }}#beranda">Kembali ke atas ↑</a></div>
    </footer>
</body>
</html>
