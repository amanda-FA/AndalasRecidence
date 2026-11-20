# Frontend Lapor Kerusakan

Halaman sekarang membaca laporan, fasilitas, lokasi kamar/gedung, dan teknisi dari database melalui `LaporanKerusakanController`. Data dibatasi pada akun Client yang sedang login. Form laporan memakai POST dengan CSRF dan validasi backend; pelapor, tanggal, dan status awal ditentukan server.

Form meminta `jenis_fasilitas` (`kamar` atau `bersama`) sebelum memilih `id_fasilitas`. Jenis kamar hanya menampilkan fasilitas kamar yang sedang ditempati Client, sedangkan jenis bersama hanya menampilkan fasilitas bersama di gedung penempatan aktifnya. Daftar nama berasal dari database. Backend memeriksa kesesuaian jenis dan fasilitas saat menyimpan; jenis ditentukan dari relasi fasilitas ke kamar, tanpa menambah kolom database.

Setiap tahap pelacakan dapat diklik. Deskripsi mengikuti status serta penugasan yang tersimpan. Tanggal disposisi dan pengerjaan yang belum tersedia dalam skema ditampilkan sebagai belum tercatat; tidak dibuat tanggal atau catatan pekerjaan buatan.

Tahap 1 dan 2 tidak menampilkan foto. Tahap 3 dan 4 menampilkan foto yang benar-benar tersimpan pada laporan; jika kosong, tampil keterangan belum ada foto. Skema saat ini hanya memiliki satu kolom `foto`, sehingga halaman tidak mengklaim foto tersebut sebagai dokumentasi teknisi pada tahap tertentu. Belum ada tabel dokumentasi banyak foto per tahap.

Unggahan foto JPG/PNG maksimal 2 MB disimpan di disk `local`. Rute foto memeriksa pemilik laporan sebelum mengirim berkas. Aset ilustrasi lama di bawah dipertahankan sebagai arsip, dan tidak dipakai sebagai pengganti data foto database.

## Aset foto contoh

Dibuat menggunakan built-in ImageGen, bukan CLI/API fallback. Foto merupakan ilustrasi pratinjau, bukan dokumentasi laporan nyata. File asli tetap disimpan di folder generated_images Codex; salinan yang digunakan aplikasi berada di workspace.

### ac-kondisi-awal.png

Lokasi: `public/images/kerusakan/ac-kondisi-awal.png`

Prompt final:

```text
Use case: photorealistic-natural. Asset type: local demo maintenance documentation photo inside a university dormitory repair-report web application. Generate ONE natural 4:3 landscape documentary photo: close view of a generic white wall-mounted split air conditioner on a warm off-white wall in a modest Indonesian student dorm room. The filter cover is open, showing visibly dusty mesh filters and dusty fins, with no dramatic damage. Natural daylight, realistic everyday smartphone photography, clear useful detail, neutral colors. No people, no brand logos, no text, no labels, no watermark. This is illustrative dummy maintenance documentation, not a UI screenshot.
```

### ac-dalam-pengerjaan.png

Lokasi: `public/images/kerusakan/ac-dalam-pengerjaan.png`

Prompt final:

```text
Use case: photorealistic-natural. Asset type: local demo maintenance documentation photo inside a university dormitory repair-report web application. Generate ONE natural 4:3 landscape documentary photo of maintenance IN PROGRESS on a generic white wall-mounted split air conditioner on a warm off-white wall in a modest Indonesian student dorm room. A professional adult maintenance technician in a navy work shirt is viewed from the side/back, securely standing on a short stable step ladder, cleaning the removable filter over a protective blue air-conditioner cleaning bag. Focus on the unit, filter and gloved hands rather than the face. Natural daylight, realistic smartphone documentary photography, useful clear detail and realistic safe workmanship. No logos, no text, no labels, no watermark. This is illustrative dummy maintenance documentation, not a UI screenshot.
```

### ac-selesai-diperbaiki.png

Lokasi: `public/images/kerusakan/ac-selesai-diperbaiki.png`

Prompt final:

```text
Use case: photorealistic-natural. Asset type: local demo maintenance documentation photo inside a university dormitory repair-report web application. Generate ONE natural 4:3 landscape documentary photo of a generic white wall-mounted split air conditioner AFTER maintenance, on a warm off-white wall in a modest Indonesian student dorm room. The unit is fully assembled, clean filter and louvers visible, vents open, neat surroundings with a small corner of a student desk below. No tools or people. Natural daylight, realistic everyday smartphone photography, clear useful detail, neutral colors. No brand logos, no text, no labels, no watermark. This is illustrative dummy maintenance documentation, not a UI screenshot.
```

