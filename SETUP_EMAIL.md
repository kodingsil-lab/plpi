# Email Configuration Setup untuk PLPI

## Cara Mengkonfigurasi Email Notifikasi

Fitur pengiriman email notifikasi LoA kepada penulis sudah diintegrasikan. Ikuti langkah-langkah berikut:

### 1. Update File `.env`

Buka file `.env` di root project dan update bagian EMAIL CONFIGURATION:

```env
#--------------------------------------------------------------------
# EMAIL CONFIGURATION
#--------------------------------------------------------------------

# Protocol: mail (PHP mail), sendmail, atau smtp
email.protocol = 'smtp'

# SMTP Configuration
email.host = 'smtp.gmail.com'        # SMTP server hostname
email.port = 587                      # Port SMTP (587 untuk TLS, 465 untuk SSL)
email.username = 'your-email@gmail.com'  # Email address
email.password = 'your-app-password'     # App password (bukan password akun)
email.crypto = 'tls'                  # Encryption: tls atau ssl
email.auth_method = 'login'           # Authentication method
email.timeout = 5                     # Timeout dalam detik

# From Email
email.from_email = 'noreply@plpi.id'
email.from_name = 'PLPI - Sistem Manajemen LoA'
```

### 2. Pengaturan SMTP oleh Email Provider

#### Gmail
```
Host: smtp.gmail.com
Port: 587
Encryption: TLS
Username: your-email@gmail.com
Password: App Password (bukan password akun normal)
  - Buat di: https://myaccount.google.com/apppasswords
  - Pilih app: Mail
  - Pilih device: Windows Computer
  - Copy password yang dihasilkan
```

#### Sendinblue
```
Host: smtp-relay.sendinblue.com
Port: 587
Encryption: TLS
Username: your-email@sendinblue.com
Password: SMTP key Anda
```

#### Mailgun
```
Host: smtp.mailgun.org
Port: 587
Encryption: TLS
Username: postmaster@yourdomain.com
Password: SMTP Password dari Mailgun
```

#### Google Workspace (Email Domain)
```
Host: smtp.google.com
Port: 587
Encryption: TLS
Username: your-email@yourdomain.com
Password: App Password dari Google Account
```

#### Server Lokal (Hosting)
Tanyakan kepada penyedia hosting untuk:
- SMTP Host
- SMTP Port
- Username dan Password

### 3. Update Config Email (Optional)

Jika ingin menggunakan environment variables, edit file `app/Config/Email.php` dan tambahkan:

```php
public string $protocol = env('email.protocol', 'mail');
public string $SMTPHost = env('email.host', '');
public int $SMTPPort = (int) env('email.port', 587);
public string $SMTPUser = env('email.username', '');
public string $SMTPPass = env('email.password', '');
public string $SMTPCrypto = env('email.crypto', 'tls');
public string $fromEmail = env('email.from_email', 'noreply@plpi.id');
public string $fromName = env('email.from_name', 'PLPI');
```

### 4. Fitur Notifikasi di Admin

Setelah konfigurasi email:

1. Buka menu **Admin > Notifikasi**
2. Akan tampil daftar LoA yang sudah dipublikasikan
3. Klik tombol **Kirim Email** (icon amplop) untuk mengirim notifikasi ke penulis
4. Status akan berubah menjadi **Notifikasi Terkirim**

### 5. Testing Email

Untuk test konfigurasi SMTP tanpa mengirim email nyata:

#### Menggunakan Mailtrap (Test SMTP Service)
1. Daftar di https://mailtrap.io
2. Buat inbox baru
3. Copy SMTP credentials ke `.env`:
```env
email.protocol = 'smtp'
email.host = 'smtp.mailtrap.io'
email.port = 2525
email.username = 'xxx'
email.password = 'xxx'
email.crypto = 'tls'
```
4. Test pengiriman email
5. Lihat email di Mailtrap inbox

#### Menggunakan MailHog (Local Testing)
Jika menggunakan Laragon, MailHog sudah built-in:
```env
email.protocol = 'smtp'
email.host = '127.0.0.1'
email.port = 1025
email.username = ''
email.password = ''
```

### 6. Troubleshooting

**Error: "Connection timeout"**
- Pastikan SMTP Host dan Port benar
- Cek apakah firewall/ISP memblokir port SMTP
- Coba port alternatif (2525 untuk TLS)

**Error: "Authentication failed"**
- Pastikan username dan password benar
- Untuk Gmail: gunakan App Password, bukan password akun normal
- Cek apakah akun sudah enable "Less secure apps" (jika diperlukan)

**Email tidak terkirim tapi tidak ada error**
- Cek di file log: `writable/logs/`
- Pastikan protokol email di Config sudah benar

### 7. Struktur File Implementasi

```
app/
├── Controllers/Admin/
│   └── NotificationController.php  (Updated dengan email sending)
├── Libraries/
│   └── EmailService.php             (New - Email service class)
└── Views/
    └── email/
        └── loa_approved_notification.php  (New - Email template)

Config/
└── Email.php                        (Updated dengan konfigurasi)

.env                                 (Updated dengan email settings)
```

### 8. Fitur Email

- **Recipient**: Email dari kolom `corresponding_email` di data LoA
- **Subject**: "Notifikasi LoA Approved - [No LoA]"
- **Content**: Template HTML profesional dengan:
  - Header dengan logo jurnal
  - Detail LoA (nomor, judul, penulis, tanggal terbit)
  - Penjelasan LoA
  - QR code verification info
  - Footer dengan kontakt penerbit
- **Attachment**: PDF LoA otomatis dilampirkan

Semoga berhasil! Hubungi support jika ada pertanyaan.
