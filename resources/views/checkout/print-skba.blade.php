<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Bebas Asrama - {{ $penghuni['nama'] }} ({{ $penghuni['nim'] }})</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: #111827;
            background-color: #f3f4f6;
            padding: 30px 15px;
        }
        .page {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 50px 60px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border-radius: 8px;
        }
        .kop {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 3px double #111827;
            padding-bottom: 18px;
            margin-bottom: 24px;
            text-align: center;
        }
        .kop img {
            width: 76px;
            height: 76px;
            object-fit: contain;
        }
        .kop-text {
            flex: 1;
        }
        .kop-text h4 {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h2 {
            font-size: 19px;
            font-weight: 900;
            text-transform: uppercase;
            color: #000a39;
        }
        .kop-text h3 {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            color: #4750c7;
        }
        .kop-text p {
            font-size: 11px;
            color: #4b5563;
            margin-top: 3px;
        }
        .judul-surat {
            text-align: center;
            margin-bottom: 24px;
        }
        .judul-surat h1 {
            font-size: 17px;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            color: #000a39;
        }
        .judul-surat p {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-top: 4px;
        }
        .content {
            font-size: 14px;
            line-height: 1.8;
            color: #1f2937;
        }
        .biodata-table {
            width: 100%;
            margin: 16px 0;
            border-collapse: collapse;
        }
        .biodata-table td {
            padding: 4px 6px;
            font-size: 14px;
            vertical-align: top;
        }
        .biodata-table td:first-child {
            width: 170px;
            color: #4b5563;
        }
        .biodata-table td:nth-child(2) {
            width: 15px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .qr-box {
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: center;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fafafa;
        }
        .qr-box span.material-symbols-outlined {
            font-size: 64px;
            color: #1f2937;
        }
        .qr-box p {
            font-size: 9px;
            color: #6b7280;
            font-family: monospace;
        }
        .sign-box {
            text-align: right;
        }
        .sign-box p.date {
            font-size: 13px;
            color: #374151;
        }
        .sign-box p.title {
            font-size: 13px;
            font-weight: 700;
            color: #000a39;
            margin-top: 2px;
        }
        .seal-badge {
            display: inline-block;
            margin: 10px 0;
            padding: 6px 12px;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            color: #3730a3;
            font-size: 11px;
            font-weight: 700;
            text-align: right;
        }
        .seal-badge small {
            display: block;
            font-size: 9px;
            font-weight: 400;
            font-family: monospace;
            color: #6b7280;
        }
        .sign-box p.name {
            font-size: 14px;
            font-weight: 700;
            text-decoration: underline;
            color: #111827;
        }
        .sign-box p.nip {
            font-size: 12px;
            color: #4b5563;
        }
        .no-print-bar {
            max-width: 800px;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }
        .btn-primary {
            background: #4750c7;
            color: #fff;
        }
        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .page {
                box-shadow: none;
                padding: 20px 40px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <strong>Surat Keterangan Bebas Asrama (SKBA)</strong>
            <span style="display:block; font-size:12px; color:#6b7280;">No: {{ $suratBebas['nomor_surat'] }}</span>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('checkout.index') }}" class="btn btn-secondary">
                <span class="material-symbols-outlined" style="font-size:18px;">arrow_back</span>
                Kembali ke Portal
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <span class="material-symbols-outlined" style="font-size:18px;">print</span>
                Cetak Dokumen
            </button>
        </div>
    </div>

    <div class="page">
        {{-- KOP SURAT --}}
        <div class="kop">
            <img src="{{ asset('images/logo_andalasresidence.png') }}" alt="Logo UNAND">
            <div class="kop-text">
                <h4>Kementerian Pendidikan Tinggi, Sains, dan Teknologi</h4>
                <h2>Universitas Andalas</h2>
                <h3>Unit Pelaksana Teknis (UPT) Asrama Mahasiswa</h3>
                <p>Gedung Pusat Kegiatan Mahasiswa Lt. 2, Kampus Limau Manis, Padang 25163 | Telp: (0751) 71181 | Web: asrama.unand.ac.id</p>
            </div>
        </div>

        {{-- JUDUL SURAT --}}
        <div class="judul-surat">
            <h1>SURAT KETERANGAN BEBAS ASRAMA (SKBA)</h1>
            <p>Nomor: {{ $suratBebas['nomor_surat'] }}</p>
        </div>

        {{-- ISI SURAT --}}
        <div class="content">
            <p>Kepala Unit Pelaksana Teknis (UPT) Asrama Mahasiswa Universitas Andalas menerangkan bahwa:</p>

            <table class="biodata-table">
                <tr>
                    <td>Nama Mahasiswa</td>
                    <td>:</td>
                    <td><strong>{{ $penghuni['nama'] }}</strong></td>
                </tr>
                <tr>
                    <td>Nomor Induk Mahasiswa (NIM)</td>
                    <td>:</td>
                    <td><strong>{{ $penghuni['nim'] }}</strong></td>
                </tr>
                <tr>
                    <td>Fakultas / Program Studi</td>
                    <td>:</td>
                    <td>{{ $penghuni['fakultas'] }} / {{ $penghuni['prodi'] }}</td>
                </tr>
                <tr>
                    <td>Alokasi Hunian Asrama</td>
                    <td>:</td>
                    <td>{{ $hunian['gedung'] }}, Kamar {{ $hunian['kamar'] }}</td>
                </tr>
                <tr>
                    <td>Periode Tinggal</td>
                    <td>:</td>
                    <td>{{ $hunian['periode'] }}</td>
                </tr>
            </table>

            <p style="text-align: justify; margin-bottom: 12px;">
                Telah menyelesaikan seluruh masa tinggal di Asrama Mahasiswa Universitas Andalas, menyerahkan seluruh fasilitas inventaris ruangan dalam keadaan <strong>Lengkap &amp; Bersih</strong>, melunasi seluruh kewajiban finansial (Bebas Tunggakan Iuran), serta telah menyerahkan kembali kunci kamar dan kartu akses RFID kepada petugas General Officer (GO).
            </p>

            <p style="text-align: justify; margin-bottom: 12px;">
                Surat keterangan ini diterbitkan secara otomatis dan terotentikasi secara digital oleh sistem informasi asrama terintegrasi, serta tersinkronisasi langsung dengan <strong>Portal Akademik Universitas Andalas (SIA / Syarat KRS &amp; Bebas Asrama untuk Wisuda)</strong>.
            </p>

            <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        {{-- TANDA TANGAN & SEAL --}}
        <div class="signature-section">
            <div class="qr-box">
                <span class="material-symbols-outlined">qr_code_2</span>
                <p>DIGITALLY VERIFIED</p>
                <p>{{ $suratBebas['verification_code'] }}</p>
            </div>

            <div class="sign-box">
                <p class="date">Padang, {{ $suratBebas['tanggal_terbit'] }}</p>
                <p class="title">{{ $suratBebas['pejabat_jabatan'] }},</p>
                <div class="seal-badge">
                    TANDATANGAN ELEKTRONIK TERSERTIFIKASI
                    <small>UPT ASRAMA UNIVERSITAS ANDALAS</small>
                </div>
                <p class="name">{{ $suratBebas['pejabat_nama'] }}</p>
                <p class="nip">NIP. {{ $suratBebas['pejabat_nip'] }}</p>
            </div>
        </div>
    </div>

</body>
</html>
