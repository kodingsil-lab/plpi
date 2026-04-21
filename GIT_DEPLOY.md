# Panduan Git Deploy PLPI

Panduan ini dibuat berdasarkan alur deploy yang benar-benar berhasil di hosting `loa.unisap.ac.id`.

## Kondisi Server

- Project path: `/home/loaunisa/plpi`
- Branch deploy: `main`
- Environment: `production`
- Composer global: tidak tersedia
- Composer dipanggil lewat `composer.phar`

## Command Deploy yang Dipakai

Jalankan dari SSH:

```bash
cd /home/loaunisa/plpi
git fetch origin main
git checkout main
git pull --ff-only origin main
php composer.phar install --no-dev --prefer-dist --optimize-autoloader
php spark migrate --all
php spark cache:clear
```

## Penjelasan Singkat

- `git fetch origin main`: ambil update terbaru dari remote
- `git checkout main`: pastikan ada di branch deploy
- `git pull --ff-only origin main`: tarik update terbaru tanpa merge commit
- `php composer.phar install --no-dev --prefer-dist --optimize-autoloader`: install dependency production
- `php spark migrate --all`: jalankan migration terbaru
- `php spark cache:clear`: bersihkan cache aplikasi

## Verifikasi Setelah Deploy

Jalankan:

```bash
cd /home/loaunisa/plpi
php spark env
php spark migrate:status
```

Hasil yang diharapkan:

- `php spark env` menampilkan `production`
- `php spark migrate:status` menampilkan semua migration sudah masuk

## Troubleshooting

### 1. `bash: ./deploy.sh: Permission denied`

Penyebab:
- file `deploy.sh` belum executable

Solusi:

```bash
chmod +x deploy.sh
```

Atau jalankan langsung dengan bash:

```bash
bash deploy.sh production deploy
```

### 2. `[ERROR] Composer not found: composer`

Penyebab:
- server tidak punya command `composer` di PATH

Solusi yang dipakai di server ini:

```bash
php composer.phar install --no-dev --prefer-dist --optimize-autoloader
```

Catatan:
- karena itu, alur deploy yang paling aman saat ini adalah deploy manual dengan command Git di atas
- `deploy.sh` bawaan belum cocok langsung untuk server ini tanpa penyesuaian tambahan

### 3. `git status --short` menampilkan file kotor

Contoh:

```bash
?? composer.phar
 M deploy.sh
```

Penyebab umum:
- ada file baru yang belum di-ignore
- permission `deploy.sh` berubah setelah `chmod +x`

Solusi:

```bash
git status --short
```

Pastikan hasilnya bersih sebelum memakai alur deploy otomatis berbasis script.

## Rekomendasi Alur Pakai

Untuk update rutin, pakai urutan ini:

```bash
cd /home/loaunisa/plpi
git status --short
git fetch origin main
git checkout main
git pull --ff-only origin main
php composer.phar install --no-dev --prefer-dist --optimize-autoloader
php spark migrate --all
php spark cache:clear
```

Kalau `git status --short` masih ada output, bereskan dulu sebelum lanjut deploy.

## Checklist Singkat

- sudah SSH ke server
- sudah masuk ke `/home/loaunisa/plpi`
- branch aktif `main`
- `.env` production sudah benar
- `composer.phar` tersedia
- migration sukses
- cache sudah dibersihkan
- website berhasil dibuka di `https://loa.unisap.ac.id/`
