# Data uji backend Fadhil

Data sudah dimasukkan ke database lokal `andalas_residence` pada 19 November 2026 melalui `database/seeders/FadhilBackendDemoSeeder.php`. Seeder ini terpisah dari `DatabaseSeeder`, sehingga tidak otomatis dijalankan saat orang lain menjalankan seeder utama proyek.

## Penempatan kamar

| Kolom / informasi | Nilai |
| --- | --- |
| Penghuni | Muhammad Fadhil (`client@unand.ac.id`) |
| `pengguna.id_pengguna` | 6 |
| `client.id_client` | 1 |
| `pendaftaran_hunian.id_pendaftaran` | 1 |
| Status pendaftaran | `terverifikasi` |
| Periode | Pendaftaran hunian, ID 1 (periode yang sudah ada) |
| `penempatan_kamar.id_penempatan` | 1 |
| `kamar.id_kamar` | 281 |
| Nomor kamar | 1A.01 |
| Gedung | Gedung Asrama Menpera (D), ID 4 |
| Lantai | 1 |
| Tipe / kapasitas | Standar 4 Orang / 4 orang |
| `tanggal_masuk` | 2026-11-19 |
| `tanggal_keluar` | `NULL` |
| `status_penempatan` | `aktif` |
| Admin | Admin Layanan Asrama, `id_pengguna = 2` |

Hanya ada satu penempatan aktif untuk Fadhil. Nomor kamar 1A.01 juga ada di gedung lain, jadi pencarian harus memakai gedung atau `id_kamar`, bukan nomor kamar saja.

## Laporan kerusakan

Empat laporan memakai pelapor `id_pengguna = 6`, fasilitas kamar `id_kamar = 281`, dan tanggal lapor 2026-11-19.

| `id_laporan` | `id_fasilitas` | Fasilitas | `status_perbaikan` | `tanggal_selesai` | Penugasan teknisi |
| --- | --- | --- | --- | --- | --- |
| 1 | 44 | Lampu utama kamar | `diajukan` | `NULL` | Belum ada |
| 2 | 45 | Kunci pintu kamar | `ditugaskan` | `NULL` | Teknisi Pemeliharaan, ID 4 |
| 3 | 46 | Lemari pakaian | `dalam_pengerjaan` | `NULL` | Teknisi Pemeliharaan, ID 4 |
| 4 | 47 | Meja belajar | `selesai` | 2026-11-19 | Teknisi Pemeliharaan, ID 4 |

Kolom `foto` sengaja `NULL`; data ini belum berisi unggahan foto laporan. Ketiga penugasan berada di tabel `penugasan_perbaikan`. Kolom `id_pengguna` pada tabel penugasan berisi ID teknisi, sedangkan `laporan_kerusakan.id_pengguna` berisi ID pelapor.

Skema sekarang hanya menyediakan satu kolom `foto` pada laporan, belum ada tabel banyak foto per tahap atau kolom tanggal disposisi, tanggal pengerjaan, dan catatan per tahap. Galeri dan rincian alur kerja pada frontend sebelumnya masih berupa data presentasi.

## Menjalankan seeder ini

Di terminal PowerShell, dari folder proyek:

```powershell
& 'C:\php84\php.exe' artisan db:seed --class=FadhilBackendDemoSeeder
```

Gunakan PHP tersebut karena PHP bawaan XAMPP saat ini belum memenuhi versi minimum proyek. Seeder membutuhkan akun Client Fadhil, Admin Layanan, Teknisi, kamar 1A.01 Menpera (D), serta periode pendaftaran yang sedang dibuka. Detail tambahan diambil dari data yang sudah ada.

Menjalankan ulang pada data demo yang sama tidak menambah duplikat atau mengatur ulang status laporan. Seeder memakai transaksi dan berhenti jika Fadhil sudah memiliki penempatan aktif di kamar lain atau kamar tujuan tidak tersedia. Seeder hanya berjalan pada environment `local` atau `testing`.

## Relasi untuk membuat backend

Penempatan: `pengguna → client → pendaftaran_hunian → penempatan_kamar → kamar → gedung / tipe_kamar`.

Laporan: `pengguna → laporan_kerusakan → fasilitas → kamar / gedung`.

Teknisi: `laporan_kerusakan → penugasan_perbaikan → pengguna`.

Contoh query untuk memeriksa penempatan di tab SQL phpMyAdmin:

```sql
SELECT p.nama_lengkap, pk.*, k.nomor_kamar, g.nama_gedung
FROM penempatan_kamar AS pk
JOIN pendaftaran_hunian AS ph ON ph.id_pendaftaran = pk.id_pendaftaran
JOIN client AS c ON c.id_client = ph.id_client
JOIN pengguna AS p ON p.id_pengguna = c.id_pengguna
JOIN kamar AS k ON k.id_kamar = pk.id_kamar
JOIN gedung AS g ON g.id_gedung = k.id_gedung
WHERE p.email = 'client@unand.ac.id';
```

Pada backend halaman Client, batasi query menggunakan ID pengguna yang sedang login. Saat memindahkan kamar, akhiri penempatan lama dengan `tanggal_keluar` dan status nonaktif terlebih dahulu dalam transaksi yang sama dengan pembuatan penempatan baru, agar tidak ada dua penempatan aktif.

Halaman Penempatan Kamar dan Lapor Kerusakan sekarang membaca database melalui `PenempatanController` dan `LaporanKerusakanController`. Data dibatasi ke akun Client yang sedang login. Data contoh dalam dokumen ini merupakan baris database yang dibuat oleh seeder, bukan array dummy pada halaman.

Form laporan baru memakai `POST /lapor-kerusakan`, menyimpan pelapor dari sesi login, status awal `diajukan`, dan tanggal laporan saat ini. Fasilitas dibatasi pada kamar aktif dan fasilitas bersama gedung yang sama. Unggahan JPG/PNG maksimal 2 MB disimpan pada disk `local` dan dibaca melalui rute foto yang memeriksa kepemilikan laporan. Memuat ulang halaman tetap menampilkan laporan yang sudah tersimpan.
