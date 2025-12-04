<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Aplikasi Cuti')</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- AOS CSS -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    body {
      scroll-behavior: smooth; /* Smooth scroll saat klik navbar */
      padding-top: 70px; /* Supaya konten tidak tertutup navbar fixed-top */
    }
  </style>
</head>
<body>

  {{-- Navbar --}}
  @include('layouts.navbar')

  {{-- Konten Utama --}}
  <main>
    @yield('content')
  </main>

  {{-- Footer --}}
  @include('layouts.footer')

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- AlpineJS -->
  <script src="//unpkg.com/alpinejs" defer></script>



</body>
</html>
