# Quick Start Deploy PLPI

Panduan ini mengikuti alur deploy yang sudah terbukti berhasil di hosting `loa.unisap.ac.id`.

## TL;DR

Jalankan di server:

```bash
cd /home/loaunisa/plpi
git fetch origin main
git checkout main
git pull --ff-only origin main
php composer.phar install --no-dev --prefer-dist --optimize-autoloader
php spark migrate --all
php spark cache:clear
```

## Verifikasi Cepat

```bash
cd /home/loaunisa/plpi
php spark env
php spark migrate:status
```

Hasil yang diharapkan:

- environment = `production`
- semua migration sudah terpasang

## Catatan Penting

- Project path di server: `/home/loaunisa/plpi`
- Domain production: `https://loa.unisap.ac.id/`
- Server ini tidak memakai `composer` global
- Dependency di-install dengan `php composer.phar ...`
- Jangan pakai `./deploy.sh production setup` karena action `setup` tidak ada

## Jika `deploy.sh` Gagal

Masalah yang sudah ditemukan di server:

- `Permission denied` pada `deploy.sh`
- `Composer not found: composer`
- working tree kotor saat ada `composer.phar` atau perubahan mode file

Karena itu, untuk saat ini alur paling aman adalah deploy manual dengan command Git di atas.

## Referensi

- Panduan detail: [DEPLOYMENT.md](./DEPLOYMENT.md)
- Panduan Git deploy: [GIT_DEPLOY.md](./GIT_DEPLOY.md)
