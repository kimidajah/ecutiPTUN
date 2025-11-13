<div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-light" 
     style="width: 260px; height: 100vh; position: fixed;">
    <a href="{{ route('admin.dashboard') }}" 
       class="d-flex align-items-center mb-3 text-decoration-none text-light">
        <span class="fs-5 fw-bold">Cuti & Cashbon</span>
    </a>
    <hr class="border-secondary">

    <ul class="nav nav-pills flex-column mb-auto">
        {{-- Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-link text-light {{ request()->routeIs('admin.dashboard') ? 'active bg-warning text-dark' : 'text-light' }}">
                <i class="bi bi-house-door me-2"></i> Dasbor
            </a>
        </li>

        {{-- Permintaan Cuti --}}
        <li>
            <a href="{{ route('admin.cuti.index') }}" 
               class="nav-link text-light {{ request()->routeIs('admin.cuti.*') ? 'active bg-warning text-dark' : 'text-light' }}">
                <i class="bi bi-calendar-check me-2"></i> Permintaan Cuti
            </a>
        </li>

        {{-- Aturan Cuti --}}
        <li>
            <a href="{{ route('admin.cuti.rules') }}" 
               class="nav-link text-light {{ request()->routeIs('admin.cuti.rules') ? 'active bg-warning text-dark' : 'text-light' }}">
                <i class="bi bi-gear me-2"></i> Aturan Cuti
            </a>
        </li>

        {{-- Users & Karyawan --}}
        <li>
            <a href="{{ route('admin.users.index') }}" 
               class="nav-link text-light {{ request()->routeIs('admin.users.*') ? 'active bg-warning text-dark' : 'text-light' }}">
                <i class="bi bi-people me-2"></i> Users & Karyawan
            </a>
        </li>
    </ul>

    <hr class="border-secondary">
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle" 
           id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}" alt="avatar" width="32" height="32" class="rounded-circle me-2">
            <strong>{{ Auth::user()->name }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
            <li><a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                   Keluar</a>
            </li>
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</div>
