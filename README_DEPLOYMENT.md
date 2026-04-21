# PLPI Production Deployment Files Summary

## 📋 File yang Sudah Disiapkan

### 1. **QUICK_START_DEPLOY.md** (Baca Ini DULU!)
   - Quick reference guide
   - 5 langkah deploy
   - Checklist pre-deployment
   - **Recommended untuk di-baca pertama kali**

### 2. **DEPLOYMENT.md** (Detailed Guide)
   - Panduan lengkap deployment
   - Metode SSH dan FTP
   - Database migration
   - Troubleshooting
   - Rollback procedure

### 3. **EMAIL_SETUP.md** (Email Configuration)
   - Cara setup SMTP hosting
   - Common settings per provider
   - Troubleshooting email
   - Security best practices
   - Alternative email services

### 4. **.env.production** (Environment Template)
   - Production environment configuration
   - Semua setting yang diperlukan
   - Comments untuk guidance
   - **Update dengan credentials Anda!**

### 5. **deploy.sh** (Linux/Mac Automation)
   - Automated deployment script
   - Usage: `./deploy.sh production setup`
   - Usage: `./deploy.sh production migrate`
   - Platform: Linux, macOS, WSL

### 6. **deploy.bat** (Windows Automation)
   - Windows batch script version
   - Same functionality as deploy.sh
   - Usage: `deploy.bat production setup`
   - Platform: Windows

---

## 🚀 Quick Start Workflow

```
1. Baca: QUICK_START_DEPLOY.md (5 menit)
   ↓
2. Baca: DEPLOYMENT.md (10 menit) - Section yang relevan
   ↓
3. Baca: EMAIL_SETUP.md (10 menit) - Setup email
   ↓
4. Hubungi hosting support untuk:
   - Database credentials
   - SMTP credentials
   - SSH/FTP access
   ↓
5. Jalankan deploy.sh atau deploy.bat
   ↓
6. Test di: https://loa.ejurnal-unisap.ac.id
```

---

## 📝 Checklist Deployment

### Pre-Deployment (Local)
- [ ] Baca QUICK_START_DEPLOY.md
- [ ] Backup database lokal
- [ ] Backup file lokal
- [ ] Commit ke git
- [ ] Test aplikasi lokal terakhir kali

### Pre-Deployment (Hosting)
- [ ] Contact hosting support untuk credentials
- [ ] Create database di hosting
- [ ] Create email account di hosting
- [ ] Verify SSH/FTP access works
- [ ] Check PHP version (8.0+)
- [ ] Check Composer availability

### Deployment Steps
- [ ] Upload files via SSH/FTP
- [ ] Copy .env.production → .env
- [ ] Update .env dengan credentials
- [ ] Run: `./deploy.sh production setup`
- [ ] Run: `./deploy.sh production migrate`
- [ ] Test: https://loa.ejurnal-unisap.ac.id

### Post-Deployment
- [ ] Verify domain accessible
- [ ] Test admin login
- [ ] Test email send (Admin > Notifikasi)
- [ ] Check logs (writable/logs/)
- [ ] Verify SSL certificate
- [ ] Setup automatic backups

---

## 🔧 Configuration Summary

### Database (Update di .env)
```env
database.default.hostname = [DARI HOSTING]
database.default.database = [DARI HOSTING]
database.default.username = [DARI HOSTING]
database.default.password = [DARI HOSTING]
```

### Email (Update di .env)
```env
email.host = [DARI HOSTING - SMTP HOST]
email.port = [DARI HOSTING - SMTP PORT]
email.username = noreply@loa.ejurnal-unisap.ac.id
email.password = [DARI HOSTING - EMAIL PASSWORD]
email.crypto = tls [ATAU ssl - DARI HOSTING]
```

### Encryption
```env
# Generate key locally:
php -r "echo bin2hex(random_bytes(32));"
encryption.key = [HASILNYA COPY KESINI]
```

---

## 🐛 Troubleshooting Quick Reference

| Problem | Solution |
|---------|----------|
| Database connection error | Cek credentials di .env, verify MySQL running |
| Email not sending | Cek SMTP credentials, test telnet ke SMTP host |
| Permission denied | Run: `chmod -R 755 writable/` |
| White screen of death | Check: `writable/logs/log-*.log` |
| 404 on domain | Check: public_html path, Apache rewrite |
| Slow loading | Check: PHP version, Composer autoloader, opcache |

---

## 📞 Contact Information

### Hosting Support
- **Domain**: loa.ejurnal-unisap.ac.id
- **Provider**: [Your Hosting Provider]
- **Contact**: [Support Email/Phone]

### Application Support
- **Admin Panel**: https://loa.ejurnal-unisap.ac.id/admin
- **Logs**: `writable/logs/log-YYYY-MM-DD.log`
- **Database**: Check phpMyAdmin di cPanel

---

## 📚 Directory Structure After Deploy

```
public_html/
├── plpi/
│   ├── public/
│   │   ├── index.php
│   │   ├── uploads/         (writable)
│   │   └── assets/
│   ├── app/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   ├── Views/
│   │   └── Config/
│   ├── writable/            (must be writable)
│   │   ├── cache/
│   │   ├── logs/
│   │   ├── session/
│   │   └── uploads/
│   ├── .env                 (from .env.production)
│   └── deploy.sh
```

---

## ⚠️ IMPORTANT REMINDERS

1. **Never commit .env to git**
   - Add to .gitignore
   - Keep credentials secret

2. **Email credentials are critical**
   - Wrong credentials = no notifications sent
   - Test email functionality daily first week

3. **Database backup before migrate**
   - Migration can't be easily reverted
   - Keep local backup copy

4. **Monitor logs actively**
   - First week after deploy
   - Check for errors daily
   - Fix issues immediately

5. **SSL Certificate Required**
   - Use cPanel AutoSSL (free with most hosts)
   - Ensure app.baseURL uses https://

---

## 🎯 Expected Deployment Time

- Preparation: 30 minutes
- Upload files: 5-10 minutes (depending on size)
- Database migration: 2-5 minutes
- Testing: 10-15 minutes
- **Total: ~1 hour**

---

## 📖 Documentation Files Location

All files in project root:
```
QUICK_START_DEPLOY.md    ← Start here!
DEPLOYMENT.md
EMAIL_SETUP.md
.env.production
deploy.sh
deploy.bat
```

---

**Last Updated: 2026-04-21**
**Version: 1.0**
**For: PLPI v1.0 - CodeIgniter 4**
