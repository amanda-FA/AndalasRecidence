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
    <div class="lg:pl-64 min-h-screen flex flex-col">
        @include('partials.navbar')

        <main class="w-full flex-1 pt-24 pb-space-2xl px-space-md lg:px-space-xl bg-background">
            @include('partials.alerts')
            @yield('content')
        </main>
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
    </script>
    @stack('scripts')
</body>
</html>
