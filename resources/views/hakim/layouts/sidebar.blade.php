<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center mb-4 fw-bold text-success">Menu Hakim</h4>
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a href="{{ route('hakim.dashboard') }}" class="nav-link {{ request()->is('hakim/dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Beranda
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('hakim.cuti.index') }}" class="nav-link {{ request()->is('hakim/cuti*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check me-2"></i> Cuti
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
