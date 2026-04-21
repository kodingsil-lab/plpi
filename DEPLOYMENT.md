# Panduan Deployment PLPI ke Hosting Production

## Informasi Hosting
- **Domain**: loa.ejurnal-unisap.ac.id
- **Hosting Type**: Shared Hosting dengan cPanel
- **Email Service**: SMTP dari Hosting

---

## Persiapan Sebelum Deploy

### 1. Informasi yang Dibutuhkan
Hubungi hosting provider untuk mendapatkan:
- [ ] SSH Access (username, password/key)
- [ ] Database credentials (hostname, username, password, database name)
- [ ] Email SMTP credentials (hostname, port, username, password)
- [ ] FTP credentials (jika tidak ada SSH)
- [ ] PHP version (minimal 8.0)
- [ ] Composer tersedia di server

### 2. Backup Data Lokal
```bash
# Backup database
# Database saat ini: plpi

# Backup files
tar -czf ~/plpi-backup-$(date +%Y%m%d).tar.gz ./
```

---

## Metode 1: Deploy via SSH (Recommended)

### Step 1: Upload Files via SFTP/SCP
```bash
# Dari local machine, upload ke server
scp -r . username@loa.ejurnal-unisap.ac.id:~/public_html/plpi
# atau gunakan FileZilla dengan SFTP
```

### Step 2: SSH ke Server
```bash
ssh username@loa.ejurnal-unisap.ac.id
cd public_html/plpi
```

### Step 3: Jalankan Script Setup
```bash
# Copy script deploy
chmod +x deploy.sh
./deploy.sh production
```

---

## Metode 2: Deploy via FTP (Jika SSH Tidak Tersedia)

### Step 1: Upload via FTP
- Gunakan FileZilla atau WinSCP
- Upload semua file ke `public_html/plpi`
- Set permission 755 untuk folder, 644 untuk file

### Step 2: Setup Manual di cPanel
1. Buka File Manager di cPanel
2. Navigate ke `public_html/plpi`
3. Run terminal / SSH dari cPanel jika tersedia
4. Jalankan: `bash deploy.sh production`

---

## Konfigurasi Environment Production

File `.env.production` sudah disiapkan. Anda perlu update:

```env
# APP Configuration
app.baseURL = 'https://loa.ejurnal-unisap.ac.id/'
CI_ENVIRONMENT = production

# DATABASE
database.default.hostname = [FROM HOSTING]
database.default.database = [FROM HOSTING]
database.default.username = [FROM HOSTING]
database.default.password = [FROM HOSTING]

# EMAIL CONFIGURATION
email.protocol = 'smtp'
email.host = [SMTP HOST FROM HOSTING]
email.port = [SMTP PORT - usually 587 or 465]
email.username = [EMAIL FROM HOSTING]
email.password = [EMAIL PASSWORD]
email.crypto = [tls atau ssl, biasanya tls]
email.timeout = 30
```

---

## Database Migration Production

### Option 1: Import via phpMyAdmin (cPanel)
1. Buka phpMyAdmin di cPanel
2. Buat database baru sesuai credentials
3. Import file migration atau SQL dump
4. Run migrations: `php spark migrate`

### Option 2: Via Command Line
```bash
cd public_html/plpi
php spark migrate
```

---

## Post-Deployment Checklist

- [ ] Update `.env` dengan production credentials
- [ ] Run database migrations: `php spark migrate`
- [ ] Set folder permissions:
  ```bash
  chmod 755 writable/ public/uploads/
  chmod 644 writable/*
  chmod 644 public/uploads/*
  ```
- [ ] Clear cache:
  ```bash
  php spark cache:clear
  ```
- [ ] Test aplikasi: https://loa.ejurnal-unisap.ac.id
- [ ] Setup SSL Certificate (cPanel -> AutoSSL)
- [ ] Configure firewall/security

---

## Email Configuration Production

### SMTP Server Hosting
Hosting provider biasanya menyediakan:
- **Host**: mail.loa.ejurnal-unisap.ac.id atau smtp.hosting-provider.com
- **Port**: 587 (TLS) atau 465 (SSL)
- **Username**: noreply@loa.ejurnal-unisap.ac.id (atau sesuai akun email hosting)
- **Password**: [Sesuai setting hosting]

### Test Email Configuration
```bash
# Buat test script atau gunakan dashboard admin
# Cek: Pengaturan > Notifikasi > Test Email
```

---

## Troubleshooting

### Jika Migration Gagal
```bash
php spark migrate:refresh  # Untuk reset (WARNING: Hapus data!)
php spark migrate          # Jalankan ulang
```

### Jika Email Tidak Terkirim
1. Cek credentials di `.env`
2. Cek log: `writable/logs/log-YYYY-MM-DD.log`
3. Test connection SMTP:
   ```bash
   telnet mail.hosting.com 587
   ```

### Jika Permission Error
```bash
chmod -R 755 writable/
chmod -R 644 public/uploads/
```

---

## Rollback ke Lokal

Jika deployment bermasalah:
```bash
# Restore dari backup lokal
tar -xzf ~/plpi-backup-YYYYMMDD.tar.gz
# Update .env kembali ke local configuration
```

---

## Support & Maintenance

- **Log files**: `writable/logs/`
- **Cache**: `writable/cache/`
- **Sessions**: `writable/session/`
- **Uploads**: `public/uploads/`

Untuk maintenance, pastikan folder ini dapat ditulis oleh web server.

