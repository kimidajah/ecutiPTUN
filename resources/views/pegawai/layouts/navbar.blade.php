
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #74c69d;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-light" href="{{ route('pegawai.dashboard') }}">Pegawai Panel</a>
        <div class="d-flex align-items-center">
            <a href="{{ route('pegawai.dashboard') }}" class="nav-link text-light me-3">Beranda</a>
            <a href="{{ route('pegawai.cuti.index') }}" class="nav-link text-light me-3">Cuti</a>

            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>
