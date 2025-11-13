<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center mb-4 fw-bold text-success">Menu</h4>
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.permintaan') }}" class="nav-link {{ request()->is('admin/permintaan-cuti') ? 'active' : '' }}">
                <i class="bi bi-envelope-paper me-2"></i> Permintaan Cuti
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.user') }}" class="nav-link {{ request()->is('admin/user-karyawan') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> User & Karyawan
            </a>
        </li>
    </ul>
</div>
