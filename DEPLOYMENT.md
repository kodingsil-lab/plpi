# Panduan Deployment PLPI ke Production

## Informasi Server

- Domain: `loa.unisap.ac.id`
- Project path: `/home/loaunisa/plpi`
- Branch deploy: `main`
- Environment: `production`
- Hosting: shared hosting/cPanel dengan akses SSH

## Metode Deploy yang Dipakai

Metode yang terbukti berhasil adalah deploy manual berbasis Git langsung di server.

```bash
cd /home/loaunisa/plpi
git fetch origin main
git checkout main
git pull --ff-only origin main
php composer.phar install --no-dev --prefer-dist --optimize-autoloader
php spark migrate --all
php spark cache:clear
```

## Fungsi Tiap Command

- `git fetch origin main`: mengambil update terbaru dari remote
- `git checkout main`: memastikan branch deploy aktif
- `git pull --ff-only origin main`: menarik perubahan terbaru tanpa merge commit
- `php composer.phar install --no-dev --prefer-dist --optimize-autoloader`: install dependency production
- `php spark migrate --all`: menjalankan migration terbaru
- `php spark cache:clear`: membersihkan cache aplikasi

## Verifikasi Setelah Deploy

Jalankan:

```bash
cd /home/loaunisa/plpi
php spark env
php spark migrate:status
```

Pastikan:

- environment adalah `production`
- migration sudah terpasang semua

## Konfigurasi Environment

File template yang dipakai adalah `.env.production`.

Pastikan `.env` di server sudah sesuai kebutuhan production:

```env
CI_ENVIRONMENT = production
app.baseURL = 'https://loa.unisap.ac.id/'
database.default.hostname = localhost
database.default.database = ...
database.default.username = ...
database.default.password = ...
email.host = ...
email.port = 587
email.username = ...
email.password = ...
CI_DEBUG = false
```

## Troubleshooting

### 1. `bash: ./deploy.sh: Permission denied`

Solusi:

```bash
chmod +x deploy.sh
```

atau:

```bash
bash deploy.sh production deploy
```

### 2. `[ERROR] Composer not found: composer`

Penyebab:

- server tidak menyediakan command `composer` global

Solusi:

Gunakan:

```bash
php composer.phar install --no-dev --prefer-dist --optimize-autoloader
```

### 3. Working tree tidak bersih

Cek:

```bash
git status --short
```

Jika ada output, bereskan dulu sebelum menjalankan deploy otomatis.

## Rekomendasi

Untuk update rutin production, pakai panduan di [GIT_DEPLOY.md](./GIT_DEPLOY.md) sebagai referensi utama.
