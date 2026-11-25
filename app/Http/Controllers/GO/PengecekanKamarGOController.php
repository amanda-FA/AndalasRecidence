<?php

namespace App\Http\Controllers\GO;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCheckout;
use App\Models\Pengecekan;
use App\Models\Gedung;
use Illuminate\Http\Request;

class PengecekanKamarGOController extends Controller
{
    public function index(Request $request)
    {
        $gedungFilter = $request->query('gedung', 'all');
        $statusFilter = $request->query('status', 'all');
        $sort = $request->query('sort', 'terbaru');

        $query = PengajuanCheckout::with([
            'penempatanKamar.kamar.gedung',
            'penempatanKamar.pendaftaranHunian.client.pengguna',
            'pengecekan',
        ])->where('status_pengajuan', 'disetujui');

        if ($gedungFilter !== 'all') {
            $query->whereHas('penempatanKamar.kamar.gedung', fn ($q) => $q->where('id_gedung', $gedungFilter));
        }

        $checkouts = $query->orderByDesc('tanggal_pengajuan')->get();

        $antrean = $checkouts->map(function ($checkout) {
            $penempatan = $checkout->penempatanKamar;
            $kamar = $penempatan?->kamar;
            $gedung = $kamar?->gedung;
            $client = $penempatan?->pendaftaranHunian?->client;
            $pengguna = $client?->pengguna;
            $pengecekan = $checkout->pengecekan;

            if ($pengecekan && $pengecekan->status_verifikasi === 'selesai') {
                $status = 'selesai';
                $statusLabel = 'Selesai Pengecekan';
                $statusColor = 'bg-success/10 text-success';
            } elseif ($pengecekan) {
                $status = 'dalam_pengecekan';
                $statusLabel = 'Dalam Pengecekan';
                $statusColor = 'bg-[#C97D1A]/10 text-[#C97D1A]';
            } else {
                $status = 'menunggu';
                $statusLabel = 'Menunggu Pengecekan';
                $statusColor = 'bg-error/10 text-error';
            }

            return [
                'id_pengajuan' => $checkout->id_pengajuan,
                'nama' => $pengguna?->nama_lengkap ?? 'N/A',
                'kamar' => $kamar?->nomor_kamar ?? '-',
                'gedung' => $gedung?->nama_gedung ?? '-',
                'id_gedung' => $gedung?->id_gedung,
                'tanggal_checkout' => $checkout->tanggal_rencanaKeluar,
                'tanggal_checkout_formatted' => $checkout->tanggal_rencanaKeluar?->format('d M Y') ?? '-',
                'status' => $status,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
            ];
        });

        if ($statusFilter !== 'all') {
            $antrean = $antrean->where('status', $statusFilter)->values();
        }

        if ($sort === 'terdekat') {
            $antrean = $antrean->sortBy('tanggal_checkout')->values();
        }

        $gedungList = Gedung::orderBy('nama_gedung')->pluck('nama_gedung', 'id_gedung');

        return view('go.pengecekan-kamar', compact('antrean', 'gedungList', 'gedungFilter', 'statusFilter', 'sort'));
    }

    public function mulai($id)
    {
        $checkout = PengajuanCheckout::findOrFail($id);
        $user = auth()->user();

        Pengecekan::firstOrCreate(
            ['id_pengajuan' => $checkout->id_pengajuan],
            [
                'id_pengguna' => $user->id_pengguna,
                'tanggal_cek' => now(),
                'kondisi_kamar' => 'dalam_pengecekan',
                'catatan' => '',
                'status_verifikasi' => 'dalam_pengecekan',
            ]
        );

        return redirect()->route('go.pengecekan-kamar.index')
            ->with('success', 'Pengecekan dimulai untuk pengajuan #' . $id);
    }

    public function show($id)
    {
        $checkout = PengajuanCheckout::with([
            'penempatanKamar.kamar.gedung',
            'penempatanKamar.kamar.fasilitas',
            'penempatanKamar.pendaftaranHunian.client.pengguna',
            'pengecekan.detailPengecekans.fasilitas',
        ])->findOrFail($id);

        $penempatan = $checkout->penempatanKamar;
        $kamar = $penempatan?->kamar;
        $gedung = $kamar?->gedung;
        $client = $penempatan?->pendaftaranHunian?->client;
        $pengguna = $client?->pengguna;
        $pengecekan = $checkout->pengecekan;
        $petugas = auth()->user();

        $fasilitasKamar = $kamar?->fasilitas ?? collect();

        $checklistA = [
            ['nama' => 'Dinding & Cat', 'icon' => 'wall_art'],
            ['nama' => 'Plafon / Langit-langit', 'icon' => 'roofing'],
            ['nama' => 'Pintu Utama & Handel', 'icon' => 'door_front'],
            ['nama' => 'Jendela Kaca', 'icon' => 'window'],
            ['nama' => 'Ubin / Keramik Lantai', 'icon' => 'grid_on'],
        ];

        $checklistB = [
            ['nama' => 'Tempat Tidur & Kasur Spring Bed', 'icon' => 'bed'],
            ['nama' => 'Lemari Pakaian 2 Pintu & Kunci Loker', 'icon' => 'dresser'],
            ['nama' => 'Meja Belajar & Kursi', 'icon' => 'table_restaurant'],
        ];

        $checklistC = [
            ['nama' => 'Unit AC Split & Remote AC', 'icon' => 'ac_unit'],
            ['nama' => 'Sakelar Lampu / Stop Kontak / MCB', 'icon' => 'electrical_services'],
            ['nama' => 'Instalasi Kamar Mandi Dalam', 'icon' => 'bathroom'],
        ];

        $nomorAudit = '#CHK-' . now()->format('Y') . '-' . str_pad($checkout->id_pengajuan, 4, '0', STR_PAD_LEFT);

        return view('go.form-pengecekan', compact(
            'checkout', 'penempatan', 'kamar', 'gedung', 'pengguna',
            'pengecekan', 'petugas', 'fasilitasKamar',
            'checklistA', 'checklistB', 'checklistC', 'nomorAudit'
        ));
    }

    public function selesai(Request $request, $id)
    {
        $request->validate([
            'kondisi_kamar' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan' => 'nullable|string|max:2000',
            'kunci_rfid' => 'nullable|in:valid,tidak_valid',
            'kunci_fisik' => 'nullable|in:dikembalikan,belum',
        ]);

        $pengecekan = Pengecekan::where('id_pengajuan', $id)->firstOrFail();
        $pengecekan->update([
            'kondisi_kamar' => $request->kondisi_kamar,
            'catatan' => $request->catatan ?? '',
            'status_verifikasi' => 'selesai',
        ]);

        return redirect()->route('go.pengecekan-kamar.index')
            ->with('success', 'Pengecekan selesai dan diteruskan ke Fasilitator.');
    }
}
