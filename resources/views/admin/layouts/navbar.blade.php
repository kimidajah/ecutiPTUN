<nav class="navbar navbar-expand-lg shadow-sm rounded" style="background-color: #74c69d;">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold text-light">Admin Panel</span>
        <div class="d-flex align-items-center text-light">
            <span class="me-3">{{ Auth::user()->name ?? 'Guest' }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm" type="submit">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>
