<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Andalas Residence')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased">
    {{-- Mobile Sidebar Backdrop --}}
    <div id="sidebar-backdrop" class="fixed inset-0 bg-primary/40 backdrop-blur-xs z-40 hidden lg:hidden" onclick="toggleSidebar(false)"></div>

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Wrapper --}}
    <div class="lg:pl-64 print:pl-0 min-h-screen flex flex-col">
        @include('partials.navbar')

        <main class="w-full flex-1 pt-24 print:pt-4 pb-space-2xl px-space-md lg:px-space-xl bg-background">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>

    {{-- Global Delete Confirmation Modal --}}
    <div id="delete-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-space-md bg-primary/40 backdrop-blur-xs" role="dialog" aria-modal="true" aria-labelledby="delete-confirm-title">
        <div class="bg-surface-container-lowest rounded-2xl shadow-2xl max-w-md w-full p-space-lg flex flex-col gap-space-md animate-[fadeIn_0.15s_ease-out]">
            <div class="flex items-start gap-space-md">
                <div class="w-11 h-11 rounded-xl bg-error-container text-error flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">delete_forever</span>
                </div>
                <div class="flex flex-col gap-1">
                    <h3 id="delete-confirm-title" class="font-headline-sm text-headline-sm text-primary">Konfirmasi Penghapusan</h3>
                    <p id="delete-confirm-message" class="font-body-md text-body-md text-on-surface-variant">Apakah Anda yakin ingin menghapus data ini?</p>
                    <p class="font-caption-sm text-caption-sm text-error mt-1">Tindakan ini permanen dan data tidak dapat dikembalikan.</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-space-sm pt-space-xs">
                <button type="button" onclick="closeDeleteModal()" class="px-space-md py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high font-label-md text-label-md transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="button" id="delete-confirm-submit" class="px-space-lg py-2 rounded-lg bg-error text-on-error hover:bg-[#991b1b] font-label-md text-label-md shadow-sm transition-colors cursor-pointer">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar(open) {
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (!sidebar || !backdrop) return;

            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        let pendingDeleteForm = null;

        function openDeleteModal(form) {
            pendingDeleteForm = form;
            const modal = document.getElementById('delete-confirm-modal');
            const titleEl = document.getElementById('delete-confirm-title');
            const msgEl = document.getElementById('delete-confirm-message');
            if (!modal || !titleEl || !msgEl) return;

            titleEl.textContent = form.dataset.confirmTitle || 'Konfirmasi Penghapusan';
            msgEl.textContent = form.dataset.confirm || 'Apakah Anda yakin ingin menghapus data ini?';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-confirm-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingDeleteForm = null;
        }

        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form instanceof HTMLFormElement && form.dataset.confirm && !form.dataset.confirmed) {
                e.preventDefault();
                openDeleteModal(form);
            }
        });

        document.getElementById('delete-confirm-submit')?.addEventListener('click', function () {
            if (!pendingDeleteForm) return;
            pendingDeleteForm.dataset.confirmed = 'true';
            pendingDeleteForm.submit();
        });

        document.getElementById('delete-confirm-modal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
