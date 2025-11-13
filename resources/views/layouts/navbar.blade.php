<nav 
  class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" 
  x-data="{ 
      active: window.location.hash || '#beranda',
      isLogin: window.location.pathname.includes('login') || window.location.pathname.includes('register')
  }" 
  x-init="
      window.addEventListener('hashchange', () => active = window.location.hash || '#beranda');
  "
>
  <div class="container">
    <!-- Logo kiri -->
    <a 
      class="navbar-brand fw-bold text-success" 
      href="{{ url('/') }}"
      :class="{ 'text-success': !isLogin }"
    >
      <img src="{{ asset('images/logoPTUN.svg') }}" alt="Logo" width="35" height="35" class="me-2">
      E-Cuti PTUN Bandung
    </a>

    <!-- Tombol toggle (untuk mobile) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu kanan -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">

        <!-- Beranda -->
        <li class="nav-item">
          <a 
            class="nav-link" 
            :class="{ 
              'text-success fw-bold': active === '#beranda', 
              'text-success-50': active !== '#beranda' 
            }" 
            href="{{ url('/#beranda') }}" 
            @click="active = '#beranda'">
            Beranda
          </a>
        </li>

        <!-- Alur -->
        <li class="nav-item">
          <a 
            class="nav-link" 
            :class="{ 
              'text-success fw-bold': active === '#alur', 
              'text-success-50': active !== '#alur' 
            }" 
            href="{{ url('/#alur') }}" 
            @click="active = '#alur'">
            Alur
          </a>
        </li>

        <!-- Jenis Cuti -->
        <li class="nav-item">
          <a 
            class="nav-link" 
            :class="{ 
              'text-success fw-bold': active === '#jenis', 
              'text-success-50': active !== '#jenis' 
            }" 
            href="{{ url('/#jenis') }}" 
            @click="active = '#jenis'">
            Jenis Cuti
          </a>
        </li>

        <!-- Garis pemisah -->
        <li class="nav-item">
          <span class="text-success mx-3">|</span>
        </li>

        <!-- Tombol login / dropdown user -->
        @guest
          <li class="nav-item">
            <a class="btn btn-outline-success btn-sm px-3" href="{{ route('login') }}">Login</a>
          </li>
        @else
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-success" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
              {{ Auth::user()->name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit" class="dropdown-item text-success">Logout</button>
                </form>
              </li>
            </ul>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>

<style>
  /* Warna teks hijau muda dan efek hover/klik */
  .text-success-50 {
    color: #8fd19e !important; /* hijau muda */
  }
  .text-success-50:hover {
    color: #198754 !important; /* hijau tua bootstrap */
  }
  .nav-link.text-success.fw-bold {
    color: #146c43 !important; /* hijau tua */
  }
</style>
