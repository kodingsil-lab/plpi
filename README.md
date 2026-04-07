# PLPI

**PUSAT LAYANAN PUBLIKASI ILMIAH (PLPI)**  
Sistem Informasi Pengelolaan LoA, Invoice, dan Layanan Jurnal

## Status Implementasi Saat Ini

- Basis project: CodeIgniter 4
- Template style: diport dari `sapa-lppm` (aset `public/assets`)
- Modul LoA yang sudah aktif (fondasi + logic utama):
  - Public: ajukan LoA, cek status, verifikasi nomor LoA, lihat LoA by token
  - Admin: dashboard, daftar permohonan, daftar LoA terbit
  - PDF: preview/download LoA via Dompdf

## Setup Cepat

1. Install dependency
```bash
composer install
```

2. Pastikan `.env` tersedia (sudah disiapkan dari `env`)
- `app.baseURL = 'http://plpi.test/'`
- `database.default.database = plpi`
- user/password default local: `root` / kosong

3. Buat database dan migrate
```bash
php spark db:create plpi
php spark migrate -n App
```

4. Jalankan server
```bash
php spark serve --host 0.0.0.0 --port 8080
```

## URL Penting

- Public home: `/`
- Ajukan LoA: `/loa/request`
- Verifikasi LoA: `/loa/verify`
- Login: `/login`
- Dashboard admin: `/dashboard`
- Permohonan LoA: `/admin/loa-requests`
- LoA terbit: `/admin/loa-letters`

## Catatan

- Login saat ini masih mode sederhana (session dummy) untuk percepatan porting UI + alur.
- Batch berikutnya: hardening auth/role, audit log, email notification, dan integrasi invoice module.
- Dokumen migrasi detail ada di folder: `docs/ci4-migration`.

