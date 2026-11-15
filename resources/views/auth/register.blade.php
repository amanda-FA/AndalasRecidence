@extends('layouts.public')

@section('title', 'Buat Akun Client - Andalas Residence')
@section('minimal', true)

@section('content')
<div class="flex flex-col w-full relative overflow-hidden">
    {{-- Subtle Ambient Glow Background Ornaments --}}
    <div class="absolute -top-32 -left-32 w-[520px] h-[520px] bg-secondary-fixed/50 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute top-1/2 -right-40 w-[600px] h-[600px] bg-surface-container-high/60 rounded-full blur-3xl pointer-events-none -z-10"></div>

    {{-- Main Registration Card --}}
    <div class="max-w-[1280px] w-full mx-auto px-margin-mobile md:px-margin py-space-xl mb-space-2xl">
        <div class="grid grid-cols-1 gap-space-xl items-start max-w-2xl mx-auto">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl p-space-md md:p-space-xl relative">
                {{-- Card Header --}}
                <div class="pb-space-md mb-space-md bg-surface-container-low/50 -mx-space-md -mt-space-md p-space-md md:-mx-space-xl md:-mt-space-xl md:p-space-lg rounded-t-2xl">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <span class="font-label-md text-label-md text-secondary tracking-wide uppercase">Formulir Pendaftaran Residen</span>
                        <span class="font-caption-sm-medium text-caption-sm text-[#2E9E5B] bg-[#2E9E5B]/10 px-2.5 py-0.5 rounded-full flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#2E9E5B]"></span> Jalur Mahasiswa / Umum Aktif
                        </span>
                    </div>
                    <h1 class="font-headline-md text-headline-md text-primary">Buat Akun Residen Baru</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                        Lengkapi data autentikasi untuk memulai pemilihan gedung dan pemesanan kamar asrama Universitas Andalas.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-space-md p-space-md rounded-xl bg-error-container text-on-error-container flex flex-col gap-1" role="alert">
                        <div class="flex items-center gap-space-sm font-label-md text-label-md">
                            <span class="material-symbols-outlined text-[20px]">error</span>
                            <span>Mohon periksa kembali isian Anda:</span>
                        </div>
                        <ul class="list-disc list-inside font-caption-sm text-caption-sm pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Registration Form --}}
                <form class="space-y-space-md" id="resident-register-form" method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Kategori Pendaftar --}}
                    @php $selectedCategory = old('applicant_category', 'reguler'); @endphp
                    <div class="space-y-2 pb-space-xs">
                        <div class="flex items-center justify-between">
                            <label class="block font-label-md text-label-md text-primary">Kategori Pendaftar / Klien Hunian <span class="text-error">*</span></label>
                            <span class="font-caption-sm text-caption-sm text-on-surface-variant">Pilih status kepesertaan</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
                            <label class="category-card relative flex flex-col p-space-sm rounded-xl border {{ $selectedCategory === 'reguler' ? 'border-secondary bg-surface-container-low shadow-sm' : 'border-outline-variant bg-surface-container-lowest' }} hover:border-secondary cursor-pointer transition-all">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-secondary text-[20px]">school</span>
                                        <span class="font-title-md text-[14px] text-primary font-semibold">Mahasiswa Reguler</span>
                                    </div>
                                    <input {{ $selectedCategory === 'reguler' ? 'checked' : '' }} class="h-4 w-4 text-secondary focus:ring-secondary" name="applicant_category" type="radio" value="reguler">
                                </div>
                                <p class="font-caption-sm text-caption-sm text-on-surface-variant">Jalur SNBP, SNBT, &amp; Seleksi Mandiri</p>
                            </label>

                            <label class="category-card relative flex flex-col p-space-sm rounded-xl border {{ $selectedCategory === 'kipk' ? 'border-secondary bg-surface-container-low shadow-sm' : 'border-outline-variant bg-surface-container-lowest' }} hover:border-secondary cursor-pointer transition-all">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-outline text-[20px]">workspace_premium</span>
                                        <span class="font-title-md text-[14px] text-primary font-semibold">Mahasiswa KIP-K</span>
                                    </div>
                                    <input {{ $selectedCategory === 'kipk' ? 'checked' : '' }} class="h-4 w-4 text-secondary focus:ring-secondary" name="applicant_category" type="radio" value="kipk">
                                </div>
                                <p class="font-caption-sm text-caption-sm text-on-surface-variant">Beasiswa KIP-Kuliah &amp; Afirmasi Dikti</p>
                            </label>

                            <label class="category-card relative flex flex-col p-space-sm rounded-xl border {{ $selectedCategory === 'summer_course' ? 'border-secondary bg-surface-container-low shadow-sm' : 'border-outline-variant bg-surface-container-lowest' }} hover:border-secondary cursor-pointer transition-all">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-outline text-[20px]">travel_explore</span>
                                        <span class="font-title-md text-[14px] text-primary font-semibold">Summer Course</span>
                                    </div>
                                    <input {{ $selectedCategory === 'summer_course' ? 'checked' : '' }} class="h-4 w-4 text-secondary focus:ring-secondary" name="applicant_category" type="radio" value="summer_course">
                                </div>
                                <p class="font-caption-sm text-caption-sm text-on-surface-variant">Program Internasional &amp; Pertukaran</p>
                            </label>

                            <label class="category-card relative flex flex-col p-space-sm rounded-xl border {{ $selectedCategory === 'guest' ? 'border-secondary bg-surface-container-low shadow-sm' : 'border-outline-variant bg-surface-container-lowest' }} hover:border-secondary cursor-pointer transition-all">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-outline text-[20px]">apartment</span>
                                        <span class="font-title-md text-[14px] text-primary font-semibold">Non-Mahasiswa / Tamu</span>
                                    </div>
                                    <input {{ $selectedCategory === 'guest' ? 'checked' : '' }} class="h-4 w-4 text-secondary focus:ring-secondary" name="applicant_category" type="radio" value="guest">
                                </div>
                                <p class="font-caption-sm text-caption-sm text-on-surface-variant">Peneliti, Dosen Tamu &amp; Wisma Kampus</p>
                            </label>
                        </div>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="space-y-1">
                        <label class="block font-label-md text-label-md text-primary" for="reg-name">
                            Nama Lengkap (Sesuai KTP / KTM) <span class="text-error">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px] pointer-events-none">person</span>
                            <input class="w-full bg-surface-container-lowest border border-outline-variant/50 text-primary placeholder:text-outline/70 font-body-md text-body-md rounded-lg py-3 pl-11 pr-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-secondary transition-all"
                                   id="reg-name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Contoh: Muhammad Fadhil Pratama"
                                   required
                                   type="text">
                        </div>
                    </div>

                    {{-- NIM / KTP --}}
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="font-label-md text-label-md text-primary" for="reg-identifier">
                                Nomor Induk Mahasiswa (NIM) / Nomor KTP <span class="text-error">*</span>
                            </label>
                            <div class="group relative flex items-center cursor-pointer">
                                <span class="material-symbols-outlined text-outline text-[16px] hover:text-secondary">help</span>
                                <div class="absolute right-0 bottom-full mb-1 hidden group-hover:block w-64 p-2 bg-inverse-surface text-inverse-on-surface rounded-lg shadow-lg font-caption-sm text-caption-sm z-30">
                                    Gunakan No. KTP/NIK jika Anda mahasiswa baru yang belum mendapatkan nomor induk mahasiswa resmi.
                                </div>
                            </div>
                        </div>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px] pointer-events-none">badge</span>
                            <input class="w-full bg-surface-container-lowest border border-outline-variant/50 text-primary placeholder:text-outline/70 font-body-md text-body-md rounded-lg py-3 pl-11 pr-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-secondary transition-all font-mono"
                                   id="reg-identifier"
                                   name="identity_number"
                                   value="{{ old('identity_number') }}"
                                   placeholder="Contoh: 2411521008 atau 16 digit NIK KTP"
                                   required
                                   type="text">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="font-label-md text-label-md text-primary" for="reg-email">
                                Alamat Email Aktif <span class="text-error">*</span>
                            </label>
                            <span class="font-caption-sm text-caption-sm text-on-surface-variant">Disarankan format @unand.ac.id</span>
                        </div>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px] pointer-events-none">mail</span>
                            <input class="w-full bg-surface-container-lowest border border-outline-variant/50 text-primary placeholder:text-outline/70 font-body-md text-body-md rounded-lg py-3 pl-11 pr-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-secondary transition-all"
                                   id="reg-email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="fadhil.pratama@unand.ac.id"
                                   required
                                   type="email">
                        </div>
                    </div>

                    {{-- Nomor WhatsApp --}}
                    <div class="space-y-1">
                        <label class="block font-label-md text-label-md text-primary" for="reg-phone">
                            Nomor WhatsApp / Telepon Seluler <span class="text-error">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px] pointer-events-none">call</span>
                            <input class="w-full bg-surface-container-lowest border border-outline-variant/50 text-primary placeholder:text-outline/70 font-body-md text-body-md rounded-lg py-3 pl-11 pr-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-secondary transition-all font-mono"
                                   id="reg-phone"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   placeholder="Contoh: 0812-7788-9901"
                                   required
                                   type="tel">
                        </div>
                        <p class="font-caption-sm text-caption-sm text-on-surface-variant">Kode verifikasi &amp; nomor kamar akan diteruskan ke nomor WhatsApp ini.</p>
                    </div>

                    {{-- Password & Konfirmasi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md pt-space-xs">
                        <div class="space-y-1">
                            <label class="block font-label-md text-label-md text-primary" for="reg-password">
                                Kata Sandi <span class="text-error">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px] pointer-events-none">lock</span>
                                <input class="w-full bg-surface-container-lowest border border-outline-variant/50 text-primary placeholder:text-outline/70 font-body-md text-body-md rounded-lg py-3 pl-11 pr-10 shadow-sm focus:outline-none focus:ring-2 focus:ring-secondary transition-all"
                                       id="reg-password"
                                       name="password"
                                       oninput="updatePasswordStrength(this.value)"
                                       placeholder="Minimal 8 karakter"
                                       required
                                       type="password">
                                <button aria-label="Tampilkan sandi" class="absolute right-3 text-outline hover:text-primary transition-colors focus:outline-none cursor-pointer" onclick="togglePasswordVisibility('reg-password', 'icon-pass-eye')" type="button">
                                    <span class="material-symbols-outlined text-[20px]" id="icon-pass-eye">visibility</span>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block font-label-md text-label-md text-primary" for="reg-confirm-password">
                                Konfirmasi Kata Sandi <span class="text-error">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px] pointer-events-none">lock_reset</span>
                                <input class="w-full bg-surface-container-lowest border border-outline-variant/50 text-primary placeholder:text-outline/70 font-body-md text-body-md rounded-lg py-3 pl-11 pr-10 shadow-sm focus:outline-none focus:ring-2 focus:ring-secondary transition-all"
                                       id="reg-confirm-password"
                                       name="password_confirmation"
                                       oninput="checkPasswordMatch()"
                                       placeholder="Ulangi kata sandi"
                                       required
                                       type="password">
                                <span class="material-symbols-outlined absolute right-3 text-[#2E9E5B] text-[20px] hidden" id="match-indicator-icon">check_circle</span>
                            </div>
                        </div>
                    </div>

                    {{-- Password Strength Meter --}}
                    <div class="p-space-sm bg-surface-container-low rounded-xl space-y-2">
                        <div class="flex items-center justify-between font-caption-sm text-caption-sm">
                            <span class="text-on-surface-variant">Kekuatan Kata Sandi:</span>
                            <span class="font-caption-sm-medium text-outline" id="password-strength-text">Belum diisi</span>
                        </div>
                        <div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden flex gap-1">
                            <div class="h-full w-1/3 bg-outline/20 rounded-full transition-all duration-300" id="str-bar-1"></div>
                            <div class="h-full w-1/3 bg-outline/20 rounded-full transition-all duration-300" id="str-bar-2"></div>
                            <div class="h-full w-1/3 bg-outline/20 rounded-full transition-all duration-300" id="str-bar-3"></div>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 font-caption-sm text-caption-sm text-on-surface-variant">
                            <span class="flex items-center gap-1" id="req-len"><span class="material-symbols-outlined text-[14px] text-outline">circle</span> Min. 8 karakter</span>
                            <span class="flex items-center gap-1" id="req-num"><span class="material-symbols-outlined text-[14px] text-outline">circle</span> Kombinasi angka</span>
                            <span class="flex items-center gap-1" id="req-alpha"><span class="material-symbols-outlined text-[14px] text-outline">circle</span> Huruf besar &amp; kecil</span>
                        </div>
                    </div>

                    {{-- Agreement --}}
                    <div class="space-y-space-sm pt-space-xs">
                        <label class="flex items-start gap-space-sm cursor-pointer select-none">
                            <input class="mt-1 h-4 w-4 rounded text-secondary focus:ring-secondary bg-surface-container-lowest" id="agree-terms" required type="checkbox">
                            <span class="font-body-md text-body-md text-on-surface">
                                Saya menyetujui <a class="text-secondary font-body-md-medium hover:underline" href="#">Ketentuan Layanan</a> serta <a class="text-secondary font-body-md-medium hover:underline" href="#">Kebijakan Privasi Hunian</a> Andalas Residence.
                            </span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-space-sm">
                        <button class="w-full py-3.5 px-space-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-space-sm active:translate-y-0.5 cursor-pointer" id="btn-register" type="submit">
                            <span>Buat Akun &amp; Lanjutkan Pendaftaran</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </div>

                    {{-- Switch to Login --}}
                    <div class="text-center pt-space-xs">
                        <p class="font-body-md text-body-md text-on-surface-variant">
                            Sudah memiliki akun portal asrama?
                            <a class="text-secondary font-body-md-medium hover:underline" href="{{ route('login') }}">Masuk / Login di sini</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Highlight selected radio card
    document.querySelectorAll('input[name="applicant_category"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('border-secondary', 'bg-surface-container-low', 'shadow-sm');
                card.classList.add('border-outline-variant', 'bg-surface-container-lowest');
            });
            const parent = this.closest('.category-card');
            if (parent) {
                parent.classList.add('border-secondary', 'bg-surface-container-low', 'shadow-sm');
                parent.classList.remove('border-outline-variant', 'bg-surface-container-lowest');
            }
        });
    });

    function togglePasswordVisibility(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (!field || !icon) return;
        if (field.type === 'password') {
            field.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            field.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    function updatePasswordStrength(val) {
        const b1 = document.getElementById('str-bar-1');
        const b2 = document.getElementById('str-bar-2');
        const b3 = document.getElementById('str-bar-3');
        const text = document.getElementById('password-strength-text');

        const reqLen = document.getElementById('req-len');
        const reqNum = document.getElementById('req-num');
        const reqAlpha = document.getElementById('req-alpha');

        const hasLen = val.length >= 8;
        const hasNum = /\d/.test(val);
        const hasUpperLower = /[a-z]/.test(val) && /[A-Z]/.test(val);

        updateReqItem(reqLen, hasLen);
        updateReqItem(reqNum, hasNum);
        updateReqItem(reqAlpha, hasUpperLower);

        let score = 0;
        if (hasLen) score++;
        if (hasNum) score++;
        if (hasUpperLower) score++;

        b1.className = 'h-full w-1/3 rounded-full transition-all duration-300 bg-outline/20';
        b2.className = 'h-full w-1/3 rounded-full transition-all duration-300 bg-outline/20';
        b3.className = 'h-full w-1/3 rounded-full transition-all duration-300 bg-outline/20';

        if (val.length === 0) {
            text.textContent = 'Belum diisi';
            text.className = 'font-caption-sm-medium text-outline';
        } else if (score === 1) {
            b1.className = 'h-full w-1/3 rounded-full transition-all duration-300 bg-error';
            text.textContent = 'Lemah';
            text.className = 'font-caption-sm-medium text-error';
        } else if (score === 2) {
            b1.className = 'h-full w-1/3 rounded-full transition-all duration-300 bg-[#C97D1A]';
            b2.className = 'h-full w-1/3 rounded-full transition-all duration-300 bg-[#C97D1A]';
            text.textContent = 'Cukup';
            text.className = 'font-caption-sm-medium text-[#C97D1A]';
        } else if (score === 3) {
            b1.className = 'h-full w-1/3 rounded-full transition-all duration-300 bg-[#2E9E5B]';
            b2.className = 'h-full w-1/3 rounded-full transition-all duration-300 bg-[#2E9E5B]';
            b3.className = 'h-full w-1/3 rounded-full transition-all duration-300 bg-[#2E9E5B]';
            text.textContent = 'Sangat Kuat';
            text.className = 'font-caption-sm-medium text-[#2E9E5B]';
        }
        checkPasswordMatch();
    }

    function updateReqItem(elem, isValid) {
        if (!elem) return;
        const icon = elem.querySelector('.material-symbols-outlined');
        if (isValid) {
            elem.classList.remove('text-on-surface-variant');
            elem.classList.add('text-[#2E9E5B]');
            if (icon) {
                icon.textContent = 'check_circle';
                icon.className = 'material-symbols-outlined text-[14px] text-[#2E9E5B]';
            }
        } else {
            elem.classList.remove('text-[#2E9E5B]');
            elem.classList.add('text-on-surface-variant');
            if (icon) {
                icon.textContent = 'circle';
                icon.className = 'material-symbols-outlined text-[14px] text-outline';
            }
        }
    }

    function checkPasswordMatch() {
        const p1 = document.getElementById('reg-password').value;
        const p2 = document.getElementById('reg-confirm-password').value;
        const matchIcon = document.getElementById('match-indicator-icon');

        if (p2.length > 0 && p1 === p2) {
            matchIcon.classList.remove('hidden');
        } else {
            matchIcon.classList.add('hidden');
        }
    }
</script>
@endpush
