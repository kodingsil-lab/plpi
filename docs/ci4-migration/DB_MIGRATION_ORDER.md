# Urutan Migrasi Database ke CI4

Gunakan urutan ini agar foreign key aman.

## A. Tabel Fondasi

1. `users`
2. `publishers`
3. `journals`
4. `journal_user` (pivot assignment)
5. `journal_profiles`

## B. Tabel Bisnis Inti

6. `loa_requests`
7. `loa_letters`
8. `loa_notifications`
9. `audit_logs`

## C. Auth / Permission

10. tabel permission/role (setara Spatie)
11. mapping role-user

## D. Data Backfill

12. backfill publisher ke journal (jika nullable di data lama)
13. backfill notification dari LoA yang sudah approved/published
14. normalisasi path aset (`logo`, `stamp`, `signature`)

## E. Validasi Pasca-Migrasi

- Cek jumlah record per tabel (Laravel vs CI4).
- Cek FK orphan:
  - `loa_requests.journal_id`
  - `loa_letters.journal_id`
  - `loa_letters.loa_request_id`
  - `loa_notifications.loa_letter_id`
- Cek random sample 20 data LoA (status, nomor, tanggal, token).

## F. Catatan Praktis

- Jalankan migrasi schema dulu, baru import data.
- Simpan skrip transformasi data terpisah (`sql/transform/*.sql` atau command CI4).
- Jangan ubah format nomor LoA saat migrasi.

