<div class="modal fade" id="cutiModal" tabindex="-1" aria-labelledby="cutiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="cutiModalLabel"><i class="bi bi-plus-circle"></i> Buat Cuti Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pegawai.cuti.store') }}" method="POST" enctype="multipart/form-data" onsubmit="disableSubmitButton(this)">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Cuti</label>
                        <select name="jenis_cuti" id="jenis_cuti_modal" class="form-select" required>
                            <option value="">-- Pilih Jenis Cuti --</option>
                            <option value="tahunan">Cuti Tahunan (12 hari)</option>
                            <option value="sakit">Cuti Sakit (Unlimited - butuh surat dokter)</option>
                            <option value="bersalin">Cuti Bersalin (90 hari)</option>
                            <option value="penting">Cuti Penting (12 hari)</option>
                            <option value="besar">Cuti Besar (60 hari)</option>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai_modal" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" id="tanggal_selesai_modal" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lama Cuti (hari)</label>
                        <input type="text" id="lama_cuti_modal" class="form-control bg-light" readonly>
                        <input type="hidden" name="lama_cuti" id="lama_cuti_hidden_modal">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Cuti tahunan / alasan pribadi" required></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Alamat selama cuti</label>
                            <input type="text" name="alamat_selama_cuti" class="form-control" placeholder="Alamat lengkap saat cuti">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Telp saat cuti</label>
                            <input type="text" name="telp_selama_cuti" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <!-- Upload Bukti untuk Cuti Sakit dan Bersalin -->
                    <div class="mb-3" id="bukti_file_section_modal" style="display: none;">
                        <label class="form-label fw-semibold">Unggah Bukti Surat Dokter <span class="text-danger">*</span></label>
                        <input type="file" name="bukti_file" id="bukti_file_modal" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB). Wajib untuk cuti sakit dan melahirkan.</small>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script untuk hitung lama cuti otomatis (exclude weekend & hari libur) --}}
<script>
function disableSubmitButton(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim...';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const tglMulai   = document.getElementById('tanggal_mulai_modal');
    const tglSelesai = document.getElementById('tanggal_selesai_modal');
    const lamaCuti   = document.getElementById('lama_cuti_modal');
    const lamaHidden = document.getElementById('lama_cuti_hidden_modal');
    const jenisCuti  = document.getElementById('jenis_cuti_modal');
    const buktiSection = document.getElementById('bukti_file_section_modal');
    const buktiFile = document.getElementById('bukti_file_modal');

    // Show/hide bukti file based on jenis cuti
    jenisCuti.addEventListener('change', function() {
        if (this.value === 'sakit' || this.value === 'bersalin') {
            buktiSection.style.display = 'block';
            buktiFile.required = true;
        } else {
            buktiSection.style.display = 'none';
            buktiFile.required = false;
            buktiFile.value = '';
        }
    });

    function hitungHariKerja() {
        if (!tglMulai.value || !tglSelesai.value) {
            lamaCuti.value = '';
            lamaHidden.value = '';
            return;
        }

        const mulai = new Date(tglMulai.value);
        const selesai = new Date(tglSelesai.value);

        if (selesai < mulai) {
            lamaCuti.value = '';
            lamaHidden.value = '';
            return;
        }

        let hariKerja = 0;
        let currentDate = new Date(mulai);

        // Loop setiap hari dari tanggal mulai hingga selesai
        while (currentDate <= selesai) {
            // Cek apakah hari itu adalah weekend (Sabtu=6, Minggu=0)
            const dayOfWeek = currentDate.getDay();
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);

            // Jika bukan weekend, hitung sebagai hari kerja
            // (Hari libur nasional akan di-handle di backend)
            if (!isWeekend) {
                hariKerja++;
            }

            currentDate.setDate(currentDate.getDate() + 1);
        }

        lamaCuti.value = hariKerja + ' Hari';
        lamaHidden.value = hariKerja; // ini dikirim ke server
    }

    tglMulai.addEventListener('change', hitungHariKerja);
    tglSelesai.addEventListener('change', hitungHariKerja);
});
</script>
