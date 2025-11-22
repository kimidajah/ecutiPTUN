<section id="jenis" class="py-5" style="
    min-height:100vh;
    background: linear-gradient(135deg, #fefbf6 0%, #fbf7e4 35%, #d8f8d4 65%, #d8f8d4 100%);
  ">
  <div class="container">
    <h2 class="fw-bold mb-3 text-center">Jenis-Jenis Cuti</h2>
    <p class="text-center mb-5 text-muted">
      Klik gambar di bawah untuk melihat penjelasan lengkap setiap jenis cuti.
    </p>

    <div class="row g-4">

      <!-- Template Kartu -->
      @php
        $jeniscuti = [
          ['title' => 'Cuti Tahunan', 'color' => 'success', 'img' => '3.svg', 'desc' => 'Cuti reguler yang menjadi hak setiap pegawai dengan perhitungan saldo otomatis dan reminder untuk penggunaan optimal. Maksimal: 12 Hari/Tahun. Persetujuan: Kepala Perangkat Daerah.'],
          ['title' => 'Cuti Sakit', 'color' => 'success', 'img' => '2.svg', 'desc' => 'Cuti untuk keperluan kesehatan dengan sistem upload surat dokter dan integrasi asuransi kesehatan. Maksimal: Sesuai surat dokter. Persetujuan: 1 Hari – Atasan Langsung, 2 Hari s.d 1 Tahun – Kepala Badan Kepegawaian.'],
          ['title' => 'Cuti Melahirkan', 'color' => 'success', 'img' => '1.svg', 'desc' => 'Cuti untuk pegawai wanita yang melahirkan, dengan perhitungan otomatis sesuai peraturan. Maksimal: 3 Bulan. Persetujuan: Pelaksana & Fungsional – Kepala Perangkat Daerah, Administrator & Pengawas – Kepala Badan Kepegawaian, JPT – SEKDA.'],
          ['title' => 'Cuti Besar', 'color' => 'success', 'img' => '4.svg', 'desc' => 'Cuti untuk keperluan ibadah keagamaan seperti haji, umroh, atau ritual penting lainnya. Persetujuan: Kepala Badan Kepegawaian.'],
          ['title' => 'Cuti Alasan Penting', 'color' => 'success', 'img' => '5.svg', 'desc' => 'Cuti untuk keperluan mendesak seperti pendidikan formal, keluarga, pernikahan, bencana, atau tugas tertentu. Persetujuan: sesuai jabatan dan jenis alasan cuti.'],
        ];
      @endphp

      @foreach ($jeniscuti as $i => $cuti)
        <div class="col-md-6 col-lg-4">
          <div 
            class="card h-100 shadow-sm border-{{ $cuti['color'] }} d-flex flex-row align-items-center hover-card"
            style="cursor:pointer;"
            data-bs-toggle="modal"
            data-bs-target="#modalCuti{{ $i }}"
          >
            <img 
              src="{{ asset('images/jeniscuti/' . $cuti['img']) }}" 
              class="img-fluid p-3"
              style="width:150px;" 
              alt="{{ $cuti['title'] }}">
            <div class="card-body">
              <h5 class="card-title text-{{ $cuti['color'] }}">{{ $cuti['title'] }}</h5>
              <p class="card-text text-muted">Klik untuk detail lengkap.</p>
            </div>
          </div>
        </div>


      <!-- Modal -->
      <div class="modal fade" id="modalCuti{{ $i }}" tabindex="-1" aria-labelledby="modalLabel{{ $i }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0">
              <h5 class="modal-title fw-bold text-{{ $cuti['color'] }}" id="modalLabel{{ $i }}">{{ $cuti['title'] }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
              <img src="{{ asset('images/jeniscuti/' . $cuti['img']) }}" 
                   class="img-fluid mb-3" 
                   style="max-width: 250px;" 
                   alt="{{ $cuti['title'] }}">
              <p class="text-muted">{{ $cuti['desc'] }}</p>
            </div>
            <div class="modal-footer border-0">
              <button type="button" class="btn btn-outline-{{ $cuti['color'] }}" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
      @endforeach

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

  <style>
    .hover-card {
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .hover-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .modal-backdrop.show {
      backdrop-filter: blur(5px); /* efek blur background */
      background-color: rgba(0, 0, 0, 0.4);
    }
  </style>
</section>
