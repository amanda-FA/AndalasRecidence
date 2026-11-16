@php
    $user = auth()->user();
    $roleLabel = $user ? ($user->getRoleNames()->first() ?? 'Pengguna') : 'Tamu';
@endphp

<header class="fixed top-0 left-0 lg:left-64 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-30 px-space-md lg:px-space-xl flex items-center justify-between">
    <div class="flex items-center gap-space-md">
        {{-- Mobile Menu Toggle --}}
        <button type="button" onclick="toggleSidebar(true)" class="lg:hidden w-10 h-10 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface flex items-center justify-center" aria-label="Buka Menu">
            <span class="material-symbols-outlined text-[22px]">menu</span>
        </button>

        {{-- Optional Left Slot (e.g. Periode Akademik selector) --}}
        @hasSection('navbar-left')
            @yield('navbar-left')
        @endif
    </div>

    <div class="flex items-center gap-space-md lg:gap-space-lg">
        @yield('navbar-actions')

        <div class="h-8 w-[1px] bg-outline-variant/40 hidden sm:block"></div>

        <div class="flex items-center gap-space-sm">
            <div class="flex-col text-right hidden sm:flex">
                <span class="font-label-md text-label-md text-on-surface">{{ $user?->name ?? 'Pengguna' }}</span>
                <span class="font-caption-sm text-caption-sm text-on-surface-variant">{{ $roleLabel }}</span>
            </div>
            <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
            </div>
        </div>
    </div>
</header>
