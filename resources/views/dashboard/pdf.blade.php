<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Okupansi - Andalas Residence</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 16mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #151a32;
            margin: 0;
            padding: 24px;
            background: #f3f2ff;
            font-size: 12px;
            line-height: 1.5;
        }
        .sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 28px 32px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(18, 32, 86, 0.08);
        }
        .toolbar {
            max-width: 820px;
            margin: 0 auto 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: #4750c7;
            color: #ffffff;
        }
        .btn-secondary {
            background: #ffffff;
            color: #151a32;
            border: 1px solid #c6c5d1;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000a39;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .header h1 {
            margin: 0 0 4px;
            font-size: 18px;
            color: #000a39;
        }
        .header p {
            margin: 0;
            color: #45464f;
            font-size: 11px;
        }
        .meta {
            text-align: right;
            font-size: 11px;
            color: #45464f;
        }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .kpi-card {
            border: 1px solid #dde1ff;
            border-radius: 8px;
            padding: 10px 12px;
            background: #fbf8ff;
        }
        .kpi-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #45464f;
            letter-spacing: 0.4px;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: 700;
            color: #000a39;
            margin: 2px 0;
        }
        .kpi-sub {
            font-size: 10px;
            color: #4750c7;
        }
        h2 {
            font-size: 13px;
            color: #000a39;
            margin: 18px 0 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            border: 1px solid #dde1ff;
            padding: 7px 9px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background: #ececff;
            color: #000a39;
            font-weight: 600;
        }
        .num {
            text-align: right;
        }
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #dde1ff;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #767680;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .toolbar {
                display: none !important;
            }
            .sheet {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ route('dashboard.okupansi', ['klaster' => $klasterFilter, 'periode' => $periodeFilter]) }}" class="btn btn-secondary">
            &larr; Kembali ke Dashboard
        </a>
        <button type="button" onclick="window.print()" class="btn btn-primary">
            Cetak / Simpan sebagai PDF
        </button>
    </div>

    <div class="sheet">
        <div class="header">
            <div>
                <h1>Laporan Eksekutif Okupansi Asrama</h1>
                <p>Andalas Residence &mdash; Universitas Andalas, Kampus Limau Manis</p>
                <p>
                    Periode: <strong>{{ $selectedPeriode ? $selectedPeriode->nama_periode . ' (' . $selectedPeriode->tahun_akademik . ')' : 'Semua Periode' }}</strong>
                    &bull; Klaster: <strong>{{ $klasterFilter === 'all' ? 'Semua Klaster' : ucfirst($klasterFilter) }}</strong>
                </p>
            </div>
            <div class="meta">
                <div>Dicetak: {{ now('Asia/Jakarta')->format('d M Y, H:i') }} WIB</div>
                <div>Oleh: {{ auth()->user()?->name ?? 'Pimpinan' }}</div>
            </div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Total Kapasitas</div>
                <div class="kpi-value">{{ number_format($kpi['total_bed'], 0, ',', '.') }} Bed</div>
                <div class="kpi-sub">{{ $kpi['total_gedung'] }} Gedung Terdaftar</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Okupansi Terisi</div>
                <div class="kpi-value">{{ number_format($kpi['total_terisi'], 0, ',', '.') }} Bed</div>
                <div class="kpi-sub">{{ $kpi['persentase_okupansi'] }}% Terisi</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Bed Tersedia</div>
                <div class="kpi-value">{{ number_format($kpi['total_tersedia'], 0, ',', '.') }} Bed</div>
                <div class="kpi-sub">{{ $kpi['persentase_tersedia'] }}% Kuota Kosong</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Dalam Perbaikan</div>
                <div class="kpi-value">{{ $kpi['kamar_perbaikan'] }} Kamar</div>
                <div class="kpi-sub">Status Maintenance</div>
            </div>
        </div>

        <h2>Rincian Okupansi per Gedung</h2>
        <table>
            <thead>
                <tr>
                    <th>Gedung</th>
                    <th>Klaster</th>
                    <th class="num">Kamar</th>
                    <th class="num">Total Bed</th>
                    <th class="num">Terisi</th>
                    <th class="num">Tersedia</th>
                    <th class="num">Okupansi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($gedungList as $row)
                    <tr>
                        <td><strong>{{ $row['nama'] }}</strong></td>
                        <td>{{ $row['klaster_label'] }}</td>
                        <td class="num">{{ $row['kamar'] }}</td>
                        <td class="num">{{ number_format($row['total_bed'], 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($row['terisi'], 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($row['tersedia'], 0, ',', '.') }}</td>
                        <td class="num">{{ $row['persentase'] }}%</td>
                        <td>{{ $row['status'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;">Tidak ada data gedung untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h2>Komposisi Penghuni Aktif</h2>
        <table>
            <thead>
                <tr>
                    <th>Kategori Penghuni</th>
                    <th class="num">Jumlah Penghuni</th>
                    <th class="num">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($komposisi as $item)
                    <tr>
                        <td>{{ $item['label'] }}</td>
                        <td class="num">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                        <td class="num">{{ $item['persen'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <span>Sistem Informasi Manajemen Kamar dan Layanan Hunian (Andalas Residence)</span>
            <span>Dokumen Laporan Eksekutif</span>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
