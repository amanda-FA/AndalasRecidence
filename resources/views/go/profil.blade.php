@extends('layouts.app')

@section('title', 'Profil General Officer')

@section('navbar-left')
    <span class="material-symbols-outlined">person</span>
    <span class="font-title-md">Profil Saya</span>
@endsection

@section('content')
<div class="bg-surface-container-lowest min-h-screen" style="padding: var(--space-lg);">

    {{-- Header --}}
    <div style="margin-bottom: var(--space-xl);">
        <h1 class="font-headline-lg text-primary" style="margin-bottom: var(--space-xs);">Profil Saya</h1>
        <p class="font-body-md text-on-surface-variant">Kelola informasi pribadi dan keamanan akun Anda.</p>
    </div>

    {{-- Identity Card --}}
    <div class="bg-primary-container" style="border-radius: 16px; padding: var(--space-xl); margin-bottom: var(--space-xl); display: flex; align-items: center; gap: var(--space-lg); flex-wrap: wrap;">
        <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <span class="font-headline-lg text-surface-container-lowest">{{ strtoupper(substr(auth()->user()->nama_lengkap ?? 'G', 0, 1)) }}</span>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <h2 class="font-headline-sm text-primary" style="margin-bottom: var(--space-xs);">{{ auth()->user()->nama_lengkap ?? '-' }}</h2>
            <p class="font-body-md text-on-surface-variant" style="margin-bottom: var(--space-sm);">{{ auth()->user()->email ?? '-' }}</p>
            <div style="display: flex; gap: var(--space-sm); flex-wrap: wrap;">
                <span class="font-label-md bg-secondary text-on-secondary" style="padding: 4px 14px; border-radius: 999px;">General Officer</span>
                <span class="font-label-md" style="background: var(--success-container); color: var(--on-success-container); padding: 4px 14px; border-radius: 999px;">Aktif</span>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: var(--space-xl);">

        {{-- Contact Info Form --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); overflow: hidden;">
            <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant);">
                <h3 class="font-title-md text-primary" style="display: flex; align-items: center; gap: var(--space-sm);">
                    <span class="material-symbols-outlined" style="font-size: 22px;">badge</span>
                    Informasi Kontak
                </h3>
            </div>

            <form action="{{ route('go.profil.update') }}" method="POST" style="padding: var(--space-lg); display: flex; flex-direction: column; gap: var(--space-md);">
                @csrf
                @method('PUT')

                <div>
                    <label class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', auth()->user()->nama_lengkap) }}" class="font-body-md text-primary" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);" required>
                    @error('nama_lengkap')
                        <span class="font-caption-sm" style="color: var(--error); margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">No. Handphone</label>
                    <input type="tel" name="no_hp" value="{{ old('no_hp', auth()->user()->no_hp) }}" class="font-body-md text-primary" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
                    @error('no_hp')
                        <span class="font-caption-sm" style="color: var(--error); margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="font-body-md text-primary" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);" required>
                    @error('email')
                        <span class="font-caption-sm" style="color: var(--error); margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="bg-secondary text-on-secondary font-label-md" style="display: inline-flex; align-items: center; justify-content: center; gap: var(--space-xs); padding: 10px 20px; border-radius: 999px; border: none; cursor: pointer; align-self: flex-start;">
                    <span class="material-symbols-outlined" style="font-size: 20px;">save</span>
                    Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- Password Change Form --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); overflow: hidden;">
            <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant);">
                <h3 class="font-title-md text-primary" style="display: flex; align-items: center; gap: var(--space-sm);">
                    <span class="material-symbols-outlined" style="font-size: 22px;">lock</span>
                    Ubah Password
                </h3>
            </div>

            <form action="{{ route('go.profil.password') }}" method="POST" style="padding: var(--space-lg); display: flex; flex-direction: column; gap: var(--space-md);">
                @csrf
                @method('PUT')

                <div>
                    <label class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Password Saat Ini</label>
                    <input type="password" name="current_password" class="font-body-md text-primary" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);" required>
                    @error('current_password')
                        <span class="font-caption-sm" style="color: var(--error); margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Password Baru</label>
                    <input type="password" name="password" class="font-body-md text-primary" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);" required>
                    @error('password')
                        <span class="font-caption-sm" style="color: var(--error); margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="font-body-md text-primary" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);" required>
                </div>

                <button type="submit" class="bg-secondary text-on-secondary font-label-md" style="display: inline-flex; align-items: center; justify-content: center; gap: var(--space-xs); padding: 10px 20px; border-radius: 999px; border: none; cursor: pointer; align-self: flex-start;">
                    <span class="material-symbols-outlined" style="font-size: 20px;">key</span>
                    Ubah Password
                </button>
            </form>
        </div>
    </div>

    {{-- Logout --}}
    <div style="margin-top: var(--space-xl); padding-top: var(--space-lg); border-top: 1px solid var(--outline-variant);">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="font-label-md" style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 10px 20px; border-radius: 999px; border: 1px solid var(--error); color: var(--error); background: transparent; cursor: pointer;">
                <span class="material-symbols-outlined" style="font-size: 20px;">logout</span>
                Keluar dari Akun
            </button>
        </form>
    </div>

</div>
@endsection
