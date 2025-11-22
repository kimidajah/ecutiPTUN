<nav class="navbar navbar-expand-lg shadow-sm" 
     style="background: #67d98a;">
    <div class="container">

        <a class="navbar-brand text-white fw-bold" href="{{ route('pimpinan.dashboard') }}">
            Panel Pimpinan
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('pimpinan.dashboard') }}">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('pimpinan.cuti.index') }}">
                        Persetujuan Cuti
                    </a>
                </li>

                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-light ms-3 px-3 rounded-pill">
                            Logout
                        </button>
                    </form>
                </li>

            </ul>

        </div>

    </div>
</nav>
