<section id="alur" class="py-5 bg-light" x-data="{ stepsVisible: [false, false, false] }"
         x-init="setTimeout(() => { stepsVisible[0] = true }, 100); setTimeout(() => { stepsVisible[1] = true }, 300); setTimeout(() => { stepsVisible[2] = true }, 500);"
         style="
    min-height:100vh;
    background: linear-gradient(135deg, #d8f8d4 0%, #fdfcfb 30%, #fff7e6 65%, #d8f8d4 100%);
  ">

  <div class="container">
    <!-- Header Section -->
    <div class="text-center mb-5">
      <h2 class="fw-bold text-success mb-2">
        <i class="bi bi-diagram-3 me-2"></i>Alur Pengajuan Cuti
      </h2>
      <p class="text-muted fs-5 mb-0">
        3 Langkah Mudah untuk Mengajukan Cuti Anda
      </p>
    </div>

    <!-- Steps Container -->
    <div class="row justify-content-center">
      <div class="col-lg-10">
        
        <!-- Step 1 -->
        <div class="mb-4" 
             x-show="stepsVisible[0]" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 transform translate-y-4" 
             x-transition:enter-end="opacity-100 transform translate-y-0">
          <div class="card border-0 shadow-sm hover-shadow">
            <div class="card-body p-4">
              <div class="row align-items-center">
                <div class="col-auto">

                </div>
                <div class="col">
                  <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success me-2">Langkah 1</span>
                    <h5 class="card-title text-success mb-0 fw-bold">Isi Formulir Pengajuan</h5>
                  </div>
                  <p class="card-text text-muted mb-0 fs-6">
                    Pegawai login ke sistem e-Cuti dan mengisi formulir pengajuan cuti dengan melengkapi data seperti tanggal mulai, tanggal selesai, jenis cuti, dan alasan cuti.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Arrow Connector 1 -->
        <div class="text-center mb-4" 
             x-show="stepsVisible[0]"
             x-transition:enter="transition ease-out duration-300 delay-200"
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100">
          <i class="bi bi-arrow-down-circle-fill text-success fs-2"></i>
        </div>

        <!-- Step 2 -->
        <div class="mb-4" 
             x-show="stepsVisible[1]" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 transform translate-y-4" 
             x-transition:enter-end="opacity-100 transform translate-y-0">
          <div class="card border-0 shadow-sm hover-shadow">
            <div class="card-body p-4">
              <div class="row align-items-center">
                <div class="col-auto">
                </div>
                <div class="col">
                  <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success me-2">Langkah 2</span>
                    <h5 class="card-title text-success mb-0 fw-bold">Verifikasi oleh Atasan</h5>
                  </div>
                  <p class="card-text text-muted mb-0 fs-6">
                    Atasan langsung menerima notifikasi dan melakukan review terhadap pengajuan cuti. Atasan dapat menyetujui atau menolak dengan memberikan catatan.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Arrow Connector 2 -->
        <div class="text-center mb-4" 
             x-show="stepsVisible[1]"
             x-transition:enter="transition ease-out duration-300 delay-200"
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100">
          <i class="bi bi-arrow-down-circle-fill text-success fs-2"></i>
        </div>

        <!-- Step 3 -->
        <div class="mb-4" 
             x-show="stepsVisible[2]" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 transform translate-y-4" 
             x-transition:enter-end="opacity-100 transform translate-y-0">
          <div class="card border-0 shadow-sm hover-shadow">
            <div class="card-body p-4">
              <div class="row align-items-center">
                <div class="col-auto">
                </div>
                <div class="col">
                  <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success me-2">Langkah 3</span>
                    <h5 class="card-title text-success mb-0 fw-bold">Notifikasi & Penyimpanan Data</h5>
                  </div>
                  <p class="card-text text-muted mb-0 fs-6">
                    Sistem secara otomatis menyimpan hasil persetujuan ke database dan mengirimkan notifikasi ke pegawai melalui WhatsApp atau dashboard sistem.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Info Box -->
        <div class="mt-5" 
             x-show="stepsVisible[2]"
             x-transition:enter="transition ease-out duration-500 delay-300"
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100">
          <div class="alert alert-success border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
              <i class="bi bi-info-circle-fill fs-4 me-3"></i>
              <div>
                <h6 class="mb-1 fw-bold">Catatan Penting</h6>
                <small>Proses pengajuan cuti biasanya memakan waktu 1-3 hari kerja. Pastikan Anda mengajukan cuti minimal 7 hari sebelum tanggal cuti dimulai.</small>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <style>
    .hover-shadow {
      transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
  </style>

</section>