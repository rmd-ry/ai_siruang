<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIRuang</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/script.js'])

</head>

<body class="bg-page font-sans">

    {{-- TOPBAR --}}
    @include('layouts.topbar')

    {{-- MAIN CONTENT (Hanya Satu) --}}
    <main class="min-h-screen container mx-auto px-3 sm:px-6 py-4 sm:py-8">
        @yield('content')
    </main>

    {{-- FOOTER (Hanya Satu) --}}
    @include('layouts.footer')

    {{-- Quick Booking Assistant (AI) --}}
    @auth
        @include('components.booking-assistant')
    @endauth

    {{-- Script Global Dropdown (Notifikasi & Menu) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Logika Dropdown Notifikasi
            const dropdowns = document.querySelectorAll('[data-dropdown]');
            
            dropdowns.forEach(dropdown => {
                const trigger = dropdown.querySelector('[data-dropdown-trigger]');
                const panel = dropdown.querySelector('[data-dropdown-panel]');
                
                if (trigger && panel) {
                    trigger.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const isHidden = panel.classList.contains('hidden');
                        
                        // Tutup dropdown lain jika ada
                        document.querySelectorAll('[data-dropdown-panel]').forEach(p => {
                            p.classList.add('hidden');
                            p.classList.remove('opacity-100');
                        });

                        if (isHidden) {
                            panel.classList.remove('hidden');
                            setTimeout(() => panel.classList.add('opacity-100'), 10);
                        }
                    });
                }
            });

            // Klik di luar untuk menutup dropdown
            document.addEventListener('click', function () {
                document.querySelectorAll('[data-dropdown-panel]').forEach(p => {
                    p.classList.add('hidden');
                    p.classList.remove('opacity-100');
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>