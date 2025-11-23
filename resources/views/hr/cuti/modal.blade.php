<div 
    x-cloak
    x-show="show"
    x-transition.opacity
    class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
    style="background: rgba(0,0,0,0.45); z-index: 9999;"
>

    <div class="bg-white rounded shadow p-4"
         style="width: 420px;" 
         @click.outside="close()"
         x-transition.scale>

        <h5 class="fw-bold mb-3">Persetujuan Cuti</h5>

        <p><strong>Pegawai:</strong> <span x-text="data.user"></span></p>
        <p><strong>Jenis Cuti:</strong> <span x-text="data.jenis"></span></p>
        <p><strong>Tanggal:</strong> <span x-text="data.tanggal"></span></p>
        <p><strong>Alasan:</strong> <span x-text="data.alasan"></span></p>

        <div class="mt-4 d-flex justify-content-end gap-2">

            <form :action="`/hr/permintaan-cuti/${data.id}/approve`" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" :disabled="data.status !== 'menunggu'" value="1">
                    Setuju
                </button>
            </form>

            <form :action="`/hr/permintaan-cuti/${data.id}/reject`" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger" :disabled="data.status !== 'menunggu'" value="2">
                    Tolak
                </button>
            </form>

            <button class="btn btn-secondary" @click="close()">Batal</button>
        </div>

    </div>

</div>


{{-- SCRIPT ALPINE --}}
<script>
function cutiPopup() {
    return {
        show: false,
        data: {
            id: '',
            user: '',
            jenis: '',
            tanggal: '',
            alasan: '',
            status: '',
        },
        init() {
            this.show = false;
        },
        open(item) {
            this.data = item;
            this.show = true;
        },
        close() {
            this.show = false;
        }
    }
}
</script>
