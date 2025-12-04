# Implementasi Role Hakim dan Ketua - Panduan Lengkap

## Ringkasan Perubahan

Sistem telah diperbarui dengan menambahkan dua role baru: **Hakim** dan **Ketua** dengan flow approval cuti yang berbeda.

### Flow Approval Cuti:

**Untuk Hakim:**
```
Hakim mengajukan cuti → HR approve/reject → Pimpinan approve/reject → Selesai
```

**Untuk Pegawai (diperbarui):**
```
Pegawai mengajukan cuti → HR approve/reject → Ketua approve/reject → Pimpinan approve/reject → Selesai
```

---

## Step-by-Step Setup

### 1. Jalankan Migrations

```bash
php artisan migrate
```

Migrations yang akan dijalankan:
- `2025_12_04_000001_add_hakim_ketua_roles.php` - Menambah role dan kolom ketua_id
- `2025_12_04_000002_add_approval_fields_to_cuti_table.php` - Menambah status dan catatan ketua

### 2. Jalankan Seeders

```bash
php artisan db:seed --class=HakimAndKetuaSeeder
```

Atau jalankan semua seeders:

```bash
php artisan db:seed
```

Credentials untuk testing:
- **Hakim**: hakim@ptun.go.id / password123
- **Ketua**: ketua@ptun.go.id / password123

### 3. File yang Dibuat/Dimodifikasi

#### **Models:**
- ✅ `app/Models/User.php` - Menambah relasi ketua()
- ✅ `app/Models/Cuti.php` - Menambah field catatan_ketua

#### **Controllers:**
- ✅ `app/Http/Controllers/HakimController.php` - CRUD cuti untuk hakim
- ✅ `app/Http/Controllers/KetuaController.php` - Approval cuti untuk ketua
- ✅ `app/Http/Controllers/HRController.php` - Updated untuk route berbeda
- ✅ `app/Http/Controllers/PimpinanController.php` - Updated untuk accept ketua approval
- ✅ `app/Http/Controllers/HomeController.php` - Auto redirect berdasarkan role

#### **Routes:**
- ✅ `routes/hakim.php` - Routes hakim
- ✅ `routes/ketua.php` - Routes ketua
- ✅ `routes/web.php` - Include routes baru

#### **Views - Hakim:**
- ✅ `resources/views/hakim/layouts/app.blade.php`
- ✅ `resources/views/hakim/layouts/sidebar.blade.php`
- ✅ `resources/views/hakim/layouts/navbar.blade.php`
- ✅ `resources/views/hakim/layouts/footer.blade.php`
- ✅ `resources/views/hakim/dashboard.blade.php`
- ✅ `resources/views/hakim/cuti/index.blade.php`
- ✅ `resources/views/hakim/cuti/create.blade.php`
- ✅ `resources/views/hakim/cuti/edit.blade.php`
- ✅ `resources/views/hakim/cuti/show.blade.php`

#### **Views - Ketua:**
- ✅ `resources/views/ketua/layouts/app.blade.php`
- ✅ `resources/views/ketua/layouts/sidebar.blade.php`
- ✅ `resources/views/ketua/layouts/navbar.blade.php`
- ✅ `resources/views/ketua/layouts/footer.blade.php`
- ✅ `resources/views/ketua/dashboard.blade.php`
- ✅ `resources/views/ketua/cuti/index.blade.php`
- ✅ `resources/views/ketua/cuti/show.blade.php`

#### **Helpers:**
- ✅ `app/Helpers/FormatHelper.php` - Menambah notifKetua() dan notifPegawaiApprovedKetua()

#### **Seeders:**
- ✅ `database/seeders/HakimAndKetuaSeeder.php` - Sample data hakim dan ketua
- ✅ `database/seeders/DatabaseSeeder.php` - Include HakimAndKetuaSeeder

---

## Status Cuti (Updated)

Urutan status approval:
1. `menunggu` - Pengajuan baru (belum diproses Sub Kepegawaian)
2. `disetujui_hr` - Sudah disetujui Sub Kepegawaian
3. `disetujui_ketua` - Sudah disetujui ketua (hanya untuk pegawai)
4. `disetujui_pimpinan` - Sudah disetujui pimpinan (final approval)
5. `ditolak` - Ditolak oleh Sub Kepegawaian, ketua, atau pimpinan

---

## Logical Flow

### Flow Hakim:
1. Hakim login → Dashboard hakim
2. Hakim buat cuti → Status: `menunggu`
3. Sub Kepegawaian review → Disetujui → Status: `disetujui_hr` → Notif ke Pimpinan
4. Pimpinan review → Disetujui → Status: `disetujui_pimpinan` → Notif ke Hakim ✅
5. Atau ditolak → Status: `ditolak` → Notif ke Hakim ❌

### Flow Pegawai (Updated):
1. Pegawai login → Dashboard pegawai
2. Pegawai buat cuti → Status: `menunggu`
3. Sub Kepegawaian review → Disetujui → Status: `disetujui_hr` → Notif ke Ketua
4. Ketua review → Disetujui → Status: `disetujui_ketua` → Notif ke Pimpinan
5. Pimpinan review → Disetujui → Status: `disetujui_pimpinan` → Notif ke Pegawai ✅
6. Atau ditolak di mana saja → Status: `ditolak` → Notif ke Pegawai ❌

---

## Middleware (Role Check)

Routes sudah dilindungi dengan middleware role:

```php
Route::middleware(['auth', 'role:hakim'])->prefix('hakim')->name('hakim.')->group(function () {
    // Routes hakim
});

Route::middleware(['auth', 'role:ketua'])->prefix('ketua')->name('ketua.')->group(function () {
    // Routes ketua
});
```

Existing RoleMiddleware sudah support format ini.

---

## Testing Checklist

- [ ] Run migrations
- [ ] Run seeders (HakimAndKetuaSeeder)
- [ ] Login as Hakim (hakim@ptun.go.id)
  - [ ] Cek dashboard hakim
  - [ ] Buat pengajuan cuti
  - [ ] Edit pengajuan (sebelum HR approve)
  - [ ] Lihat detail pengajuan
- [ ] Login as Ketua (ketua@ptun.go.id)
  - [ ] Cek dashboard ketua
  - [ ] Lihat daftar cuti dari pegawai yang menunggu approval ketua
  - [ ] Approve cuti pegawai
  - [ ] Reject cuti pegawai dengan alasan
- [ ] Login as HR
  - [ ] Lihat cuti dari hakim
  - [ ] Approve cuti hakim (notif ke pimpinan)
  - [ ] Lihat cuti dari pegawai
  - [ ] Approve cuti pegawai (notif ke ketua)
- [ ] Login as Pimpinan
  - [ ] Lihat cuti dari hakim (disetujui_hr)
  - [ ] Lihat cuti dari pegawai (disetujui_ketua)
  - [ ] Approve kedua jenis cuti
  - [ ] Reject kedua jenis cuti
- [ ] Login as Pegawai
  - [ ] Buat pengajuan cuti
  - [ ] Track status approval (disetujui_hr → disetujui_ketua → disetujui_pimpinan)

---

## Troubleshooting

### Error: "Akses ditolak"
- Pastikan user sudah login dengan role yang tepat
- Cek routing berdasarkan role

### WA Notification tidak terkirim
- Pastikan nomor WA user sudah diisi
- Cek WaHelper.php dan integrasi Wablas

### Migration Error
- Pastikan run `php artisan migrate:fresh --seed` jika ada issue
- Atau jalankan secara terpisah: `migrate` lalu `db:seed`

---

## Fitur Tambahan yang Bisa Dikembangkan

1. **Email Notification** - Tambahan ke WA notification
2. **Approval History** - Track siapa yang approve/reject dan kapan
3. **Dashboard Analytics** - Statistik cuti per bulan/tahun
4. **Pengajuan Cuti Batch** - Pegawai bisa lihat rekan setim dan status cuti mereka
5. **Calendar View** - Tampilkan cuti dalam format kalender
6. **Export Report** - Export data cuti ke Excel/PDF

---

## Support & Contact

Untuk pertanyaan atau issues, silakan hubungi tim development.
