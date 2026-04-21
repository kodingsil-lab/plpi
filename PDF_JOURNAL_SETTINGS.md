# Pengaturan PDF LoA per Jurnal

Panduan ini dipakai untuk mengatur posisi tampilan cap dan tanda tangan pada PDF LoA dari menu `Profil Jurnal`.

## Lokasi Pengaturan

Masuk ke:

`Admin > Pengaturan > Data Jurnal > Edit Jurnal`

Lalu cari bagian:

`Pengaturan PDF`

Field yang tersedia:

- `Posisi TTD Kiri/Kanan (px)`
- `Posisi TTD Atas/Bawah (px)`
- `Tinggi TTD (px)`

## Fungsi Masing-Masing Field

### `Posisi TTD Kiri/Kanan (px)`

Mengatur blok cap dan tanda tangan bergerak ke kiri atau ke kanan.

- Angka lebih kecil: lebih ke kiri
- Angka lebih besar: lebih ke kanan

### `Posisi TTD Atas/Bawah (px)`

Mengatur jarak vertikal sebelum area cap dan tanda tangan.

- Angka lebih kecil: naik ke atas
- Angka lebih besar: turun ke bawah

### `Tinggi TTD (px)`

Mengatur tinggi tampilan tanda tangan utama.

- Angka lebih kecil: tanda tangan lebih kecil
- Angka lebih besar: tanda tangan lebih besar

Catatan:

- Cap akan ikut menyesuaikan secara proporsional
- Pengaturan ini berlaku per jurnal, jadi setiap jurnal bisa punya posisi berbeda

## Angka Awal Rekomendasi

Untuk mulai yang aman, gunakan:

- `kiri/kanan: 20`
- `atas/bawah: 10`
- `tinggi: 85`

## Pola Penyesuaian Cepat

Kalau tampilan belum pas, gunakan panduan ini:

- Jika cap + tanda tangan terlalu ke kanan, kecilkan `Posisi TTD Kiri/Kanan`
- Jika cap + tanda tangan terlalu ke kiri, besarkan `Posisi TTD Kiri/Kanan`
- Jika cap + tanda tangan terlalu turun, kecilkan `Posisi TTD Atas/Bawah`
- Jika cap + tanda tangan terlalu naik, besarkan `Posisi TTD Atas/Bawah`
- Jika tanda tangan terlalu besar, kecilkan `Tinggi TTD`
- Jika tanda tangan terlalu kecil, besarkan `Tinggi TTD`

## Contoh Penyesuaian

### Contoh 1

Jika tanda tangan terlalu ke kanan dan terlalu turun:

- `kiri/kanan: 14`
- `atas/bawah: 6`
- `tinggi: 85`

### Contoh 2

Jika tanda tangan sudah pas posisinya tetapi terlalu kecil:

- `kiri/kanan: 20`
- `atas/bawah: 10`
- `tinggi: 95`

## Saran Praktis

- Ubah satu angka dulu, lalu preview PDF
- Jangan ubah semua field sekaligus kalau ingin cepat menemukan posisi yang pas
- Simpan angka yang sudah cocok untuk masing-masing jurnal

## Ringkasannya

Nilai awal yang disarankan:

```text
Posisi TTD Kiri/Kanan (px): 20
Posisi TTD Atas/Bawah (px): 10
Tinggi TTD (px): 85
```
