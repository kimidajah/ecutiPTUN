<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sub Kepegawaian Dashboard')</title>

    <!-- Bootstrap CSS tanpa integrity -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
        rel="stylesheet">

        
</head>
<body class="bg-light">

    @include('hr.layouts.navbar')

    <div class="container py-4">
        @yield('content')
    </div>

    @include('hr.layouts.footer')

    <!-- Bootstrap JS tanpa integrity -->
    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')

</body>
</html>
