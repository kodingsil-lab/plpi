# System Map (Laravel Saat Ini -> CI4 Target)

## Model Utama

- `User`
- `Publisher`
- `Journal`
- `JournalProfile`
- `LoaRequest`
- `LoaLetter`
- `LoaNotification`
- `AuditLog`

## Controller Admin

- `DashboardController`
- `LoaRequestAdminController`
- `LoaLetterAdminController`
- `JournalAdminController`
- `JournalAssignmentAdminController`
- `PublisherAdminController`
- `AdminUserController`
- `LoaNotificationController`
- `Settings\JournalProfileController`

## Controller Public

- `PublicHomeController`
- `LoaRequestPublicController`
- `LoaLetterPublicController`
- `LoaVerifyController`

## Service & Bisnis Logic

- `LoaNumberService` (penomoran LoA)
- `LoaPdfService` (render PDF LoA)
- `LoaApprovedNotificationMail` (email notifikasi approval)

## AuthZ

- Policies:
  - `LoaRequestPolicy`
  - `LoaLetterPolicy`
  - `JournalPolicy`
- Gate:
  - `superadmin-only`
- Middleware alias custom:
  - `role` (`EnsureUserHasRole`)

## Hal Yang Harus Dipertahankan Saat Porting

- Semua status flow request/letter (`pending`, `revision`, `approved`, `published`, `rejected`, `revoked`)
- Format nomor LoA (harus konsisten dengan data lama)
- URL publik bertoken
- Tanggal publikasi, metadata penandatangan, dan path aset jurnal
- Audit log untuk aksi penting (approve/reject/update/delete)

## Risiko Tinggi Saat Migrasi

- Role/permission tidak identik (akses bocor/terblokir).
- Path file aset berubah sehingga preview PDF gagal.
- Template PDF beda tipografi/layout.
- Token URL publik tidak kompatibel dengan data lama.
- Data relasional (journal-user, notifications) tidak ikut termigrasi sempurna.

