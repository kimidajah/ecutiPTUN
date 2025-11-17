<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Batas Cuti / Tahun</h6>
                <h3 class="fw-bold text-success">{{ $batasCuti }} Hari</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Cuti Anda Tahun Ini</h6>
                <h3 class="fw-bold text-primary">{{ $totalCutiTahunIni }} Hari</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Sisa Cuti Tahun Ini</h6>
                <h3 class="fw-bold text-danger">{{ $sisaCuti }} Hari</h3>
            </div>
        </div>
    </div>
</div>
