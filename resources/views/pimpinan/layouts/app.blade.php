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
            min-height: 100vh;
            background: linear-gradient(135deg, #b5f4b0 0%, #fdfcfb 60%, #fff7e6 100%);
            font-family: "Segoe UI", sans-serif;
        }

        .content-wrapper {
            padding: 25px;
        }

        .card-wrapper {
            background: white;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

    @include('pimpinan.layouts.navbar')

    <div class="container content-wrapper">
        <div class="card-wrapper">
            @yield('content')
        </div>
    </div>

    @include('pimpinan.layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
