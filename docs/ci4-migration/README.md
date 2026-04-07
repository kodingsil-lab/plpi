# Migrasi Laravel -> CodeIgniter 4 (Project LoA Center)

Dokumen ini adalah paket persiapan migrasi dari project Laravel saat ini ke CI4, dengan fokus agar fitur inti tetap aman saat cutover.

## 1) Scope Fitur Yang Harus Dipertahankan

- Auth admin (login, logout, lupa password)
- Role access (`superadmin`, `admin_jurnal`)
- Dashboard admin + statistik
- Permohonan LoA (list, detail, approve, reject, export CSV)
- Letter of Acceptance (list, edit, delete, bulk delete, regenerate, export CSV)
- Verifikasi LoA publik (berdasarkan token dan nomor LoA)
- Generate PDF + preview + download
- Pengaturan jurnal, publisher, user, notifikasi
- Upload file (logo, tanda tangan, aset pendukung)

## 2) Dependency Yang Perlu Dicari Padanannya di CI4

- `spatie/laravel-permission` -> CI4 Shield Group/Permission (disarankan)
- `barryvdh/laravel-dompdf` -> Dompdf native di CI4 service
- `simplesoftwareio/simple-qrcode` -> `endroid/qr-code` atau ext sejenis di CI4
- Breeze auth -> CI4 Shield / custom auth module

## 3) Struktur Migrasi Yang Disarankan

1. Kunci struktur database final.
2. Migrasi auth + role/permission dulu.
3. Migrasi modul publik LoA (submit/status/verify).
4. Migrasi modul admin LoA request + LoA letter.
5. Migrasi PDF/email/notifikasi.
6. Migrasi setting lanjutan (publisher, users, journal profile).
7. UAT + parallel run + cutover.

## 4) Target Arsitektur CI4

- `app/Controllers/Admin/*` untuk semua backend admin
- `app/Controllers/Public/*` untuk endpoint publik
- `app/Models/*` per tabel utama
- `app/Filters/RoleFilter.php` (alias `role`)
- `app/Libraries/LoaPdfService.php`, `LoaNumberService.php`
- `app/Views/layouts/*`, `app/Views/admin/*`, `app/Views/public/*`

## 5) File Referensi

- Route mapping: [ROUTE_MAP.md](./ROUTE_MAP.md)
- Mapping model/controller/service: [SYSTEM_MAP.md](./SYSTEM_MAP.md)
- Urutan migrasi DB: [DB_MIGRATION_ORDER.md](./DB_MIGRATION_ORDER.md)
- Perintah scaffold awal CI4: [CI4_SCAFFOLD_COMMANDS.md](./CI4_SCAFFOLD_COMMANDS.md)

## 6) Definition of Done (Minimum)

- Semua route utama berjalan.
- Login + role gating sesuai Laravel.
- PDF LoA hasilnya sama (layout, nomor, tanda tangan, QR/verify).
- Data lama terbaca aman (users, journals, requests, letters).
- Tidak ada broken link pada endpoint publik (`/loa/v/{token}`, `/loa/verify`).

