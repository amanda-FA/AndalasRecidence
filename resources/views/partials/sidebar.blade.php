@php
    $user = auth()->user();
    $roleLabel = $user ? ($user->getRoleNames()->first() ?? 'Pengguna') : 'Tamu';
    $activeClass = 'bg-secondary text-on-secondary shadow-brand-active font-body-md-medium';
    $inactiveClass = 'text-primary-fixed hover:bg-secondary/40 hover:text-surface-container-lowest';
@endphp

<aside id="app-sidebar"
       class="fixed left-0 top-0 h-full w-64 bg-primary-container z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)] -translate-x-full lg:translate-x-0 transition-transform duration-200">
    <div class="flex flex-col overflow-y-auto">
        {{-- Brand Header --}}
        <div class="h-20 px-space-lg flex items-center justify-between bg-primary/20 shrink-0">
            <a href="{{ url('/') }}" class="flex items-center gap-space-sm">
                <img src="{{ asset('images/logo_andalasresidence.png') }}" alt="Logo Andalas Residence" width="48" height="48" class="w-12 h-12 rounded-xl bg-primary-container object-contain shrink-0">

                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm text-surface-container-lowest leading-none">
                        Andalas
                    </span>
                    <span class="font-caption-sm text-caption-sm text-secondary-fixed-dim uppercase tracking-wider">
                        Residence
                    </span>
                </div>
            </a>

            <button type="button"
                    class="lg:hidden text-primary-fixed hover:text-on-primary p-1"
                    onclick="toggleSidebar(false)"
                    aria-label="Tutup Menu">
                <span class="material-symbols-outlined text-[22px]">
                    close
                </span>
            </button>
        </div>

        {{-- Navigation Label --}}
        <div class="px-space-md py-space-sm">
            <div class="px-space-sm py-space-xs text-secondary-fixed-dim font-caption-sm-medium text-caption-sm-medium uppercase tracking-wider">
                Navigasi Utama
            </div>
        </div>

        {{-- Navigation Links --}}
        <nav class="flex flex-col px-space-sm gap-space-xs">
            {{-- Menu Pimpinan --}}
            @role('Pimpinan')
                @if (Route::has('dashboard.okupansi'))
                    <a href="{{ route('dashboard.okupansi') }}"
                       class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('dashboard.okupansi') ? $activeClass : $inactiveClass }}">
                        <span class="material-symbols-outlined text-[20px]">
                            space_dashboard
                        </span>
                        <span class="font-body-md text-body-md">
                            Dashboard Okupansi
                        </span>
                    </a>
                @endif
            @endrole

            {{-- Menu Fasilitator --}}
            @role('Fasilitator')
                @if (Route::has('gedung.index'))
                    <a href="{{ route('gedung.index') }}"
                       class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('gedung.*') ? $activeClass : $inactiveClass }}">
                        <span class="material-symbols-outlined text-[20px]">
                            domain
                        </span>
                        <span class="font-body-md text-body-md">
                            Manajemen Gedung
                        </span>
                    </a>
                @endif

                @if (Route::has('kamar.index'))
                    <a href="{{ route('kamar.index') }}"
                       class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('kamar.*') ? $activeClass : $inactiveClass }}">
                        <span class="material-symbols-outlined text-[20px]">
                            bed
                        </span>
                        <span class="font-body-md text-body-md">
                            Manajemen Kamar
                        </span>
                    </a>
                @endif
            @endrole

            {{-- Menu Admin Layanan --}}
            @role('Admin Layanan')
                @if (Route::has('periode.index'))
                    <a href="{{ route('periode.index') }}"
                       class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('periode.*') ? $activeClass : $inactiveClass }}">
                        <span class="material-symbols-outlined text-[20px]">
                            date_range
                        </span>
                        <span class="font-body-md text-body-md">
                            Periode Pendaftaran
                        </span>
                    </a>
                @endif

                @if (Route::has('verifikasi-pendaftaran.index'))
                    <a href="{{ route('verifikasi-pendaftaran.index') }}"
                       class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('verifikasi-pendaftaran.*') ? $activeClass : $inactiveClass }}">
                        <span class="material-symbols-outlined text-[20px]">
                            fact_check
                        </span>
                        <span class="font-body-md text-body-md">
                            Verifikasi Pendaftaran
                        </span>
                    </a>
                @endif
            @endrole

            {{-- Menu Client --}}
            @role('Client')
                @if (Route::has('pendaftaran.create'))
                    <a href="{{ route('pendaftaran.create') }}"
                    class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('pendaftaran.*') ? $activeClass : $inactiveClass }}">
                        <span class="material-symbols-outlined text-[20px]">
                            assignment
                        </span>
                        <span class="font-body-md text-body-md">
                            Pendaftaran Hunian
                        </span>
                    </a>
                @endif

                @if (Route::has('penempatan.index'))
                    <a href="{{ route('penempatan.index') }}"
                       class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('penempatan.*') ? $activeClass : $inactiveClass }}">
                        <span class="material-symbols-outlined text-[20px]">
                            meeting_room
                        </span>
                        <span class="font-body-md text-body-md">
                            Penempatan Kamar
                        </span>
                    </a>
                @endif

                @if (Route::has('kerusakan.index'))
                    <a href="{{ route('kerusakan.index') }}"
                       class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('kerusakan.*') ? $activeClass : $inactiveClass }}">
                        <span class="material-symbols-outlined text-[20px]">build_circle</span>
                        <span class="font-body-md text-body-md">Lapor Kerusakan</span>
                    </a>
                @endif

                @if (Route::has('checkout.index'))
                    <a href="{{ route('checkout.index') }}"
                       class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('checkout.*') || request()->routeIs('client.checkout*') ? $activeClass : $inactiveClass }}">
                        <span class="material-symbols-outlined text-[20px]">assignment_return</span>
                        <span class="font-body-md text-body-md">Check-out &amp; Bebas Asrama</span>
                    </a>
                @endif
            @endrole

            @if (Route::has('pembayaran.index'))
                <a href="{{ route('pembayaran.index') }}"
                class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all {{ request()->routeIs('pembayaran.*') ? $activeClass : $inactiveClass }}">
                    <span class="material-symbols-outlined text-[20px]">
                        payments
                    </span>
                    <span class="font-body-md text-body-md">
                        Pembayaran
                    </span>
                </a>
            @endif

            @yield('sidebar-items')
        </nav>
    </div>

    {{-- Bottom Logout --}}
    @auth
        <div class="p-space-md">
            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit"
                        class="w-full flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-primary/40 hover:bg-error text-primary-fixed hover:text-on-error font-caption-sm-medium text-caption-sm-medium transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">
                        logout
                    </span>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    @endauth
</aside>
