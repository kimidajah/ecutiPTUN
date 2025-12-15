<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Permintaan dan Pemberian Cuti</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #000; }
        .title { text-align: center; margin-bottom: 8mm; }
        .title h3 { margin: 0 0 2mm 0; font-size: 16px; }
        .subtitle { font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        .section-title { background: #eee; font-weight: bold; }
        .no-border td { border: none; padding: 0; }
        .small { font-size: 10px; }
        .mb-2 { margin-bottom: 6px; }
        .mb-4 { margin-bottom: 12px; }
        .sign-row td { height: 28mm; }
        .right { text-align: right; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="title">
        <div class="small">LAMPIRAN II</div>
        <div class="small">SURAT EDARAN SEKRETARIS MAHKAMAH AGUNG</div>
        <div class="small">REPUBLIK INDONESIA</div>
        <div class="small">NOMOR 13 TAHUN 2019</div>
        <br>
        <div class="mb-2">Bandung,</div>
        <div class="mb-4">Yth. Pejabat Pembina Kepegawaian Pengadilan Tata Usaha Negara Bandung</div>
        <div class="mb-2">di-</div>
        <div class="mb-4">Bandung</div>
        <h3>FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</h3>
        <div class="subtitle">Nomor : /KPTUN.W2-TUN2/KP5.3/.../...</div>
    </div>

    <!-- I. DATA PEGAWAI -->
    <table class="mb-4">
        <tr>
            <td colspan="4" class="section-title">I. DATA PEGAWAI</td>
        </tr>
        <tr>
            <td class="nowrap" style="width:25%">NAMA</td>
            <td class="nowrap" style="width:25%">{{ $user->name }}</td>
            <td class="nowrap" style="width:25%">NIP</td>
            <td class="nowrap" style="width:25%">{{ $user->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td>JABATAN</td>
            <td>{{ $user->jabatan ?? '-' }}</td>
            <td>GOL. RUANG</td>
            <td>{{ $user->gol_ruang ?? '-' }}</td>
        </tr>
        @php
            $masaKerjaTahun = null;
            if (!empty($user->tanggal_masuk)) {
                $start = \Carbon\Carbon::parse($user->tanggal_masuk)->startOfDay();
                $months = $start->diffInMonths(\Carbon\Carbon::now());
                $masaKerjaTahun = (int) round($months / 12);
            }
        @endphp
        <tr>
            <td>UNIT KERJA</td>
            <td>{{ $user->unit_kerja ?? '-' }}</td>
            <td>MASA KERJA</td>
            <td>{{ isset($masaKerjaTahun) ? ($masaKerjaTahun . ' Tahun') : '-' }}</td>
        </tr>
    </table>

    <!-- II. JENIS CUTI YANG SIAMBIL -->
    <table class="mb-4">
        <tr>
            <td colspan="2" class="section-title">II. JENIS CUTI YANG SIAMBIL</td>
        </tr>
        @php
            $jenis = strtolower($cuti->jenis_cuti ?? '');
            $is = fn($key) => $jenis === $key;
            $box = fn($checked) => $checked ? '☑' : '☐';
        @endphp
        <tr>
            <td>{{ $box($is('tahunan')) }} 1. CUTI TAHUNAN</td>
            <td>{{ $box($is('besar')) }} 2. CUTI BESAR</td>
        </tr>
        <tr>
            <td>{{ $box($is('sakit')) }} 3. CUTI SAKIT</td>
            <td>{{ $box($is('bersalin')) }} 4. CUTI MELAHIRKAN</td>
        </tr>
        <tr>
            <td>{{ $box($is('penting')) }} 5. CUTI KARENA ALASAN PENTING</td>
            <td>{{ $box($is('luar tanggungan')) }} 6. CUTI DI LUAR TANGGUNGAN NEGARA</td>
        </tr>
    </table>

    <!-- III. ALASAN CUTI -->
    <table class="mb-4">
        <tr>
            <td class="section-title">III. ALASAN CUTI</td>
        </tr>
        <tr>
            <td>{{ $cuti->alasan }}</td>
        </tr>
    </table>

    <!-- IV. LAMANYA CUTI -->
    <table class="mb-4">
        <tr>
            <td colspan="3" class="section-title">IV. LAMANYA CUTI</td>
        </tr>
        <tr>
            <td style="width:40%">Selama: {{ $cuti->lama_cuti }} Hari</td>
            <td style="width:30%">Mulai Tanggal: {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</td>
            <td style="width:30%">s/d: {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</td>
        </tr>
    </table>

    <!-- V. CATATAN CUTI -->
    @php
        $tahun = (int) now()->format('Y');
        $jenisCutiSekarang = strtolower($cuti->jenis_cuti ?? 'tahunan');
        
        // Tentukan saldo berdasarkan jenis cuti
        if ($jenisCutiSekarang === 'tahunan') {
            $saldo = $user->saldo_cuti_tahunan ?? 30;
            $fieldSaldo = 'saldo_cuti_tahunan';
        } elseif ($jenisCutiSekarang === 'sakit') {
            $saldo = $user->saldo_cuti_sakit ?? 14;
            $fieldSaldo = 'saldo_cuti_sakit';
        } elseif ($jenisCutiSekarang === 'bersalin') {
            $saldo = $user->saldo_cuti_bersalin ?? 90;
            $fieldSaldo = 'saldo_cuti_bersalin';
        } elseif ($jenisCutiSekarang === 'penting') {
            $saldo = $user->saldo_cuti_penting ?? 12;
            $fieldSaldo = 'saldo_cuti_penting';
        } elseif ($jenisCutiSekarang === 'besar') {
            $saldo = $user->saldo_cuti_besar ?? 60;
            $fieldSaldo = 'saldo_cuti_besar';
        } else {
            $saldo = 0;
            $fieldSaldo = '';
        }
        
        // Hitung cuti yang sudah diambil tahun ini (hanya untuk jenis cuti yang sama)
        $diambilTahunIni = \App\Models\Cuti::where('user_id', $user->id)
            ->where('jenis_cuti', $cuti->jenis_cuti)
            ->whereYear('tanggal_mulai', $tahun)
            ->where('status', 'disetujui_pimpinan')
            ->sum('lama_cuti');
        
        // Hitung sisa (untuk cuti saat ini, setelah pengajuan ini disetujui)
        $sisa = max(0, $saldo - $diambilTahunIni - $cuti->lama_cuti);
    @endphp
    <table class="mb-4">
        <tr>
            <td colspan="4" class="section-title">V. CATATAN CUTI</td>
        </tr>
        <tr>
            <td class="nowrap" style="width:15%">TAHUN</td>
            <td class="nowrap" style="width:15%">SISA</td>
            <td class="nowrap" style="width:15%">KETERANGAN</td>
            <td style="width:55%">PARAF PETUGAS CUTI</td>
        </tr>
        <tr style="height:35mm">
            <td class="nowrap">{{ $tahun }}</td>
            <td class="nowrap">{{ $sisa }} HARI</td>
            <td class="nowrap">Sisa Cuti Tahun Ini</td>
            <td></td>
        </tr>
    </table>

    <!-- VI. ALAMAT SELAMA MENJALANKAN CUTI -->
    <table class="mb-4">
        <tr>
            <td colspan="2" class="section-title">VI. ALAMAT SELAMA MENJALANKAN CUTI</td>
        </tr>
        <tr>
            <td style="width:70%">{{ $cuti->alamat_selama_cuti ?? '-' }}</td>
            <td style="width:30%" class="center">Telp. {{ $cuti->telp_selama_cuti ?? $user->no_wa ?? '-' }}</td>
        </tr>
        <tr class="sign-row">
            <td></td>
            <td class="right">Hormat Saya,<br><br><br><br><div class="nowrap">{{ $user->name }}</div><div class="nowrap">NIP. {{ $user->nip ?? '-' }}</div></td>
        </tr>
    </table>

    <!-- VII. PERTIMBANGAN ATASAN LANGSUNG -->
    <table class="mb-4">
        <tr>
            <td colspan="4" class="section-title">VII. PERTIMBANGAN ATASAN LANGSUNG</td>
        </tr>
        <tr>
            <td class="center">DISETUJUI</td>
            <td class="center">PERUBAHAN</td>
            <td class="center">DITANGGUHKAN</td>
            <td class="center">TIDAK DISETUJUI</td>
        </tr>
        <tr class="sign-row">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="2">Nama: @if($cuti->atasan) {{ $cuti->atasan->name }} @endif</td>
            <td colspan="2">NIP. @if($cuti->atasan) {{ $cuti->atasan->nip ?? '-' }} @endif</td>
        </tr>
    </table>

    <!-- VIII. KEPUTUSAN PEJABAT BERWENANG MEMBERIKAN CUTI -->
    <table class="mb-4">
        <tr>
            <td colspan="4" class="section-title">VIII. KEPUTUSAN PEJABAT BERWENANG MEMBERIKAN CUTI</td>
        </tr>
        <tr>
            <td class="center">DISETUJUI</td>
            <td class="center">PERUBAHAN</td>
            <td class="center">DITANGGUHKAN</td>
            <td class="center">TIDAK DISETUJUI</td>
        </tr>
        <tr class="sign-row">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="2">Nama: @if($cuti->pimpinan) {{ $cuti->pimpinan->name }} @endif</td>
            <td colspan="2">NIP. @if($cuti->pimpinan) {{ $cuti->pimpinan->nip ?? '-' }} @endif</td>
        </tr>
    </table>

    @php
        $masaKerjaThn = 0;
        if (!empty($user->tanggal_masuk)) {
            $start = \Carbon\Carbon::parse($user->tanggal_masuk)->startOfDay();
            $months = $start->diffInMonths(\Carbon\Carbon::now());
            $masaKerjaThn = (int) round($months / 12);
        }
    @endphp
    <div class="small">Catatan: Coret yang tidak perlu. Pilih dengan tanda centang (√). Masa kerja: {{ $masaKerjaThn }} Tahun.</div>
</body>
</html>
