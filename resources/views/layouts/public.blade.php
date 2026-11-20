<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Andalas Residence - Sistem Informasi Hunian & Layanan Kamar')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-background font-body-md text-on-surface antialiased min-h-screen flex flex-col justify-between">
    {{-- Public Top Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-surface/90 backdrop-blur-xl shadow-brand-header">
        <div class="h-20 max-w-[1280px] mx-auto px-margin-mobile md:px-margin flex items-center justify-between gap-space-md">
            <a href="{{ url('/') }}" class="flex items-center gap-space-sm">
                <div class="w-10 h-10 rounded-xl bg-primary-container flex items-center justify-center text-on-primary shadow-sm">
                    <span class="material-symbols-outlined text-[22px]">apartment</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm text-primary tracking-tight">Andalas Residence</span>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant hidden sm:block">Sistem Manajemen Kamar &amp; Layanan Hunian</span>
                </div>
            </a>

            @unless (View::hasSection('minimal'))
                <nav class="hidden lg:flex items-center gap-space-lg">
                    <a class="font-body-md text-body-md text-on-surface-variant hover:text-on-surface transition-colors" href="{{ url('/') }}">Beranda</a>
                    <a class="font-body-md text-body-md text-on-surface-variant hover:text-on-surface transition-colors" href="#">Gedung &amp; Kamar</a>
                    <a class="font-body-md text-body-md text-on-surface-variant hover:text-on-surface transition-colors" href="#">Alur Pendaftaran</a>
                    <a class="font-body-md text-body-md text-on-surface-variant hover:text-on-surface transition-colors" href="#">Fasilitas</a>
                    <a class="font-body-md text-body-md text-on-surface-variant hover:text-on-surface transition-colors" href="#">Bantuan</a>
                </nav>
            @endunless

            <div class="flex items-center gap-space-sm">
                @auth
                    @if (Route::has('dashboard.okupansi'))
                        <a class="inline-flex items-center px-space-md py-space-xs bg-secondary text-on-secondary font-label-md text-label-md rounded-lg shadow-brand-public hover:bg-on-secondary-fixed-variant transition-colors" href="{{ route('portal') }}">
                            Dashboard
                        </a>
                    @endif
                @else
                    @if (Route::has('login'))
                        <a class="{{ View::hasSection('minimal') ? 'inline-flex bg-secondary text-on-secondary hover:bg-on-secondary-fixed-variant shadow-brand-public px-5 py-2.5' : 'hidden sm:inline-flex text-secondary hover:text-on-secondary hover:bg-secondary px-space-md py-2' }} items-center font-label-md text-label-md rounded-lg transition-colors" href="{{ route('login') }}">
                            Masuk / Login
                        </a>
                    @endif
                    @unless (View::hasSection('minimal'))
                        @if (Route::has('register'))
                            <a class="inline-flex items-center px-space-md py-space-xs bg-secondary text-on-secondary font-label-md text-label-md rounded-lg shadow-brand-public hover:bg-on-secondary-fixed-variant transition-colors" href="{{ route('register') }}">
                                Daftar Akun
                            </a>
                        @endif
                    @endunless
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="w-full pt-20 bg-background flex-1">
        @yield('content')
    </main>

    @unless (View::hasSection('minimal'))
    {{-- Public Footer --}}
    <footer class="w-full bg-surface-container-low shadow-brand-footer mt-space-2xl">
        <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-space-2xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-space-xl">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-space-sm mb-space-sm">
                        <div class="w-8 h-8 rounded-lg bg-primary-container flex items-center justify-center text-on-primary">
                            <span class="material-symbols-outlined text-[18px]">apartment</span>
                        </div>
                        <span class="font-headline-sm text-headline-sm text-primary">Andalas Residence</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-space-md">
                        Kompleks Asrama Mahasiswa &amp; Hunian Kampus Universitas Andalas, Limau Manis, Padang, Sumatera Barat 25163.
                    </p>
                </div>
                <div>
                    <h4 class="font-title-md text-title-md text-on-surface mb-space-sm">Jam Operasional</h4>
                    <p class="font-body-md text-body-md text-on-surface-variant">Senin - Jumat: 08.00 - 16.00 WIB</p>
                    <p class="font-body-md text-body-md text-on-surface-variant">Layanan Meja Depan: 24 Jam</p>
                    <p class="font-body-md text-body-md text-on-surface-variant">Kunjungan Tamu: 09.00 - 21.00 WIB</p>
                </div>
                <div>
                    <h4 class="font-title-md text-title-md text-on-surface mb-space-sm">Pusat Kontak &amp; Bantuan</h4>
                    <p class="font-body-md text-body-md text-on-surface-variant">Telepon: (0751) 71181</p>
                    <p class="font-body-md text-body-md text-on-surface-variant">WhatsApp Helpdesk: +62 811-660-890</p>
                    <p class="font-body-md text-body-md text-on-surface-variant">Email: asrama@unand.ac.id</p>
                </div>
                <div>
                    <h4 class="font-title-md text-title-md text-on-surface mb-space-sm">Akses Cepat</h4>
                    <ul class="space-y-space-xs">
                        <li class="font-body-md text-body-md text-on-surface-variant hover:text-on-surface"><a href="#">Syarat Penghuni</a></li>
                        <li class="font-body-md text-body-md text-on-surface-variant hover:text-on-surface"><a href="#">Ketersediaan Unit</a></li>
                        <li class="font-body-md text-body-md text-on-surface-variant hover:text-on-surface"><a href="#">Pertanyaan Umum (FAQ)</a></li>
                        <li class="font-body-md text-body-md text-on-surface-variant hover:text-on-surface"><a href="{{ route('login') }}">Portal Internal Staf</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-space-xl pt-space-md flex flex-col sm:flex-row items-center justify-between text-on-surface-variant font-caption-sm text-caption-sm gap-space-sm">
                <p>&copy; {{ date('Y') }} Universitas Andalas. Hak Cipta Dilindungi Undang-Undang.</p>
                <p>Sistem Informasi Manajemen Kamar dan Layanan Hunian (SIM-KAS)</p>
            </div>
        </div>
    </footer>
    @endunless

    @stack('scripts')
</body>
</html>
