# QUICK DEPLOYMENT GUIDE - PLPI ke loa.ejurnal-unisap.ac.id

## TL;DR - Deploy dalam 5 Langkah

### STEP 1: Persiapan Lokal
```bash
cd /path/to/plpi
git add .
git commit -m "Pre-deployment commit"
```

### STEP 2: Hubungi Hosting untuk Info
Dapatkan dari support hosting:
```
✓ Database credentials (hostname, username, password, db name)
✓ SMTP credentials (host, port, username, password)
✓ SSH access (jika tersedia)
✓ FTP credentials (jika SSH tidak tersedia)
```

### STEP 3: Upload ke Server

**Opsi A: Via SSH (Recommended)**
```bash
# Dari local machine
scp -r . username@loa.ejurnal-unisap.ac.id:~/public_html/plpi
# atau gunakan rsync untuk lebih cepat
rsync -avz --delete ./ username@loa.ejurnal-unisap.ac.id:~/public_html/plpi/
```

**Opsi B: Via FTP**
```
Gunakan FileZilla:
1. Host: loa.ejurnal-unisap.ac.id
2. Username: FTP username
3. Password: FTP password
4. Upload ke: public_html/plpi
```

### STEP 4: SSH ke Server & Setup
```bash
ssh username@loa.ejurnal-unisap.ac.id
cd public_html/plpi

# Jalankan script setup
chmod +x deploy.sh
./deploy.sh production setup
```

Saat diminta, update `.env` dengan:
- Database credentials
- Email SMTP credentials
- Encryption key (sudah auto-generate)

### STEP 5: Jalankan Migration
```bash
./deploy.sh production migrate
```

**DONE! ✓ Visit: https://loa.ejurnal-unisap.ac.id**

---

## Jika Tidak Ada SSH Access (FTP Only)

1. Upload semua file via FTP ke `public_html/plpi`
2. Edit `.env.production` lokal dengan credentials
3. Upload `.env.production` ke server sebagai `.env`
4. Buka browser: `https://loa.ejurnal-unisap.ac.id/public`
5. Jalankan: `https://loa.ejurnal-unisap.ac.id/public/spark` → migrate

**Note**: Ini lebih sulit, SSH lebih recommended

---

## Email Setup (CRITICAL)

### Di cPanel Hosting:
1. **Create Email Account**:
   - Email: `noreply@loa.ejurnal-unisap.ac.id`
   - Generate random password

2. **Catat SMTP Info**:
   - Host: `mail.loa.ejurnal-unisap.ac.id`
   - Port: `587` (atau cek dengan support)
   - Username: `noreply@loa.ejurnal-unisap.ac.id`
   - Password: [Password yang di-generate]

3. **Update di .env.production**:
   ```env
   email.host = mail.loa.ejurnal-unisap.ac.id
   email.port = 587
   email.username = noreply@loa.ejurnal-unisap.ac.id
   email.password = PASSWORD_YANG_SUDAH_DICATAT
   email.crypto = tls
   ```

4. **Test Email**:
   - Login ke PLPI
   - Go to: Admin > Pengaturan > Notifikasi
   - Click: "Kirim Email"
   - Check log: `writable/logs/log-YYYY-MM-DD.log`

---

## Database Setup

### Via cPanel:
1. **Create Database**:
   - Go to: cPanel > MySQL Databases
   - Database: `plpi_prod` (atau sesuai pilihan)
   - User: `plpi_user`
   - Password: Generate strong password

2. **Update .env.production**:
   ```env
   database.default.hostname = localhost
   database.default.database = plpi_prod
   database.default.username = plpi_user
   database.default.password = PASSWORD_YANG_DICATAT
   ```

3. **Run Migration**:
   ```bash
   ./deploy.sh production migrate
   ```

---

## Post-Deployment Checklist

```
□ Domain accessible: https://loa.ejurnal-unisap.ac.id
□ Admin login works: admin login page loads
□ Database connected: no "database connection" errors
□ Email configured: test email sends
□ SSL active: green lock icon shows
□ Logs checking: no error in writable/logs/
□ Uploads folder writable: can create test file
□ Cache working: no cache errors
```

---

## Files Included

- `DEPLOYMENT.md` - Detailed deployment guide
- `EMAIL_SETUP.md` - Email configuration guide  
- `deploy.sh` - Automated deployment script (Linux/Mac)
- `deploy.bat` - Deployment script (Windows)
- `.env.production` - Production environment template

---

## Support & Troubleshooting

### Email Not Sending?
```bash
# SSH ke server
tail -f writable/logs/log-*.log
# Cari "Email" atau "SMTP"
```

### Database Migration Error?
```bash
# SSH ke server
php spark migrate:refresh  # Warning: Clears database!
php spark migrate          # Re-run migrations
```

### Permission Error?
```bash
chmod -R 755 writable/
chmod -R 644 public/uploads/
chmod 600 .env
```

### Cache Issues?
```bash
php spark cache:clear
php spark views:cache     # Optional, cache views
```

---

## Need Help?

1. Check documentation files in this directory
2. Review application logs: `writable/logs/`
3. Check cPanel error logs
4. Run diagnostic test: `php spark test`

**Deployment Document Created: 2026-04-21**

---

## Next Steps After Deploy

1. **Configure Email Notifications**
   - Test dengan mengirim email dari admin panel
   - Monitor: Admin > Notifikasi

2. **Setup Backups**
   - Schedule automatic backups di cPanel
   - Set retention: minimum 7 days

3. **Monitor Performance**
   - Check server resources weekly
   - Monitor email delivery rate
   - Review security logs

4. **Maintenance Schedule**
   - Weekly: Test email functionality
   - Monthly: Security audit, logs review
   - Quarterly: Framework & dependencies update

