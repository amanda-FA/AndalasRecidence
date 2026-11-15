<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\Gedung;
use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataAsramaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Tipe Kamar
        $tipeKamarData = [
            ['key' => 'Standar_2', 'nama_tipe' => 'Standar 2 Orang', 'kapasitas' => 2, 'tarif' => 2500000.0, 'deskripsi' => 'Kamar Standar 2 orang (Dipan, Lemari, Meja Belajar)'],
            ['key' => 'Standar_4', 'nama_tipe' => 'Standar 4 Orang', 'kapasitas' => 4, 'tarif' => 2500000.0, 'deskripsi' => 'Kamar Standar 4 orang (Full Furniture)'],
            ['key' => 'Standar_1', 'nama_tipe' => 'Standar 1 Keluarga', 'kapasitas' => 1, 'tarif' => 700000.0, 'deskripsi' => 'Unit Standar Keluarga (KM Dalam, Dapur, 2 Kamar, R. Tamu)'],
            ['key' => 'Medium_2', 'nama_tipe' => 'Medium 2 Orang', 'kapasitas' => 2, 'tarif' => 6000000.0, 'deskripsi' => 'Kamar Medium 2 orang (KM Dalam/Luar, Full Furniture)'],
            ['key' => 'Medium_3', 'nama_tipe' => 'Medium 3 Orang', 'kapasitas' => 3, 'tarif' => 4000000.0, 'deskripsi' => 'Kamar Medium 3 orang (KM Dalam, Full Furniture)'],
            ['key' => 'Medium_4', 'nama_tipe' => 'Medium 4 Orang', 'kapasitas' => 4, 'tarif' => 3000000.0, 'deskripsi' => 'Kamar Medium 4 orang (KM Luar/Dalam, Full Furniture)'],
            ['key' => 'Medium_1', 'nama_tipe' => 'Medium 1 Keluarga', 'kapasitas' => 1, 'tarif' => 800000.0, 'deskripsi' => 'Unit Medium Keluarga (KM Dalam, Dapur, 2 Kamar, R. Tamu)'],
            ['key' => 'Premium_1', 'nama_tipe' => 'Premium 1 Keluarga', 'kapasitas' => 1, 'tarif' => 900000.0, 'deskripsi' => 'Unit Premium Keluarga (KM Dalam, Dapur, 2 Kamar, R. Tamu)'],
            ['key' => 'Premium_2', 'nama_tipe' => 'Premium 2 Orang', 'kapasitas' => 2, 'tarif' => 7500000.0, 'deskripsi' => 'Kamar Premium 2 orang (KM Dalam, AC, Spring Bed, Full Furniture)'],
            ['key' => 'Premium_4', 'nama_tipe' => 'Premium 4 Orang', 'kapasitas' => 4, 'tarif' => 3750000.0, 'deskripsi' => 'Kamar Premium 4 orang (KM Luar/Dalam, AC, Full Furniture)'],
            ['key' => 'Umum_2', 'nama_tipe' => 'Umum 2 Orang', 'kapasitas' => 2, 'tarif' => 100000.0, 'deskripsi' => 'Kamar Harian Umum 2 orang (Dipan, Lemari, Meja Belajar)'],
            ['key' => 'Umum_4', 'nama_tipe' => 'Umum 4 Orang', 'kapasitas' => 4, 'tarif' => 100000.0, 'deskripsi' => 'Kamar Harian Umum 4 orang (Full Furniture)'],
            ['key' => 'Umum Vip_2', 'nama_tipe' => 'Umum VIP 2 Orang', 'kapasitas' => 2, 'tarif' => 300000.0, 'deskripsi' => 'Kamar Harian Umum VIP (KM Dalam, AC, Full Furniture)'],
        ];

        $tipeKamarIds = [];
        foreach ($tipeKamarData as $tk) {
            $model = TipeKamar::firstOrCreate(
                ['nama_tipe' => $tk['nama_tipe']],
                [
                    'kapasitas' => $tk['kapasitas'],
                    'tarif' => $tk['tarif'],
                    'deskripsi' => $tk['deskripsi'],
                ]
            );
            $tipeKamarIds[$tk['key']] = $model->id_tipeKamar;
        }

        // Default fallback tipe kamar
        $defaultTipeId = reset($tipeKamarIds);

        // 2. Seed Gedung, Fasilitas Gedung, dan Kamar

        // --- Gedung: RPX (A) ---
        $gedung_rpx = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama RPX (A)'],
            [
                'jenis_penghuni' => 'campuran',
                'jumlah_lantai' => 4,
                'keterangan' => 'Hunian asrama mahasiswa dan umum (kamar disewakan)',
            ]
        );

        // Fasilitas Gedung RPX (A)
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rpx->id_gedung,
            'nama_fasilitas' => 'Dipan',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rpx->id_gedung,
            'nama_fasilitas' => 'Lemari',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rpx->id_gedung,
            'nama_fasilitas' => 'Meja Belajar',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rpx->id_gedung,
            'nama_fasilitas' => 'WiFi Eduroam',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rpx->id_gedung,
            'nama_fasilitas' => 'Musholla',
            'kondisi' => 'Baik',
        ]);

        $kamars_rpx = [
            ['nk' => '1A.01', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.02', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.03', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.04', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.05', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.06', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.07', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.08', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.09', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.10', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.11', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.12', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.13', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.14', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.15', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.16', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.17', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.18', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.19', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.20', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.21', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.22', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.23', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.24', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.25', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.27', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.28', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.29', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.30', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.31', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.32', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.33', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '2A.01', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.02', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.03', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.04', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.05', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.06', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.07', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.08', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.09', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.10', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.11', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.12', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.13', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.14', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.15', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.16', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.17', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.18', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.19', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.20', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.21', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.22', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.23', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.24', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.25', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.26', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.27', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.28', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.29', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.30', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.31', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.32', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.33', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '3A.01', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.02', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.03', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.04', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.05', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.06', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.07', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.08', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.09', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.10', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.11', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.12', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.13', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.14', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.15', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.16', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.17', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.18', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.19', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.20', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.21', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.22', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.23', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.24', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.25', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.26', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.27', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.28', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.29', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.30', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.31', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.32', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.33', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '4A.01', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.02', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.03', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.04', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.05', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.06', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.07', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.08', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.09', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.10', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.11', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.12', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.13', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.14', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.15', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.16', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4A.17', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.18', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.19', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.20', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.21', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.22', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.23', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.24', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.25', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.26', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.27', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.28', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.29', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.30', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.31', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.32', 'tp' => 'Standar_2', 'lt' => 4],
            ['nk' => '4B.33', 'tp' => 'Standar_2', 'lt' => 4],
        ];

        foreach ($kamars_rpx as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_rpx->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }

        // --- Gedung: Rusunawa (B) ---
        $gedung_rus = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama Rusunawa (B)'],
            [
                'jenis_penghuni' => 'putra',
                'jumlah_lantai' => 5,
                'keterangan' => 'Rusunawa asrama mahasiswa putra dan umum',
            ]
        );

        // Fasilitas Gedung Rusunawa (B)
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rus->id_gedung,
            'nama_fasilitas' => 'Kamar Mandi Dalam',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rus->id_gedung,
            'nama_fasilitas' => 'AC',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rus->id_gedung,
            'nama_fasilitas' => 'Spring Bed',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rus->id_gedung,
            'nama_fasilitas' => 'WiFi Eduroam',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rus->id_gedung,
            'nama_fasilitas' => 'Kantin',
            'kondisi' => 'Baik',
        ]);

        $kamars_rus = [
            ['nk' => '1A.01', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.02', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1A.03', 'tp' => 'Premium_2', 'lt' => 1],
            ['nk' => '1A.04', 'tp' => 'Premium_2', 'lt' => 1],
            ['nk' => '1B.01', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.02', 'tp' => 'Premium_2', 'lt' => 1],
            ['nk' => '1B.03', 'tp' => 'Standar_2', 'lt' => 1],
            ['nk' => '1B.04', 'tp' => 'Premium_2', 'lt' => 1],
            ['nk' => '2A.01', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.02', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.03', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.04', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '2A.05', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.06', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.07', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.08', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.09', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.10', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.11', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2A.12', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '2B.01', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '2B.02', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '2B.03', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '2B.04', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '2B.05', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.06', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.07', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '2B.08', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.09', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.10', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.11', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '3A.13', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3A.14', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '3A.15', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '3A.16', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '3A.17', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.18', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3A.19', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.20', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3A.21', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3A.22', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3A.23', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3A.24', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3B.12', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '3B.13', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '3B.14', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '3B.15', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3B.16', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '3B.17', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3B.18', 'tp' => 'Standar_2', 'lt' => 3],
            ['nk' => '3B.19', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3B.20', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3B.21', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '3B.22', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '4A.25', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4A.26', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4A.27', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '4A.28', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '4A.29', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4A.30', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4A.31', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4A.32', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4A.33', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4A.34', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '4A.35', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4A.36', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.23', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.24', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.25', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.26', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.27', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.28', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.29', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.30', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.31', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '4B.32', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '4B.33', 'tp' => 'Premium_2', 'lt' => 4],
            ['nk' => '5A.37', 'tp' => 'Standar_2', 'lt' => 5],
            ['nk' => '5A.38', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5A.39', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5A.40', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5A.41', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5A.42', 'tp' => 'Medium_4', 'lt' => 5],
            ['nk' => '5A.43', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5A.44', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5A.45', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5A.46', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5A.47', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5A.48', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5B.34', 'tp' => 'Standar_2', 'lt' => 5],
            ['nk' => '5B.35', 'tp' => 'Standar_2', 'lt' => 5],
            ['nk' => '5B.36', 'tp' => 'Standar_2', 'lt' => 5],
            ['nk' => '5B.37', 'tp' => 'Standar_2', 'lt' => 5],
            ['nk' => '5B.38', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5B.39', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5B.40', 'tp' => 'Medium_4', 'lt' => 5],
            ['nk' => '5B.41', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5B.42', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5B.43', 'tp' => 'Premium_2', 'lt' => 5],
            ['nk' => '5B.44', 'tp' => 'Premium_2', 'lt' => 5],
        ];

        foreach ($kamars_rus as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_rus->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }

        // --- Gedung: Pupera Putri (C) ---
        $gedung_pup = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama Pupera Putri (C)'],
            [
                'jenis_penghuni' => 'putri',
                'jumlah_lantai' => 4,
                'keterangan' => 'Asrama mahasiswi Pupera Putri',
            ]
        );

        // Fasilitas Gedung Pupera Putri (C)
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_pup->id_gedung,
            'nama_fasilitas' => 'Kamar Mandi Luar',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_pup->id_gedung,
            'nama_fasilitas' => 'Full Furniture',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_pup->id_gedung,
            'nama_fasilitas' => 'Dapur Bersama',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_pup->id_gedung,
            'nama_fasilitas' => 'Pos Satpam',
            'kondisi' => 'Baik',
        ]);

        $kamars_pup = [
            ['nk' => '101', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '102', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '103', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '104', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '105', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '106', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '108', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '109', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '110', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '111', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '201', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '202', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '203', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '204', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '205', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '206', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '207', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '208', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '209', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '210', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '211', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '212', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '213', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '301', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '302', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '303', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '304', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '305', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '306', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '307', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '308', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '309', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '310', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '311', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '312', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '313', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '401', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '402', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '403', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '404', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '405', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '406', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '407', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '408', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '409', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '410', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '411', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '412', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '413', 'tp' => 'Medium_4', 'lt' => 4],
        ];

        foreach ($kamars_pup as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_pup->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }

        // --- Gedung: Menpera (D) ---
        $gedung_men = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama Menpera (D)'],
            [
                'jenis_penghuni' => 'putra',
                'jumlah_lantai' => 5,
                'keterangan' => 'Asrama mahasiswa putra Menpera (Reguler & KIP-K)',
            ]
        );

        // Fasilitas Gedung Menpera (D)
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_men->id_gedung,
            'nama_fasilitas' => 'Kamar Mandi Luar',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_men->id_gedung,
            'nama_fasilitas' => 'Full Furniture',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_men->id_gedung,
            'nama_fasilitas' => 'Lapangan Olahraga',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_men->id_gedung,
            'nama_fasilitas' => 'Musholla',
            'kondisi' => 'Baik',
        ]);

        $kamars_men = [
            ['nk' => '1A.01', 'tp' => 'Standar_4', 'lt' => 1],
            ['nk' => '1A.02', 'tp' => 'Standar_4', 'lt' => 1],
            ['nk' => '1A.03', 'tp' => 'Standar_4', 'lt' => 1],
            ['nk' => '1A.04', 'tp' => 'Standar_4', 'lt' => 1],
            ['nk' => '2A.01', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.02', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.03', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.04', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.05', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.06', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.07', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.08', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.09', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.10', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.11', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.12', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.01', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.02', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.03', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.04', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.05', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.06', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.07', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.08', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.09', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.10', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.11', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.12', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '3A.13', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.14', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.15', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.16', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.17', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.18', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.19', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.20', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.21', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.22', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.23', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.24', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B13', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B14', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B15', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B16', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B17', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B18', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B19', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B20', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B21', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B22', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B23', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B24', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '4A.25', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.26', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.27', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.28', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.29', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.30', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.31', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.32', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.33', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.34', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.35', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.36', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.25', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.26', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.27', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.28', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.29', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.30', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.31', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.32', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.33', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.34', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.35', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '4B.36', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.37', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.38', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.39', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.40', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.41', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.42', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.43', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.44', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.45', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.46', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.47', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.48', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.37', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.38', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.39', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.40', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.41', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.42', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.43', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.44', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.45', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.46', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.47', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.48', 'tp' => 'Standar_4', 'lt' => 5],
        ];

        foreach ($kamars_men as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_men->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }

        // --- Gedung: RMS (E) ---
        $gedung_rms = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama RMS (E)'],
            [
                'jenis_penghuni' => 'putri',
                'jumlah_lantai' => 4,
                'keterangan' => 'Asrama mahasiswi UNAND Gedung E',
            ]
        );

        // Fasilitas Gedung RMS (E)
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rms->id_gedung,
            'nama_fasilitas' => 'Kamar Mandi Dalam',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rms->id_gedung,
            'nama_fasilitas' => 'Full Furniture',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rms->id_gedung,
            'nama_fasilitas' => 'Dapur Bersama',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_rms->id_gedung,
            'nama_fasilitas' => 'CCTV 24 Jam',
            'kondisi' => 'Baik',
        ]);

        $kamars_rms = [
            ['nk' => 'MS 1. 2', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'MS 1. 4', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'MS 1.5', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'MS 1.6', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'MS 1.7', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'MS 1.8', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'MS 1.9', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'MS 2.10', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.11', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.12', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => 'MS 2.13', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.14', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.15', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.16', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.17', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.18', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.19', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.20', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 2.21', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'MS 3.22', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.23', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.24', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.25', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.26', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.27', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.28', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.29', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.30', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.31', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.32', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 3.33', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'MS 4.34', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.35', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.36', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.37', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.38', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.39', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.40', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.41', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.42', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.43', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'MS 4.44', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 1.1', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 1.2', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 1.3', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 1.4', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 1.5', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 1.7', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 1.8', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 1.9', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 1.10', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 1.11', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => 'RM 2.12', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => 'RM 2.13', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 2.14', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 2.15', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 2.16', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 2.17', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 2.18', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 2.19', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 2.20', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => 'RM 2.21', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 2.22', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 2.23', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => 'RM 3.24', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 3.25', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 3.26', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 3.27', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 3.28', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 3.29', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 3.30', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 3.31', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 3.32', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => 'RM 3.33', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 3.34', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => 'RM 4.35', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.36', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.37', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.38', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.39', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.40', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.41', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.42', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.43', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.44', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.45', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.46', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => 'RM 4.47', 'tp' => 'Standar_4', 'lt' => 4],
        ];

        foreach ($kamars_rms as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_rms->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }

        // --- Gedung: Oren (F) ---
        $gedung_ore = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama Oren (F)'],
            [
                'jenis_penghuni' => 'putri',
                'jumlah_lantai' => 5,
                'keterangan' => 'Asrama mahasiswi UNAND Gedung Oren',
            ]
        );

        // Fasilitas Gedung Oren (F)
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_ore->id_gedung,
            'nama_fasilitas' => 'Kamar Mandi Dalam',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_ore->id_gedung,
            'nama_fasilitas' => 'Full Furniture',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_ore->id_gedung,
            'nama_fasilitas' => 'AC',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_ore->id_gedung,
            'nama_fasilitas' => 'Ruang Belajar',
            'kondisi' => 'Baik',
        ]);

        $kamars_ore = [
            ['nk' => '1A.01', 'tp' => 'Medium_4', 'lt' => 1],
            ['nk' => '1A.02', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '1A.03', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '2A.01', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.02', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.03', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.04', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.05', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.06', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.07', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.08', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.09', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.10', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.11', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.12', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2B.01', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.02', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.03', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.04', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.05', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.06', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '2B.07', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '2B.08', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.09', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.10', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.11', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '2B.12', 'tp' => 'Standar_2', 'lt' => 2],
            ['nk' => '3A.13', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.14', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.15', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.16', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.17', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.18', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.19', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.20', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.21', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.22', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.23', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.24', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '3B13', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B14', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B15', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B16', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B17', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B18', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B19', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B20', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B21', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B22', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B23', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B24', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '4A.25', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.26', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.27', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.28', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.29', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.30', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.31', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.32', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.33', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '4A.34', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.35', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.36', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.25', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.26', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.27', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.28', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.29', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.30', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.31', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.32', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.33', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.34', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '4B.35', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '4B.36', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '5A.37', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.38', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.39', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.40', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.41', 'tp' => 'Medium_4', 'lt' => 5],
            ['nk' => '5A.42', 'tp' => 'Medium_4', 'lt' => 5],
            ['nk' => '5A.43', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.44', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.45', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.46', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.47', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.48', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.37', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.38', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.39', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.40', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.41', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.42', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.43', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.44', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.45', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.46', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.47', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.48', 'tp' => 'Standar_4', 'lt' => 5],
        ];

        foreach ($kamars_ore as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_ore->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }

        // --- Gedung: Hijau (G) ---
        $gedung_hij = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama Hijau (G)'],
            [
                'jenis_penghuni' => 'putra',
                'jumlah_lantai' => 5,
                'keterangan' => 'Asrama mahasiswa UNAND Gedung Hijau',
            ]
        );

        // Fasilitas Gedung Hijau (G)
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_hij->id_gedung,
            'nama_fasilitas' => 'Kamar Mandi Luar',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_hij->id_gedung,
            'nama_fasilitas' => 'Full Furniture',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_hij->id_gedung,
            'nama_fasilitas' => 'AC',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_hij->id_gedung,
            'nama_fasilitas' => 'Parkir Motor',
            'kondisi' => 'Baik',
        ]);

        $kamars_hij = [
            ['nk' => '2A.01', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.02', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.03', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.04', 'tp' => 'Medium_4', 'lt' => 2],
            ['nk' => '2A.05', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.06', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.07', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.08', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.09', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.10', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.11', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2A.12', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.01', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.02', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.03', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.04', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.05', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.06', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.07', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.08', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.09', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.10', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.11', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '2B.12', 'tp' => 'Standar_4', 'lt' => 2],
            ['nk' => '3A.13', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.14', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.15', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.16', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.17', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.18', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.19', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.20', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.21', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.22', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.23', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3A.24', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B13', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B14', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B15', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B16', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B17', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B18', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B19', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B20', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B21', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B22', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B23', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '3B24', 'tp' => 'Standar_4', 'lt' => 3],
            ['nk' => '4A.25', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.26', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.27', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.28', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.29', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.30', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.31', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.32', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.33', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.34', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.35', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4A.36', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.25', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.26', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.27', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.28', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.29', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.30', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.31', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.32', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.33', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.34', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.35', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '4B.36', 'tp' => 'Standar_4', 'lt' => 4],
            ['nk' => '5A.37', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.38', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.39', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.40', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.41', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.42', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.43', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.44', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.45', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.46', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.47', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5A.48', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.37', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.38', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.39', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.40', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.41', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.42', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.43', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.44', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.45', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.46', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.47', 'tp' => 'Standar_4', 'lt' => 5],
            ['nk' => '5B.48', 'tp' => 'Standar_4', 'lt' => 5],
        ];

        foreach ($kamars_hij as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_hij->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }

        // --- Gedung: Pupera Putera (H) ---
        $gedung_pup = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama Pupera Putera (H)'],
            [
                'jenis_penghuni' => 'putra',
                'jumlah_lantai' => 3,
                'keterangan' => 'Asrama mahasiswa putra Pupera',
            ]
        );

        // Fasilitas Gedung Pupera Putera (H)
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_pup->id_gedung,
            'nama_fasilitas' => 'Kamar Mandi Dalam',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_pup->id_gedung,
            'nama_fasilitas' => 'AC',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_pup->id_gedung,
            'nama_fasilitas' => 'Full Furniture',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_pup->id_gedung,
            'nama_fasilitas' => 'WiFi Eduroam',
            'kondisi' => 'Baik',
        ]);

        $kamars_pup = [
            ['nk' => '101', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '102', 'tp' => 'Medium_3', 'lt' => 1],
            ['nk' => '103', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '104', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '105', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '106', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '107', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '108', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '110', 'tp' => 'Premium_2', 'lt' => 1],
            ['nk' => '111', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '112', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '113', 'tp' => 'Medium_2', 'lt' => 1],
            ['nk' => '201', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '202', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '203', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '204', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '205', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '206', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '207', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '208', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '209', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '210', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '211', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '212', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '213', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '214', 'tp' => 'Premium_2', 'lt' => 2],
            ['nk' => '215', 'tp' => 'Medium_2', 'lt' => 2],
            ['nk' => '301', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '303', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '304', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '305', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '306', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '307', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '308', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '309', 'tp' => 'Premium_2', 'lt' => 3],
            ['nk' => '310', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '311', 'tp' => 'Medium_3', 'lt' => 3],
            ['nk' => '312', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '313', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '314', 'tp' => 'Medium_2', 'lt' => 3],
            ['nk' => '315', 'tp' => 'Medium_2', 'lt' => 3],
        ];

        foreach ($kamars_pup as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_pup->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }

        // --- Gedung: Nakes ---
        $gedung_nak = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama Nakes'],
            [
                'jenis_penghuni' => 'campuran',
                'jumlah_lantai' => 4,
                'keterangan' => 'Asrama Tenaga Kesehatan dan Mahasiswa Profesi',
            ]
        );

        // Fasilitas Gedung Nakes
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_nak->id_gedung,
            'nama_fasilitas' => 'Kamar Mandi Luar/Dalam',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_nak->id_gedung,
            'nama_fasilitas' => 'AC',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_nak->id_gedung,
            'nama_fasilitas' => 'Full Furniture',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_nak->id_gedung,
            'nama_fasilitas' => 'Akses RS UNAND',
            'kondisi' => 'Baik',
        ]);

        $kamars_nak = [
            ['nk' => '101', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '102', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '103', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '104', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '105', 'tp' => 'Umum Vip_2', 'lt' => 1],
            ['nk' => '106', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '107', 'tp' => 'Umum Vip_2', 'lt' => 1],
            ['nk' => '108', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '109', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '110', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '111', 'tp' => 'Umum_4', 'lt' => 1],
            ['nk' => '201', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '202', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '203', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '204', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '205', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '206', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '207', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '208', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '209', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '210', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '211', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '212', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '213', 'tp' => 'Premium_4', 'lt' => 2],
            ['nk' => '301', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '302', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '303', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '304', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '305', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '306', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '307', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '308', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '309', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '310', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '311', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '312', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '313', 'tp' => 'Medium_4', 'lt' => 3],
            ['nk' => '401', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '402', 'tp' => 'Premium_4', 'lt' => 4],
            ['nk' => '403', 'tp' => 'Premium_4', 'lt' => 4],
            ['nk' => '404', 'tp' => 'Premium_4', 'lt' => 4],
            ['nk' => '405', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '406', 'tp' => 'Premium_4', 'lt' => 4],
            ['nk' => '407', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '408', 'tp' => 'Premium_4', 'lt' => 4],
            ['nk' => '409', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '410', 'tp' => 'Premium_4', 'lt' => 4],
            ['nk' => '411', 'tp' => 'Medium_4', 'lt' => 4],
            ['nk' => '412', 'tp' => 'Premium_4', 'lt' => 4],
            ['nk' => '413', 'tp' => 'Medium_4', 'lt' => 4],
        ];

        foreach ($kamars_nak as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_nak->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }

        // --- Gedung: ASN ---
        $gedung_asn = Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Asrama ASN'],
            [
                'jenis_penghuni' => 'tamu',
                'jumlah_lantai' => 3,
                'keterangan' => 'Wisma dan Hunian Keluarga ASN / Tamu Universitas',
            ]
        );

        // Fasilitas Gedung ASN
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_asn->id_gedung,
            'nama_fasilitas' => 'Kamar Mandi Dalam',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_asn->id_gedung,
            'nama_fasilitas' => 'Full Furniture',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_asn->id_gedung,
            'nama_fasilitas' => 'Dapur',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_asn->id_gedung,
            'nama_fasilitas' => '2 Kamar',
            'kondisi' => 'Baik',
        ]);
        Fasilitas::firstOrCreate([
            'id_gedung' => $gedung_asn->id_gedung,
            'nama_fasilitas' => 'Ruang Tamu',
            'kondisi' => 'Baik',
        ]);

        $kamars_asn = [
            ['nk' => 'Unit 1.1', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.2', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.3', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.4', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.5', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.6', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.7', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.8', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.9', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.10', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.11', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 1.12', 'tp' => 'Premium_1', 'lt' => 1],
            ['nk' => 'Unit 2.1', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.2', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.3', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.4', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.5', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.6', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.7', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.8', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.9', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.10', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.11', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.12', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.13', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.14', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.15', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 2.16', 'tp' => 'Medium_1', 'lt' => 2],
            ['nk' => 'Unit 3.1', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.2', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.3', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.4', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.5', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.6', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.7', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.8', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.9', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.10', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.11', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.12', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.13', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.14', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.15', 'tp' => 'Standar_1', 'lt' => 3],
            ['nk' => 'Unit 3.16', 'tp' => 'Standar_1', 'lt' => 3],
        ];

        foreach ($kamars_asn as $kData) {
            $tipeId = $tipeKamarIds[$kData['tp']] ?? $defaultTipeId;
            Kamar::firstOrCreate(
                [
                    'id_gedung' => $gedung_asn->id_gedung,
                    'nomor_kamar' => $kData['nk'],
                ],
                [
                    'id_tipeKamar' => $tipeId,
                    'lantai' => $kData['lt'],
                    'status_kamar' => 'tersedia',
                ]
            );
        }
    }
}
