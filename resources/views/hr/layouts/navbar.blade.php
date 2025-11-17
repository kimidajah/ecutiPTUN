<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('hr.dashboard') }}">
            HR Panel
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarHR" aria-controls="navbarHR" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarHR">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="{{ route('hr.dashboard') }}" class="nav-link">Beranda</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('hr.cuti.index') }}" class="nav-link">Manajemen Cuti</a>
                </li>
                {{-- Logout --}}
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-outline-light ms-3">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
