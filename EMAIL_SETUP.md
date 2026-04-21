# Email Configuration Guide - Production Setup

## Email Configuration untuk PLPI Production

Aplikasi PLPI menggunakan SMTP untuk mengirim email notifikasi LoA ke penulis. Berikut adalah panduan lengkap setup email untuk production.

---

## 1. Mendapatkan SMTP Credentials dari Hosting

### Step 1: Akses cPanel Hosting
1. Login ke cPanel hosting Anda
2. Buka **Email Accounts** atau **Email Manager**
3. Buat email account baru atau gunakan yang ada

### Step 2: Catat SMTP Credentials
Informasi yang Anda butuhkan:
```
Email Address:  noreply@loa.ejurnal-unisap.ac.id
Password:       [Dari email account]
SMTP Host:      mail.loa.ejurnal-unisap.ac.id
               (atau mail.hosting-provider.com)
SMTP Port:      587 (TLS) atau 465 (SSL)
Encryption:     TLS atau SSL
```

---

## 2. Konfigurasi Email di .env Production

Edit file `.env.production`:

```env
#--------------------------------------------------------------------
# EMAIL CONFIGURATION
#--------------------------------------------------------------------
email.protocol = 'smtp'

# SMTP Server dari hosting
email.host = mail.loa.ejurnal-unisap.ac.id
email.port = 587

# Email credentials
email.username = noreply@loa.ejurnal-unisap.ac.id
email.password = PASSWORD_EMAIL_ANDA

# Encryption (tls untuk port 587, ssl untuk port 465)
email.crypto = tls

# Timeout connection
email.timeout = 30

# From address untuk email yang dikirim
email.from_email = noreply@loa.ejurnal-unisap.ac.id
email.from_name = PLPI - Sistem Manajemen LoA
```

---

## 3. Troubleshooting Email Configuration

### Masalah: Email Tidak Terkirim

#### Langkah 1: Cek Credentials
```bash
# SSH ke server
ssh username@loa.ejurnal-unisap.ac.id

# Test SMTP connection
telnet mail.loa.ejurnal-unisap.ac.id 587
# Harus ada response: 220 mail.xxx ESMTP
```

#### Langkah 2: Cek Log
```bash
# Check application logs
tail -f writable/logs/log-*.log
# Cari error keyword: "Email" atau "SMTP"
```

#### Langkah 3: Test Email via Dashboard
1. Login ke PLPI admin: https://loa.ejurnal-unisap.ac.id
2. Pergi ke: **Pengaturan > Notifikasi**
3. Klik tombol "Kirim Email Test"
4. Cek log untuk error details

---

## 4. Common SMTP Settings by Host

### Shared Hosting Providers

#### Bluehost
```
SMTP Host: mail.yourdomainname.com
SMTP Port: 587 (TLS) or 465 (SSL)
Username: noreply@loa.ejurnal-unisap.ac.id
Encryption: TLS
```

#### GoDaddy
```
SMTP Host: smtp.godaddy.com
SMTP Port: 587
Username: noreply@loa.ejurnal-unisap.ac.id
Encryption: TLS
```

#### Hostinger
```
SMTP Host: smtp.hostinger.com
SMTP Port: 587
Username: noreply@loa.ejurnal-unisap.ac.id
Encryption: TLS
```

#### Niagahoster
```
SMTP Host: mail.loa.ejurnal-unisap.ac.id (atau smtp.niagahoster.com)
SMTP Port: 587
Username: noreply@loa.ejurnal-unisap.ac.id
Encryption: TLS
```

#### Local Hosting (Indonesia)
```
SMTP Host: mail.domainanda.com
SMTP Port: 25, 587, or 465 (tanyakan ke support)
Username: noreply@loa.ejurnal-unisap.ac.id
Encryption: TLS atau SSL
```

---

## 5. Alternative Email Services

### Menggunakan Gmail SMTP (Tidak Recommended untuk Production)

```env
email.protocol = 'smtp'
email.host = smtp.gmail.com
email.port = 587
email.username = your-email@gmail.com
email.password = app-specific-password
email.crypto = tls
```

**Note**: Gunakan App Password (bukan password gmail biasa)
- Setup: https://support.google.com/accounts/answer/185833

### Menggunakan SendGrid (Recommended Alternative)

```env
email.protocol = 'smtp'
email.host = smtp.sendgrid.net
email.port = 587
email.username = apikey
email.password = SG.xxxxxxxxxxxxxxxxxxx
email.crypto = tls
```

---

## 6. Email Template Configuration

File email template berada di: `app/Views/email/`

### Template yang Tersedia:
- `loa_approved_notification.php` - Notifikasi LoA Approved ke penulis

### Customize Template:
Edit file template sesuai kebutuhan Anda (branding, format, dll)

---

## 7. Monitoring dan Maintenance

### Daily Checks
1. Monitor log file untuk email errors
2. Test send email setiap minggu
3. Monitor SMTP quota (jika ada limit)

### Monthly Tasks
1. Review email delivery rate
2. Clean up old logs
3. Update credentials jika ada perubahan

### Log Location
```
writable/logs/log-YYYY-MM-DD.log
```

Cari line yang berisi "Email" untuk melihat activity:
```bash
grep -i email writable/logs/log-*.log
```

---

## 8. Security Best Practices

### Do's ✓
- Gunakan SMTP TLS/SSL untuk encryption
- Jangan share credentials
- Keep `.env` file private (mode 600)
- Gunakan dedicated email account untuk app
- Monitor email usage

### Don'ts ✗
- Jangan commit `.env` ke git repository
- Jangan expose credentials di log
- Jangan gunakan personal email
- Jangan gunakan plain password tanpa encryption

---

## 9. Contoh Email Log Output

### Successful Email
```
[2026-04-21 10:30:45] Email: Successfully sent email to: penulis@example.com
[2026-04-21 10:30:45] Email: Subject: Notifikasi LoA Approved - 001/LOA/...
```

### Failed Email
```
[2026-04-21 10:30:45] ERROR - Email send failed. SMTP Error:
[2026-04-21 10:30:45] ERROR - Unable to connect to SMTP server: mail.hosting.com:587
[2026-04-21 10:30:45] ERROR - Username or password incorrect
```

---

## 10. Contact Support

Jika mengalami masalah:
1. Cek log file untuk error message
2. Hubungi hosting provider untuk verify SMTP settings
3. Test connection manual via telnet/openssl
4. Review documentation di: app/Config/Email.php

---

## Checklist Pre-Production

- [ ] Email account created di hosting
- [ ] SMTP credentials collected and verified
- [ ] `.env.production` updated dengan credentials
- [ ] Test email berhasil terkirim
- [ ] Log file tidak ada error
- [ ] Permissions set correctly (writable folder)
- [ ] SSL certificate active di domain
- [ ] Backup database tersedia

---

## Reference

- CodeIgniter Email Class: https://codeigniter.com/user_guide/libraries/email.html
- SMTP Ports: https://kinsta.com/blog/smtp-port/
- Email Best Practices: https://www.mailgun.com/blog/email-best-practices/
