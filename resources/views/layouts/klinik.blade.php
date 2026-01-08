<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    @include('layouts.partials.head')
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                        <div class="row gy-6">
                            {{ $slot }}
                            <!--/ Data Tables -->
                        </div>
                    </div>
                    <!-- / Content -->

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
    <!-- Core JS -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}" data-navigate-once></script>
    @yield('Scripts')

    <!-- Main JS -->

    <script src="{{ asset('assets/js/main.js') }}" data-navigate-once></script>

    <script data-navigate-once>
        document.addEventListener('livewire:navigated', () => {
            const currentUrl = window.location.href.split(/[?#]/)[0]; // Bersihkan query string atau hash

            // 1. Bersihkan SEMUA class active dan open terlebih dahulu agar tidak double
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active', 'open');
            });

            // 2. Cari link yang aktif
            const menuLinks = document.querySelectorAll('.menu-link');

            menuLinks.forEach(link => {
                // Normalisasi URL link untuk perbandingan
                const linkUrl = link.href.split(/[?#]/)[0];

                if (linkUrl === currentUrl) {
                    const menuItem = link.closest('.menu-item');
                    if (menuItem) {
                        menuItem.classList.add('active');

                        // Tambahkan active & open ke parent (Data Master / Dashboards)
                        let parent = menuItem.parentElement.closest('.menu-item');
                        while (parent) {
                            parent.classList.add('active', 'open');
                            parent = parent.parentElement.closest('.menu-item');
                        }
                    }
                }
            });

            // 3. Panggil ulang init menu Materio agar dropdown bisa diklik
            if (window.Helpers) {
                window.Helpers.initMenu();
                // Jika sidebar tertutup otomatis, paksa buka kembali
                if (!window.Helpers.isSmallDevice()) {
                    window.Helpers.setCollapsed(false, false);
                }
            }
        });
    </script>
</body>

</html>
