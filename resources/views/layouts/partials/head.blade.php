 <meta charset="utf-8" />
 <meta name="viewport"
     content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
 <title>SIKLINIK - Sistem Informasi Manajemen Klinik & Rekam Medis Elektronik</title>
 <meta name="title" content="SIKLINIK - Sistem Informasi Manajemen Klinik & Rekam Medis Elektronik">
 <meta name="description"
     content="SIKLINIK: Solusi digital manajemen klinik, rekam medis elektronik (RME), antrean pasien, dan sistem kasir medis terintegrasi. Meningkatkan efisiensi layanan kesehatan Anda.">
 <meta name="keywords"
     content="sistem informasi klinik, rekam medis elektronik, aplikasi klinik, RME, manajemen antrean klinik, software rumah sakit, SIKLINIK, pendaftaran pasien online">
 <meta name="robots" content="index, follow">
 <meta name="author" content="SIKLINIK">
 <meta property="og:type" content="website">
 <meta property="og:url" content="{{ url()->current() }}">
 <meta property="og:title" content="SIKLINIK - Digitalisasi Layanan Kesehatan Anda">
 <meta property="og:description"
     content="Kelola antrean, rekam medis (SOAP), hingga pembayaran klinik dalam satu sistem terintegrasi.">
 <meta property="og:site_name" content="SIKLINIK">

 <meta name="twitter:card" content="summary_large_image">
 <meta name="twitter:title" content="SIKLINIK - Manajemen Klinik Modern">
 <meta name="twitter:description"
     content="Optimalkan operasional klinik dengan Sistem Informasi Manajemen Klinik yang efisien dan aman.">

 <meta name="csrf-token" content="{{ csrf_token() }}">

 <!-- Favicon -->
 <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.googleapis.com" />
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
 <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
     rel="stylesheet" />
 <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
 <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
 <!-- Core CSS -->
 <!-- build:css assets/vendor/css/theme.css -->
 <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
 <!-- Vendors CSS -->
 <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
 <!-- Helpers -->
 <script src="{{ asset('assets/vendor/js/helpers.js') }}" data-navigate-once></script>
 <script src="{{ asset('assets/js/config.js') }}" data-navigate-once></script>

 @yield('Css')
