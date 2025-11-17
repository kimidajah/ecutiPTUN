<section id="beranda" 
  class="py-5" 
  style="
    min-height:100vh;
    background: linear-gradient(135deg, #b5f4b0 0%, #fdfcfb 70%, #fff7e6 100%);
  ">
  <div class="container">
    <div class="row align-items-center">

      <!-- Kiri: Teks -->
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h1 class="fw-bold text-success">SELAMAT DATANG</h1>
        <h4 class="text-success mb-3">di Aplikasi E-Cuti Pengadilan Tata Usaha Negara Bandung</h4>
        <p style="color:#484c4d;">
          E-Cuti adalah aplikasi Pengajuan Permohonan Cuti berbasis website yang
          memudahkan pegawai Pengadilan Tata Usaha Negara Bandung dalam mengelola dan mengajukan cuti secara cepat, efisien, dan transparan.
        </p>
        <!-- Tombol Login -->
        <a href="{{ route('login') }}" 
          class="btn btn-success px-4 py-2 mt-3 rounded-pill shadow-sm"
          style="font-size: 1.1rem;">
          <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </a>
      </div>

      <!-- Kanan: Ilustrasi -->
      <div class="col-lg-6 text-center">
        <img src="{{ asset('images/homepage.svg') }}" alt="eCuti Illustration" class="img-fluid" style="max-height:450px;">
      </div>

    </div>
  </div>
</section>
