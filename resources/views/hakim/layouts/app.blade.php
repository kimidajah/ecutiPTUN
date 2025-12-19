<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hakim Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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
            transition: margin-left 0.3s ease;
            position: relative;
        }

        .sidebar.collapsed {
            margin-left: -250px;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            transition: margin-left 0.3s ease;
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

        .toggle-sidebar {
            cursor: pointer;
            font-size: 1.5rem;
            color: white;
        }
    </style>
</head>
<body>
    {{-- Sidebar --}}
    @include('hakim.layouts.sidebar')

    {{-- Main content area --}}
    <div class="main-content">
        {{-- Navbar --}}
        @include('hakim.layouts.navbar')

        {{-- Dynamic content --}}
        <div class="container-fluid mt-4">
            @yield('content')
        </div>

        {{-- Footer --}}
        @include('hakim.layouts.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        }
    </script>
    @stack('scripts')
</body>
</html>
