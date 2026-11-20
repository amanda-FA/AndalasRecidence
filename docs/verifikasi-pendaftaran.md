# Verifikasi pendaftaran

Admin Layanan membuka `/verifikasi-pendaftaran`. Ringkasan, filter jalur/periode/status, tanggal dan jam memakai database. Antrean dan riwayat berada di dua tabel dengan paginasi terpisah: antrean diurutkan berdasarkan tanggal pendaftaran paling lama, riwayat berdasarkan waktu verifikasi paling baru. Kolom tanggal/jam berada di kiri; ID dan berkas tidak ditampilkan pada tabel. Ikon tiga titik membuka halaman detail. Detail merupakan halaman tersendiri. Berkas disimpan di disk lokal privat dan hanya dapat dibuka Admin Layanan melalui route yang memeriksa kecocokan berkas dengan pendaftaran.

Jenis client dan aturan pemilihan kamar didefinisikan pada `JenisClient`. Form registrasi, pendaftaran hunian, dan verifikasi memakai aturan yang sama:

| Jenis client | Pemilihan gedung dan kamar |
| --- | --- |
| Maba Lokal KIP-K | Ditempatkan admin |
| Maba Lokal NON KIP-K | Dipilih client |
| Mahasiswa S1 Non Maba | Dipilih client |
| Internasional/S2/S3 pribadi | Dipilih client |
| Internasional/S2/S3 ditanggung | Ditempatkan admin |
| Non Mahasiswa (UMUM) | Dipilih client |
| Summer Course | Ditempatkan admin |

Jalankan `php artisan db:seed --class=JenisClientSeeder` untuk memperbarui master jenis client. Seeder mengganti nama Mahasiswa Reguler menjadi Maba Lokal NON KIP-K, Mahasiswa KIP-K menjadi Maba Lokal KIP-K, dan Non-Mahasiswa / Tamu menjadi Non Mahasiswa (UMUM). Kategori lama Mahasiswa Internasional dan Mahasiswa Pascasarjana dipetakan ke Internasional/S2/S3 pribadi. ID kategori dipertahankan saat mengganti nama; jika kategori tujuan sudah ada, referensi client dipindahkan sebelum kategori lama dihapus. Jenis lama yang tidak dikenali membatalkan transaksi agar tidak dipetakan sembarangan. Seeder dapat diulang tanpa mengubah pendaftaran, berkas, atau penempatan. `status_KIPK` diselaraskan dengan kategori Maba Lokal KIP-K; aturan pemilihan kamar mengikuti kategori, termasuk internasional ditanggung yang bukan penerima KIP-K.

Registrasi akun menyimpan kategori yang dipilih. Form pendaftaran memiliki unggahan terpisah untuk FC KK, FC KTP, dan hasil tes kesehatan; seluruh jenis berkas yang diwajibkan harus lengkap.

## Upgrade database yang sudah memiliki berkas pendaftaran

Migrasi jenis berkas mempertahankan ID, pendaftaran pemilik, path file, dan tanggal unggahan berkas lama. Kolom `berkas_pendaftaran.id_jenis_berkas` menerima `NULL` untuk berkas lama yang belum diketahui jenisnya. Isi dokumen tidak ditebak dari urutan unggahan atau nama file. Upload baru tetap wajib mengirim jenis berkas yang valid melalui validasi aplikasi; FK tetap menolak ID jenis yang tidak ada.

Migrasi `2026_11_20_000008_allow_unclassified_legacy_registration_documents` menyamakan instalasi yang sudah menjalankan versi awal dengan kolom wajib. Rollback migrasi penyesuaian ini mempertahankan kolom nullable agar berkas yang belum diklasifikasikan tidak dipaksa ke jenis sembarang. Rollback seluruh fitur tetap menghapus kolom relasi melalui migrasi pembuatnya.

Setelah memperbarui kode, jalankan `php artisan migrate` dan `php artisan db:seed --class=JenisBerkasSeeder`. Gunakan migrasi biasa untuk database berisi data. Admin membuka detail Verifikasi Pendaftaran, mencari bagian **Berkas Lama Belum Diklasifikasikan**, membuka masing-masing file, lalu memilih jenis KK, KTP, atau hasil tes kesehatan dan menekan **Simpan Jenis Berkas**. Jenis yang sudah ditetapkan tidak dapat ditimpa melalui endpoint ini. Path, file, dan status pendaftaran tidak diubah oleh klasifikasi. Berkas tambahan dengan jenis sama tetap memiliki tautan akses tersendiri.

Sistem menolak verifikasi selama masih ada berkas tanpa jenis atau persyaratan belum lengkap. Setelah seluruh jenis ditentukan dan file lengkap tersedia, alur verifikasi kembali berjalan seperti biasa. Penyesuaian ini tidak menghapus database atau berkas unggahan.

Empat kategori yang diizinkan memilih sendiri menentukan gedung, tipe, dan nomor kamar dari database saat mendaftar. Pilihan disimpan melalui FK `pendaftaran_hunian.id_kamar`; backend memeriksa kecocokan gedung, tipe, jenis kelamin, kondisi, dan kapasitas kamar. Tombol verifikasi mengarahkan admin ke halaman `/verifikasi-pendaftaran/{id}/jadwal` untuk mengisi check-in, check-out, jadwal pengambilan kunci, lokasi, penanggung jawab, dan kontak. Bagian paling atas menampilkan gedung dan kamar pilihan client sebagai informasi tetap. Setelah formulir disimpan, jadwal dan hasil verifikasi disimpan bersama pada pendaftaran, tanpa pembuatan penempatan oleh proses ini. Admin kembali ke daftar dengan notifikasi sukses. Gedung/kamar tidak dapat diganti lewat endpoint jadwal. Pilihan kamar adalah data pendaftaran dan tidak otomatis memulai masa hunian. Referensi kamar lama hanya dipulihkan dari penempatan yang benar-benar tercatat. Maba Lokal KIP-K, Internasional/S2/S3 ditanggung, dan Summer Course diarahkan ke halaman pemilihan gedung/kamar/bed oleh admin. Admin mengisi periode hunian, jadwal dan lokasi pengambilan kunci, penanggung jawab, serta kontaknya. Gedung disaring berdasarkan jenis kelamin client. Bed dihitung dari kapasitas tipe kamar dan penempatan yang beririsan dengan rentang tanggal pilihan, termasuk reservasi yang belum dimulai. Kamar dengan penghuni lama yang belum memiliki nomor bed ditandai Perlu Pendataan dan tidak dapat dipilih untuk alokasi bed baru sampai posisi bed lama tercatat.

Verifikasi dan penempatan dijalankan dalam satu transaksi dengan penguncian client, pendaftaran, serta kamar. Sistem memeriksa ulang status, berkas, peruntukan, kondisi kamar, kapasitas/bed, dan penempatan lain milik client. Pengajuan yang sudah diproses tidak dapat diproses ulang. Admin juga dapat menyimpan status perlu perbaikan atau ditolak beserta catatan wajib. Rentang tanggal hunian dihitung inklusif; bed yang berakhir pada suatu tanggal dapat digunakan lagi mulai hari berikutnya.

`tanggal_daftar` dan `tanggal_verifikasi` kini bertipe datetime. Waktu aplikasi menggunakan Asia/Jakarta (WIB). Data lama yang semula hanya bertipe date tidak mempunyai informasi jam asli. `penempatan_kamar.rencana_keluar` adalah akhir hunian yang direncanakan; `tanggal_keluar` tetap dipakai untuk tanggal keluar aktual.

## Data uji

Jalankan migrasi, kemudian `php artisan db:seed --class=VerifikasiPendaftaranDemoSeeder`. Seeder menghasilkan tujuh pendaftaran (satu per kategori) dengan status awal menunggu verifikasi dalam Periode Uji Verifikasi 2026/2027. Terdapat 21 lampiran berupa salinan aset PNG dari `public/images` untuk pengujian, bukan dokumen identitas asli.

| Kategori | Email client uji |
| --- | --- |
| Maba Lokal KIP-K | uji.verifikasi.kipk@andalas.test |
| Maba Lokal NON KIP-K | uji.verifikasi.reguler@andalas.test |
| Mahasiswa S1 Non Maba | uji.verifikasi.s1_non_maba@andalas.test |
| Internasional/S2/S3 pribadi | uji.verifikasi.international_private@andalas.test |
| Internasional/S2/S3 ditanggung | uji.verifikasi.international_funded@andalas.test |
| Non Mahasiswa (UMUM) | uji.verifikasi.guest@andalas.test |
| Summer Course | uji.verifikasi.summer_course@andalas.test |

Password akun uji: `password`. Seeder dapat dijalankan lagi tanpa menduplikasi pendaftaran dan tidak mengembalikan hasil verifikasi yang sudah diproses menjadi menunggu. Seeder ini terpisah dari seeder utama agar data demo tidak otomatis dibuat pada instalasi produksi.

PHP lokal `C:/php84/php.ini` memakai batas file 10 MB dan batas request 64 MB untuk mendukung unggahan beberapa berkas. Mesin lain perlu menggunakan batas yang memadai; file konfigurasi PHP lokal ini tidak ikut Git.

## Data contoh tambahan

`php artisan db:seed --class=PendaftaranContohSeeder` menambahkan delapan pendaftaran menunggu pemeriksaan (dua per kategori contoh awal: maba NON KIP-K, KIP-K, Summer Course, dan umum), dengan nama seperti Nadia Putri Azzahra, Rizky Aditya Pratama, Salsabila Rahma Putri, Farhan Akbar Saputra, Alya Nur Safitri, Dimas Ardiansyah, Ratna Wulandari, dan Budi Santoso. Data ini bersifat sintetis meskipun namanya ditampilkan secara wajar. Email menggunakan domain `example.test` dan password `password`. Terdapat 24 lampiran berupa salinan gambar proyek. Pilihan kamar untuk maba NON KIP-K dan umum menggunakan kamar sebenarnya dari database.

Seeder juga merapikan nama empat akun demonstrasi lama yang masih memiliki awalan Uji Verifikasi menjadi nama wajar, mempertahankan email login dan hasil pemeriksaannya, serta melengkapi pilihan kamar pada pendaftaran contoh maba NON KIP-K/umum yang belum tercatat. Akun client lain tidak diubah. Seeder dapat diulang tanpa menduplikasi pendaftaran atau mengembalikan status yang telah diproses.
