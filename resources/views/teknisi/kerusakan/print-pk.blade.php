<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tugas Perintah Kerja (PK) - {{ $report['nomor_tiket'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #111827;
        }
        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    {{-- Action Bar (Non-Printable) --}}
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-indigo-600 text-[24px]">print</span>
            <div>
                <h1 class="text-sm font-bold text-gray-900">Pratinjau Surat Tugas PK (Perintah Kerja)</h1>
                <p class="text-xs text-gray-500">Gunakan tombol cetak untuk menyimpan sebagai PDF atau mencetak langsung.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="window.close()" class="px-4 py-2 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 cursor-pointer">
                Tutup
            </button>
            <button type="button" onclick="window.print()" class="px-4 py-2 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white flex items-center gap-1.5 cursor-pointer shadow-sm">
                <span class="material-symbols-outlined text-[16px]">print</span>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- Official Printable Document Sheet --}}
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-xl shadow-md border border-gray-200 text-gray-900 print:shadow-none print:border-none print:p-0">
        
        {{-- Header / Kop Surat Resmi --}}
        <div class="flex items-center justify-between border-b-2 border-gray-900 pb-4 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo_andalasresidence.png') }}" alt="Logo Universitas Andalas" class="w-16 h-16 object-contain">
                <div>
                    <h2 class="text-base font-bold uppercase tracking-wider text-gray-900">Universitas Andalas</h2>
                    <h3 class="text-lg font-extrabold uppercase text-indigo-950">Unit Pelaksana Teknis Asrama Mahasiswa (Andalas Residence)</h3>
                    <p class="text-xs text-gray-600">Gedung Pengelola Asrama, Kampus Limau Manis, Padang 25163 | Telp: (0751) 71181</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Dokumen Operasional</div>
                <div class="text-xs font-mono font-bold text-indigo-900 mt-1">{{ $nomor_spk }}</div>
            </div>
        </div>

        {{-- Judul Surat Tugas --}}
        <div class="text-center my-6">
            <h1 class="text-lg font-bold uppercase tracking-wide underline decoration-1 underline-offset-4">Surat Perintah Kerja (SPK) Pemeliharaan Fasilitas</h1>
            <p class="text-xs text-gray-500 mt-1">Nomor Registrasi Tiket: <span class="font-mono font-semibold">{{ $report['nomor_tiket'] }}</span></p>
        </div>

        {{-- Tabel Data Laporan & Penugasan --}}
        <div class="grid grid-cols-2 gap-4 mb-6 text-xs">
            <div class="border border-gray-300 rounded-lg p-3.5 bg-gray-50">
                <h4 class="font-bold text-gray-900 uppercase tracking-wider mb-2 border-b border-gray-200 pb-1">I. Data Pelapor &amp; Lokasi</h4>
                <table class="w-full">
                    <tr class="py-1">
                        <td class="text-gray-500 w-32 py-0.5">Nama Pelapor</td>
                        <td class="font-semibold text-gray-900">: {{ $report['pelapor_nama'] }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-gray-500 py-0.5">NIM / Identitas</td>
                        <td>: {{ $report['pelapor_identitas'] }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-gray-500 py-0.5">Kontak / HP</td>
                        <td>: {{ $report['pelapor_telepon'] }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-gray-500 py-0.5">Lokasi Gedung</td>
                        <td class="font-semibold">: {{ $report['gedung_nama'] }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-gray-500 py-0.5">Kamar / Fasilitas</td>
                        <td>: {{ $report['kamar_nomor'] }} (Lantai {{ $report['lantai'] }})</td>
                    </tr>
                </table>
            </div>

            <div class="border border-gray-300 rounded-lg p-3.5 bg-gray-50">
                <h4 class="font-bold text-gray-900 uppercase tracking-wider mb-2 border-b border-gray-200 pb-1">II. Rincian Penugasan Teknis</h4>
                <table class="w-full">
                    <tr class="py-1">
                        <td class="text-gray-500 w-32 py-0.5">Teknisi Pelaksana</td>
                        <td class="font-semibold text-indigo-950">: {{ $report['teknisi_nama'] }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-gray-500 py-0.5">Fasilitator Gedung</td>
                        <td>: {{ $report['fasilitator_nama'] }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-gray-500 py-0.5">Tingkat Urgensi</td>
                        <td class="font-bold text-red-700">: {{ $report['urgensi'] }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-gray-500 py-0.5">Tanggal Lapor</td>
                        <td>: {{ $report['tanggal_lapor'] }} ({{ $report['waktu_lapor'] }})</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-gray-500 py-0.5">Batas SLA Target</td>
                        <td class="font-semibold text-indigo-800">: {{ $report['sla_target'] }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Uraian Kerusakan --}}
        <div class="border border-gray-300 rounded-lg p-4 mb-6 text-xs">
            <h4 class="font-bold text-gray-900 uppercase tracking-wider mb-2 border-b border-gray-200 pb-1">III. Uraian Kendala Fasilitas</h4>
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <span class="text-gray-500 block">Fasilitas Utama:</span>
                    <span class="font-bold text-gray-900">{{ $report['fasilitas_nama'] }} ({{ $report['kategori_fasilitas'] }})</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Subkomponen Terindikasi:</span>
                    <span class="font-medium text-gray-800">{{ $report['subkomponen'] }}</span>
                </div>
            </div>
            <div>
                <span class="text-gray-500 block">Deskripsi Kerusakan dari Pelapor:</span>
                <p class="mt-1 p-2 bg-gray-50 rounded border border-gray-200 italic text-gray-800 leading-relaxed">
                    "{{ $report['deskripsi'] }}"
                </p>
            </div>
        </div>

        {{-- Rencana Suku Cadang & Catatan Pekerjaan --}}
        <div class="border border-gray-300 rounded-lg p-4 mb-8 text-xs">
            <h4 class="font-bold text-gray-900 uppercase tracking-wider mb-2 border-b border-gray-200 pb-1">IV. Suku Cadang &amp; Catatan Tindak Lanjut</h4>
            <table class="w-full text-left mb-3 border border-gray-200">
                <thead class="bg-gray-100 font-semibold">
                    <tr>
                        <th class="p-2 border-b border-gray-200 w-12 text-center">No</th>
                        <th class="p-2 border-b border-gray-200">Komponen / Material Dibutuhkan</th>
                        <th class="p-2 border-b border-gray-200 w-24">Jumlah</th>
                        <th class="p-2 border-b border-gray-200 w-32">Status Logistik</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($report['suku_cadang'] as $idx => $part)
                        <tr class="border-b border-gray-100">
                            <td class="p-2 text-center">{{ $idx + 1 }}</td>
                            <td class="p-2 font-medium">{{ $part['nama'] }}</td>
                            <td class="p-2">{{ $part['qty'] }}</td>
                            <td class="p-2">{{ $part['status'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                <span class="text-gray-500 block">Instruksi / Catatan Awal Teknisi:</span>
                <p class="mt-1 text-gray-800">{{ $report['catatan_teknisi'] }}</p>
            </div>
        </div>

        {{-- Tanda Tangan & Pengesahan --}}
        <div class="grid grid-cols-3 gap-6 text-center text-xs mt-10 pt-4 border-t border-gray-300">
            <div>
                <p class="text-gray-600">Mahasiswa / Pelapor,</p>
                <div class="h-20 flex items-end justify-center">
                    <p class="font-bold text-gray-900 border-b border-gray-900 pb-0.5 inline-block min-w-[120px]">{{ $report['pelapor_nama'] }}</p>
                </div>
                <p class="text-[10px] text-gray-500 mt-1">{{ $report['pelapor_identitas'] }}</p>
            </div>

            <div>
                <p class="text-gray-600">Teknisi Pelaksana,</p>
                <div class="h-20 flex items-end justify-center">
                    <p class="font-bold text-gray-900 border-b border-gray-900 pb-0.5 inline-block min-w-[120px]">{{ $report['teknisi_nama'] }}</p>
                </div>
                <p class="text-[10px] text-gray-500 mt-1">ID Teknisi: TKN-04</p>
            </div>

            <div>
                <p class="text-gray-600">Mengetahui, Fasilitator Asrama,</p>
                <div class="h-20 flex items-end justify-center">
                    <p class="font-bold text-gray-900 border-b border-gray-900 pb-0.5 inline-block min-w-[120px]">Rahadian Triputra</p>
                </div>
                <p class="text-[10px] text-gray-500 mt-1">Pengelola Gedung</p>
            </div>
        </div>

        <div class="mt-8 text-[10px] text-gray-400 text-center">
            Dicetak secara otomatis melalui Sistem Informasi Andalas Residence &middot; Tanggal Cetak: {{ date('d/m/Y H:i') }} WIB
        </div>

    </div>

</body>
</html>
