# CI4 Scaffold Cepat (Starter)

## 1) Buat Project CI4 Baru

```bash
composer create-project codeigniter4/appstarter loa-center-ci4
cd loa-center-ci4
composer require codeigniter4/shield
composer require dompdf/dompdf
composer require endroid/qr-code
```

## 2) Setup Environment

```bash
cp env .env
php spark key:generate
```

Atur:
- `app.baseURL`
- `database.default.*`
- `app.CSPEnabled` (sesuaikan kebutuhan)

## 3) Jalankan Migrasi Dasar

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

## 4) Struktur Folder Disarankan

```txt
app/
  Controllers/
    Admin/
    Public/
    Auth/
  Models/
  Filters/
    RoleFilter.php
  Libraries/
    LoaPdfService.php
    LoaNumberService.php
  Config/
    Routes.php
```

## 5) Route Blueprint CI4 (Contoh)

```php
$routes->get('/', 'Public\\HomeController::index');

$routes->group('admin', ['filter' => 'sessionauth,role:superadmin,admin_jurnal'], static function ($routes) {
    $routes->get('loa-requests', 'Admin\\LoaRequestController::index');
    $routes->get('loa-letters', 'Admin\\LoaLetterController::index');
});
```

## 6) Sprint Implementasi Disarankan

1. Auth + Role + Layout admin.
2. Public LoA request + status + verify.
3. Admin LoA request (approve/reject).
4. Admin LoA letter (edit/regenerate/download).
5. Notifikasi + email + CSV export.
6. Modul setting (journal/publisher/user/profile).
7. Hardening (rate limit, audit log, security header).

## 7) Acceptance Test Minimum

- Login sesuai role.
- Submit LoA publik.
- Approve request -> LoA letter terbentuk.
- Preview & download PDF berhasil.
- Verifikasi LoA publik valid.
- Hapus data tetap aman (confirm + audit).

