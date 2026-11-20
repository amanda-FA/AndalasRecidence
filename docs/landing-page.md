# Landing page publik Andalas Residence

`GET /` menampilkan landing page untuk pengunjung maupun pengguna yang sudah login. Tombol Masuk mengarah ke halaman `/login` yang sudah ada, tombol Daftar ke `/register`. Buka Portal mengarah ke `/portal`, yang memakai pemilihan tujuan berdasarkan role pada AuthController. Pengguna yang sudah login dan membuka `/login` atau `/register` diarahkan ke portal sesuai role.

Katalog publik tersedia di `/hunian`, dengan pencarian nama gedung dan filter peruntukan. Detail publik tersedia di `/hunian/{id_gedung}`, termasuk fasilitas, tipe, tarif tercatat, dan daftar kamar dengan paginasi. Tidak ada nama, kontak, ID pengguna, atau dokumen penghuni dalam data publik.

Sumber informasi: tabel gedung, kamar, tipe_kamar, fasilitas, periode_pendaftaran, jenis_client, serta hitungan penempatan aktif per kamar. Tempat tersedia dihitung dari kapasitas dikurangi penempatan berstatus aktif, tanggal masuk sudah tercapai, dan tanggal keluar kosong. Kamar dengan status selain tersedia tidak menawarkan tempat tersedia. Perhitungan ini merupakan informasi saat halaman dimuat, bukan reservasi.

Periode berstatus dibuka dan berada dalam rentang tanggal buka/tutup ditampilkan Dibuka. Tanggal mendatang ditampilkan Akan Datang; periode yang ditutup atau sudah melewati batas waktu ditampilkan Ditutup. Tanggal memakai Asia/Jakarta dan kedua hari batas dihitung inklusif. Kategori Client ditampilkan sebagai informasi umum karena belum ada relasi kategori khusus per periode. Tidak dibuat periode atau statistik fiktif.

Hero dan Tentang Kami memakai aset proyek `public/images/asramaunand.png`. Kartu dan detail gedung menampilkan foto yang diunggah melalui form tambah/edit Manajemen Gedung. Gedung tanpa foto tetap memakai ilustrasi SVG dekoratif. Nama, fasilitas, tarif, dan ketersediaan berasal dari database. Informasi checkout/surat diberi penanda Dalam pengembangan sesuai kondisi route yang tersedia.

Foto gedung bersifat opsional, berformat JPG/PNG/WebP maksimal 2 MB. Kolom `gedung.foto` menyimpan path relatif pada disk `public` (`storage/app/public/gedung`); jalankan `php artisan storage:link` sekali pada instalasi baru agar foto dapat diakses melalui `/storage`. Saat edit tanpa unggahan baru, foto lama dipertahankan. Saat penggantian berhasil, file lama dihapus; saat penyimpanan database gagal, unggahan baru dibersihkan dan foto lama tetap tersedia. Menghapus gedung juga membersihkan file fotonya setelah penghapusan database berhasil.

Layout publik berada di `resources/views/layouts/landing.blade.php`, dengan tampilan di `resources/views/landing/`, stylesheet terpusat pada `resources/css/app.css` di bagian `Landing page publik`, dan interaksi header/menu di `resources/js/pages/landing-page.js`. Tidak ada migrasi atau perubahan isi database untuk landing page.
