<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #b5f4b0 0%, #fdfcfb 70%, #fff7e6 100%);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #d8f3dc;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .navbar {
            background-color: #95d5b2;
        }

        .sidebar a {
            color: #1b4332;
            text-decoration: none;
        }

        .sidebar a.active {
            background-color: #74c69d;
            color: white;
            border-radius: 10px;
        }

        .sidebar a:hover {
            background-color: #b7e4c7;
        }
    </style>
</head>
<body>
    {{-- Sidebar --}}
    @include('admin.layouts.sidebar')

    {{-- Main content area --}}
    <div class="main-content">
        {{-- Navbar --}}
        @include('admin.layouts.navbar')

        {{-- Dynamic content --}}
        <div class="container-fluid mt-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
