 <meta charset="utf-8" />
 <meta name="viewport"
     content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
 <meta name="robots" content="noindex, nofollow" />

 <title>SIKLINIK</title>

 <meta name="description" content="" />
 <meta name="csrf-token" content="{{ csrf_token() }}">

 <!-- Favicon -->
 <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.googleapis.com" />
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
 <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
     rel="stylesheet" />

 <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

 <!-- Core CSS -->
 <!-- build:css assets/vendor/css/theme.css -->

 <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />

 <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

 <!-- Vendors CSS -->

 <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

 <!-- endbuild -->

 <!-- Helpers -->
 <script src="{{ asset('assets/vendor/js/helpers.js') }}" data-navigate-once></script>
 <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

 <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file. -->

 <script src="{{ asset('assets/js/config.js') }}" data-navigate-once></script>

 @yield('Css')
