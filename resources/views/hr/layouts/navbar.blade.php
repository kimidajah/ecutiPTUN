<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #74c69d;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-light" href="{{ route('hr.dashboard') }}">HR Panel</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarHR" aria-controls="navbarHR" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarHR">
            <div class="d-flex align-items-center ms-auto">
                <a href="{{ route('hr.dashboard') }}" class="nav-link text-light me-3">Beranda</a>
                <a href="{{ route('hr.cuti.index') }}" class="nav-link text-light me-3">Manajemen Cuti</a>

                <form action="{{ route('logout') }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
