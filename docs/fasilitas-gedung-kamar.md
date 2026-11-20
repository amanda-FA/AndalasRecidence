# Fasilitas pada form gedung dan kamar

Menu, controller, route resource, view CRUD, dan JavaScript Manajemen Fasilitas terpisah dihapus. Fasilitator mengatur fasilitas dari form tambah/edit gedung dan kamar. Keduanya memakai `partials/facility-selector.blade.php` serta `pages/facility-selector.js`.

Daftar awal berisi 7 fasilitas gedung dan 8 fasilitas kamar sesuai form sebelumnya, dengan urutan yang sama. Pilihan gedung dan kamar dipisahkan melalui kolom `lingkup`. Modal mengirim lingkup form asal secara otomatis; nama tambahan hanya muncul pada daftar tersebut. Nama sama dapat tersedia pada kedua lingkup dengan ID pilihan masing-masing. Server menolak pilihan kamar yang dikirim untuk gedung, dan sebaliknya. Daftar inventaris lama tidak lagi otomatis ditambahkan ke pilihan kedua form.

Tombol Tambah Fasilitas membuka modal nama. `POST /pilihan-fasilitas` (`pilihan-fasilitas.store`) memvalidasi nama maksimal 100 karakter, merapikan spasi, menyimpan pilihan di tabel `fasilitas`, dan mengembalikan ID/nama sebagai JSON. Nama yang sudah tersedia dipakai kembali. Kotak baru langsung dicentang, tanpa mengirim ulang form utama. Tombol Pilih semua mencentang atau membatalkan semua kotak; status sebagian terpilih ditampilkan sebagai indeterminate. Hanya Fasilitator yang boleh menambah pilihan atau menyimpan form gedung/kamar; CSRF tetap diperiksa.

`fasilitas` tetap menyimpan inventaris per lokasi agar FK laporan kerusakan dan pengecekan tidak berubah:

- Baris pilihan nama memiliki `id_gedung`, `id_kamar`, dan `id_fasilitas_acuan` kosong, serta `lingkup` gedung/kamar. Pilihan lama tanpa lingkup tidak ditampilkan; jika nama tersebut ditambahkan melalui modal, pilihan lama dapat dipakai kembali untuk lingkup form asal.
- Checkbox mengirim ID baris pilihan melalui `fasilitas[]`. Server menolak ID inventaris lokasi sebagai pilihan.
- Inventaris gedung memiliki `id_gedung`, `id_kamar = NULL`, dan FK `id_fasilitas_acuan` menunjuk pilihan yang dicentang.
- Inventaris kamar memiliki FK `id_kamar`, `id_gedung` yang sesuai, dan FK `id_fasilitas_acuan` menunjuk pilihan yang dicentang. Satu kamar dapat memiliki banyak fasilitas.
- Relasi asli berjalan dari `fasilitas.id_kamar` ke `kamar.id_kamar`, bukan satu kolom fasilitas pada kamar. Ini mendukung beberapa pilihan dan identitas inventaris terpisah di setiap kamar.

`FacilitySelectionService` menyimpan pilihan bersama gedung/kamar dalam transaksi. Inventaris baru berkondisi Baik. Inventaris yang tidak dipilih lagi ditandai `aktif = false`; laporan dan kondisi lamanya tetap tersimpan. Ketika dipilih kembali, ID dan kondisi inventaris lama dipakai kembali. Fasilitas tidak aktif hilang dari daftar fasilitas lokasi dan pilihan laporan baru, sedangkan riwayat laporan masih dapat dibuka. Perubahan fasilitas bersama tidak mengubah fasilitas kamar di gedung tersebut.

Hapus gedung/kamar sekaligus membersihkan inventaris yang belum dipakai. Penghapusan ditolak dengan pesan jika lokasi/inventaris masih digunakan pada penempatan, laporan, atau pengecekan. Pilihan nama yang dapat dipakai kembali tetap tersedia.

Jalankan `php artisan migrate` lalu `php artisan db:seed --class=PilihanFasilitasSeeder`. Seeder menyiapkan 15 pilihan awal sesuai lingkup form dan melengkapi FK acuan inventaris yang namanya cocok dengan pilihan awal pada lingkupnya. Inventaris lama di luar daftar tetap tersimpan dan tidak diubah hanya karena tidak tampil pada form. ID, lokasi, kondisi, dan referensi laporan lama dipertahankan. Pilihan tambahan yang sudah memiliki lingkup tetap tersedia. Seeder disertakan setelah `DataAsramaSeeder` pada `DatabaseSeeder` dan dapat diulang.
