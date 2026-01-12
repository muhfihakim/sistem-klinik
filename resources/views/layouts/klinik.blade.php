<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="{{ asset('assets') }}"
    data-template="vertical-menu-template-free">

<head>
    @include('layouts.partials.head')
    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    @livewireStyles
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            {{-- @include('layouts.partials.sidebar') --}}
            @persist('main-menu')
                @include('layouts.partials.sidebar')
            @endpersist
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                @include('layouts.partials.navbar')

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        {{ $slot }}
                    </div>

                    <!-- Footer -->
                    @include('layouts.partials.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->
    <!-- Core JS (vendor) -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}" data-navigate-once></script>

    <script src="{{ asset('assets/js/main.js') }}" data-navigate-once></script>

    @livewireScripts

    <script>
        // Jalankan setiap kali wire:navigate selesai berpindah halaman
        document.addEventListener('livewire:navigated', () => {
            // 1. Inisialisasi ulang menu Materio agar dropdown & tombol sidebar tidak macet
            if (window.Helpers && typeof window.Helpers.initMenu === 'function') {
                window.Helpers.initMenu();
            }

            // 2. Logika Active Menu (Opsional jika sidebar macet)
            const currentUrl = window.location.href.split(/[?#]/)[0];
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active', 'open');
                const link = item.querySelector('a');
                if (link && link.href.split(/[?#]/)[0] === currentUrl) {
                    item.classList.add('active');
                    let parent = item.parentElement.closest('.menu-item');
                    while (parent) {
                        parent.classList.add('active', 'open');
                        parent = parent.parentElement.closest('.menu-item');
                    }
                }
            });
        });
    </script>
    {{-- <script data-navigate-once>
        document.addEventListener('livewire:init', () => {
            // Listener untuk menutup modal
            Livewire.on('close-modal', (event) => {
                const modalEl = document.querySelector(event.modalId);
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();

                    // Opsional: Hapus backdrop secara manual jika masih nyangkut
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    document.body.classList.remove('modal-open');
                    document.body.style = '';
                }
            });
        });
    </script> --}}
    <script data-navigate-once>
        document.addEventListener('livewire:init', () => {
            // Listener Global: Menutup Modal apa pun berdasarkan ID yang dikirim
            Livewire.on('close-modal', (event) => {
                const id = event.modalId || (event[0] && event[0].modalId);
                const modalEl = document.querySelector(id);
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();

                    // Bersihkan backdrop jika nyangkut
                    setTimeout(() => {
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                        document.body.classList.remove('modal-open');
                        document.body.style = '';
                    }, 100);
                }
            });

            // Listener Global: Membuka Modal
            Livewire.on('open-modal', (event) => {
                const id = event.modalId || (event[0] && event[0].modalId);
                const modalEl = document.querySelector(id);
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });

            // Di layouts/app.blade.php
            Livewire.on('close-modal', (event) => {
                const id = event.modalId || (event[0] && event[0].modalId);
                const el = document.querySelector(id);
                if (el) {
                    // Cek apakah itu Modal atau Offcanvas
                    if (el.classList.contains('offcanvas')) {
                        const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(el);
                        offcanvas.hide();
                    } else {
                        const modal = bootstrap.Modal.getOrCreateInstance(el);
                        modal.hide();
                    }
                }
            });
        });
    </script>
</body>

</html>
