<?php

namespace App\Http\Controllers\VerifikasiCheckout;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCheckout;
use Illuminate\Http\Request;

class VerifikasiCheckoutController extends Controller
{
    /**
     * Menampilkan daftar pengajuan check-out
     * dan bebas asrama untuk Fasilitator.
     */
    public function index(Request $request)
    {
        $query = PengajuanCheckout::with([
            'penempatanKamar.pendaftaranHunian.client.pengguna',
            'penempatanKamar.kamar.gedung',
            'pengecekan.pengguna',
            'suratBebasAsrama',
        ]);

        // Pencarian berdasarkan nama penghuni atau NIM
        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas(
                'penempatanKamar.pendaftaranHunian.client',
                function ($client) use ($search) {
                    $client->where('nomor_identitas', 'like', "%{$search}%")
                        ->orWhereHas('pengguna', function ($pengguna) use ($search) {
                            $pengguna->where(
                                'nama_lengkap',
                                'like',
                                "%{$search}%"
                            );
                        });
                }
            );
        }

        // Filter berdasarkan status pengajuan
        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->status);
        }

        $pengajuan = $query
            ->latest('id_pengajuan')
            ->paginate(10)
            ->withQueryString();

        // Statistik seluruh pengajuan
        $stats = [
            'total' => PengajuanCheckout::count(),

            'menunggu' => PengajuanCheckout::where(
                'status_pengajuan',
                PengajuanCheckout::STATUS_MENUNGGU_PERSETUJUAN
            )->count(),

            'disetujui' => PengajuanCheckout::where(
                'status_pengajuan',
                PengajuanCheckout::STATUS_DISETUJUI
            )->count(),

            'ditolak' => PengajuanCheckout::where(
                'status_pengajuan',
                PengajuanCheckout::STATUS_DITOLAK
            )->count(),
        ];

        return view('verifikasi-checkout.index', compact(
            'pengajuan',
            'stats'
        ));
    }
}
