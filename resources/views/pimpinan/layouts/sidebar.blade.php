<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center mb-4 fw-bold text-success">Menu Pimpinan</h4>
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a href="{{ route('pimpinan.dashboard') }}" class="nav-link {{ request()->is('pimpinan/dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('pimpinan.cuti.index') }}" class="nav-link {{ request()->is('pimpinan/cuti*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check me-2"></i> Management Cuti
            </a>
        </li>
        <li class="nav-item mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>
