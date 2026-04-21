# Ringkasan File Deployment PLPI

## File Utama

### 1. `QUICK_START_DEPLOY.md`

Panduan singkat untuk deploy rutin di server production.

### 2. `GIT_DEPLOY.md`

Panduan Git deploy yang sesuai dengan kondisi server saat ini:

- path project: `/home/loaunisa/plpi`
- branch deploy: `main`
- composer dipanggil lewat `php composer.phar`

### 3. `DEPLOYMENT.md`

Panduan deployment detail dan troubleshooting.

### 4. `.env.production`

Template environment production.

## Alur Deploy yang Dipakai

Deploy update dilakukan dengan command berikut:

```bash
cd /home/loaunisa/plpi
git fetch origin main
git checkout main
git pull --ff-only origin main
php composer.phar install --no-dev --prefer-dist --optimize-autoloader
php spark migrate --all
php spark cache:clear
```

## Kenapa Tidak Pakai `deploy.sh` Langsung

Di server production saat ini ada beberapa kendala:

- `deploy.sh` sempat perlu `chmod +x`
- command `composer` global tidak tersedia
- server memakai `composer.phar`
- script otomatis sensitif terhadap working tree yang tidak bersih

Karena itu, deploy manual via Git lebih stabil untuk sekarang.

## Verifikasi Setelah Deploy

```bash
cd /home/loaunisa/plpi
php spark env
php spark migrate:status
```

## Domain Production

- `https://loa.unisap.ac.id/`

## Urutan Baca

1. `QUICK_START_DEPLOY.md`
2. `GIT_DEPLOY.md`
3. `DEPLOYMENT.md`
