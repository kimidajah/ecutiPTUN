<nav class="navbar navbar-light" style="background-color: #74c69d;">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <span class="toggle-sidebar me-3" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </span>
            <span class="navbar-brand fw-bold text-light mb-0">Panel Pimpinan</span>
        </div>
        <span class="text-light">
            <i class="bi bi-person-circle me-1"></i>
            {{ Auth::user()->name ?? 'Pimpinan' }}
        </span>
    </div>
</nav>
