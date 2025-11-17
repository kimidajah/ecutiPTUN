<div class="modal fade" id="cutiModal" tabindex="-1" aria-labelledby="cutiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="cutiModalLabel"><i class="bi bi-plus-circle"></i> Buat Cuti Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pegawai.cuti.store') }}" method="POST">
                    @csrf

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

{{-- Script untuk hitung lama cuti otomatis --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tglMulai   = document.getElementById('tanggal_mulai_modal');
    const tglSelesai = document.getElementById('tanggal_selesai_modal');
    const lamaCuti   = document.getElementById('lama_cuti_modal');
    const lamaHidden = document.getElementById('lama_cuti_hidden_modal');

    function hitungLamaCuti() {
        const mulai = new Date(tglMulai.value);
        const selesai = new Date(tglSelesai.value);

        if (tglMulai.value && tglSelesai.value && selesai >= mulai) {
            const diffTime = selesai - mulai;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // inklusif
            lamaCuti.value = diffDays + ' Hari';
            lamaHidden.value = diffDays; // ini dikirim ke server
        } else {
            lamaCuti.value = '';
            lamaHidden.value = '';
        }
    }

    tglMulai.addEventListener('change', hitungLamaCuti);
    tglSelesai.addEventListener('change', hitungLamaCuti);
});
</script>
