@extends('layouts.guest')

@section('title', 'Masuk Portal - Andalas Residence')

@section('content')
<div class="flex flex-col w-full">
    <div class="flex flex-col gap-space-lg">
        {{-- Header & Identity Section --}}
        <header class="flex flex-col items-center text-center gap-space-xs">
            <div class="relative flex items-center justify-center w-16 h-16 rounded-xl bg-primary-container text-on-primary shadow-sm mb-space-xs">
                <span class="material-symbols-outlined text-[32px]">apartment</span>
                <span class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-secondary text-[10px] text-on-secondary">
                    <span class="material-symbols-outlined text-[10px]">school</span>
                </span>
            </div>
            <div class="flex flex-col">
                <span class="font-label-md text-caption-sm text-secondary uppercase tracking-widest">Universitas Andalas</span>
                <h1 class="font-headline-sm text-headline-sm text-primary tracking-tight">Andalas Residence</h1>
            </div>
            <p class="font-body-md text-caption-sm text-on-surface-variant max-w-xs">
                Portal Terpadu Manajemen Hunian &amp; Asrama Mahasiswa Kampus Limau Manih
            </p>
        </header>

        <!-- {{-- Multi-Role Info Banner --}}
        <div class="px-space-md py-space-xs rounded-lg bg-surface-container-low text-center">
            <span class="font-caption-sm text-caption-sm text-on-surface-variant">
                Portal otomatis menyesuaikan akses <strong class="text-primary">Penghuni, Staf, Fasilitator, Teknisi, Admin, &amp; Pimpinan</strong>
            </span>
        </div> -->

        @if (session('status'))
            <div class="p-space-sm rounded-lg bg-[#2E9E5B]/10 text-[#1F7A43] border border-[#2E9E5B]/30 font-caption-sm text-caption-sm flex items-center gap-2" role="status">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Auth Method Panels --}}
        <div class="flex flex-col gap-space-md">
            {{-- SSO Method Panel --}}
            <div class="flex flex-col gap-space-md" id="panel-sso">
                <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-sm">
                    <div class="flex items-start gap-space-sm">
                        <span class="material-symbols-outlined text-secondary text-[22px] mt-0.5">account_circle</span>
                        <div class="flex flex-col">
                            <span class="font-title-md text-caption-sm-medium text-primary">Autentikasi Mandiri Civitas</span>
                            <p class="font-caption-sm text-caption-sm text-on-surface-variant">
                                Mahasiswa, Dosen, dan Tenaga Kependidikan aktif dapat langsung masuk menggunakan akun single sign-on resmi Universitas Andalas.
                            </p>
                        </div>
                    </div>
                </div>
                <button class="w-full flex items-center justify-center gap-space-sm py-3 px-space-md bg-primary-container hover:bg-primary text-on-primary rounded-lg font-label-md text-label-md shadow-sm transition-all duration-150 active:scale-[0.99] cursor-pointer" onclick="handleSsoNotice()" type="button">
                    <span class="material-symbols-outlined text-[20px]">vpn_key</span>
                    <span>Masuk dengan Akun SSO UNAND</span>
                </button>
                <p id="sso-notice" class="hidden text-center font-caption-sm text-caption-sm text-secondary bg-surface-container-low p-2 rounded-lg">
                    Integrasi SSO UNAND sedang dalam tahap pengembangan. Silakan gunakan tombol <strong>Gunakan Email &amp; Kata Sandi</strong> di bawah.
                </p>
                <div class="flex items-center gap-space-sm">
                    <div class="h-px bg-surface-variant flex-1"></div>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant uppercase tracking-wider">Atau masuk manual</span>
                    <div class="h-px bg-surface-variant flex-1"></div>
                </div>
                <button class="w-full flex items-center justify-center gap-space-sm py-3 px-space-md bg-surface-container hover:bg-surface-variant text-on-surface rounded-lg font-label-md text-label-md shadow-sm transition-all duration-150 active:scale-[0.99] cursor-pointer" onclick="switchAuthTab('cred')" type="button">
                    <span class="material-symbols-outlined text-[20px]">mail</span>
                    <span>Gunakan Email &amp; Kata Sandi</span>
                </button>
            </div>

            {{-- Manual Credentials Form Panel --}}
            <form class="hidden flex-col gap-space-md" id="panel-cred" method="POST" action="{{ route('login') }}">
                @csrf

                @if ($errors->any())
                    <div class="p-space-sm rounded-lg bg-error-container text-on-error-container font-caption-sm text-caption-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="flex flex-col gap-1">
                    <label class="font-caption-sm-medium text-caption-sm text-on-surface" for="email">Alamat Surel</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3 text-on-surface-variant text-[20px]">badge</span>
                        <input class="w-full pl-10 pr-3 py-2.5 rounded-lg bg-surface-container-lowest border border-outline-variant/50 text-on-surface font-body-md text-body-md placeholder:text-outline-variant focus:outline-none focus:border-secondary focus:bg-surface-container-low transition-colors"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Contoh: nama@unand.ac.id"
                               type="email"
                               required
                               autofocus>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between">
                        <label class="font-caption-sm-medium text-caption-sm text-on-surface" for="password">Kata Sandi</label>
                    </div>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3 text-on-surface-variant text-[20px]">lock</span>
                        <input class="w-full pl-10 pr-10 py-2.5 rounded-lg bg-surface-container-lowest border border-outline-variant/50 text-on-surface font-body-md text-body-md placeholder:text-outline-variant focus:outline-none focus:border-secondary focus:bg-surface-container-low transition-colors"
                               id="password"
                               name="password"
                               placeholder="Masukkan kata sandi portal"
                               type="password"
                               required>
                        <button class="absolute right-3 text-on-surface-variant hover:text-on-surface focus:outline-none flex items-center cursor-pointer" onclick="togglePasswordVisibility()" type="button">
                            <span class="material-symbols-outlined text-[20px]" id="pwd-icon">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input class="w-4 h-4 rounded text-secondary bg-surface-container-lowest focus:ring-0 cursor-pointer" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                        <span class="font-caption-sm text-caption-sm text-on-surface-variant">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button class="w-full flex items-center justify-center gap-space-xs py-3 px-space-md bg-secondary hover:bg-secondary-container hover:text-on-secondary-container text-on-secondary rounded-lg font-label-md text-label-md shadow-sm transition-all duration-150 active:scale-[0.99] cursor-pointer" type="submit">
                    <span>Masuk ke Sistem</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </button>

                <button class="w-full flex items-center justify-center gap-1.5 py-2 text-on-surface-variant hover:text-primary font-caption-sm-medium text-caption-sm transition-colors cursor-pointer" onclick="switchAuthTab('sso')" type="button">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke pilihan masuk</span>
                </button>
            </form>
        </div>

        {{-- Registration Link --}}
        @if (Route::has('register'))
            <div class="p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="font-caption-sm-medium text-caption-sm text-primary">Belum memiliki akun?</span>
                    <span class="font-caption-sm text-caption-sm text-on-surface-variant">Buat akun penghuni / client baru</span>
                </div>
                <a class="px-3 py-1.5 rounded-md bg-surface-container-lowest font-caption-sm-medium text-caption-sm text-secondary shadow-sm hover:bg-surface-container transition-colors" href="{{ route('register') }}">
                    Daftar Akun
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchAuthTab(type) {
        const ssoPanel = document.getElementById('panel-sso');
        const credPanel = document.getElementById('panel-cred');

        if (type === 'sso') {
            ssoPanel.classList.remove('hidden');
            ssoPanel.classList.add('flex');
            credPanel.classList.add('hidden');
            credPanel.classList.remove('flex');
        } else {
            credPanel.classList.remove('hidden');
            credPanel.classList.add('flex');
            ssoPanel.classList.add('hidden');
            ssoPanel.classList.remove('flex');
        }
    }

    function togglePasswordVisibility() {
        const pwdInput = document.getElementById('password');
        const icon = document.getElementById('pwd-icon');
        if (pwdInput.type === 'password') {
            pwdInput.type = 'text';
            icon.innerText = 'visibility_off';
        } else {
            pwdInput.type = 'password';
            icon.innerText = 'visibility';
        }
    }

    function selectRole(button, roleName) {
        const buttons = document.querySelectorAll('.role-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
            btn.classList.add('text-on-surface-variant');
        });
        button.classList.add('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
        button.classList.remove('text-on-surface-variant');

        const roleBadge = document.getElementById('selected-role-badge');
        if (roleBadge) {
            roleBadge.innerText = roleName.split(' ')[0];
        }
    }

    function handleSsoNotice() {
        const notice = document.getElementById('sso-notice');
        if (notice) {
            notice.classList.remove('hidden');
        }
    }

    @if ($errors->any() || old('email'))
        switchAuthTab('cred');
    @endif
</script>
@endpush
