<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - Pimpinan Panel</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom Styles --}}
    <style>
        body {
            background: #f5f6fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .content-wrapper {
            padding: 20px;
        }
    </style>
</head>
<body>

    @include('pimpinan.layouts.navbar')

    <div class="container content-wrapper">
        @yield('content')
    </div>

    @include('pimpinan.layouts.footer')

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
