<?php

namespace App\Http\Controllers\Penempatan;

use App\Http\Controllers\Controller;
use App\Models\PenempatanKamar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenempatanController extends Controller
{
    public function index(Request $request): View
    {
        $placements = PenempatanKamar::forUser($request->user()->getAuthIdentifier())
            ->with(['kamar.gedung', 'kamar.tipeKamar', 'pendaftaranHunian.periode', 'admin'])
            ->orderByDesc('tanggal_masuk')->orderByDesc('id_penempatan')->get();
        $current = $placements->filter(fn ($placement) => $placement->status_penempatan === 'aktif'
            && ($placement->rencana_keluar === null || $placement->rencana_keluar->toDateString() >= today()->toDateString())
            && $placement->tanggal_keluar === null && $placement->tanggal_masuk->toDateString() <= today('Asia/Jakarta')->toDateString());
        $riwayat = $placements->map(function ($placement): array {
            $room = $placement->kamar;
            $building = $room?->gedung;
            // Kamar memiliki accessor tipeKamar untuk UI lama; ambil relasi yang sudah dimuat.
            $type = $room?->getRelation('tipeKamar');
            $period = $placement->pendaftaranHunian?->periode;

            return [
                'id_penempatan' => $placement->id_penempatan,
                'id_pendaftaran' => $placement->id_pendaftaran,
                'id_kamar' => $placement->id_kamar,
                'nomor_bed' => $placement->nomor_bed,
                'rencana_keluar' => $placement->rencana_keluar?->format('d M Y'),
                'jadwal_kunci' => $placement->jadwal_kunci_mulai?->format('d M Y H:i'),
                'lokasi_kunci' => $placement->lokasi_kunci,
                'penanggung_jawab' => $placement->penanggung_jawab,
                'kontak_penanggung_jawab' => $placement->kontak_penanggung_jawab,
                'id_admin' => $placement->id_admin,
                'nomor_kamar' => $room?->nomor_kamar ?? '—',
                'lantai' => $room?->lantai ?? '—',
                'nama_gedung' => $building?->nama_gedung ?? '—',
                'nama_singkat' => $building?->nama_gedung ?? '—',
                'jenis_penghuni' => $building?->jenis_penghuni ?? '—',
                'nama_tipe' => $type?->nama_tipe ?? '—',
                'kapasitas' => $type?->kapasitas ?? '—',
                'tahun_akademik' => $period?->tahun_akademik ?? '—',
                'nama_periode' => $period?->nama_periode ?? '—',
                'tanggal_masuk' => $placement->tanggal_masuk?->toDateString(),
                'tanggal_keluar' => $placement->tanggal_keluar?->toDateString(),
                'status_penempatan' => $placement->status_penempatan,
                'admin' => $placement->admin?->nama_lengkap ?? 'Belum tercatat',
            ];
        });
        $currentId = $current->first()?->id_penempatan;
        $aktif = $riwayat->firstWhere('id_penempatan', $currentId);
        $sebelumnya = $aktif ? $riwayat->first(fn ($row) => $row['id_penempatan'] !== $currentId
            && $row['tanggal_keluar'] && $row['tanggal_keluar'] <= $aktif['tanggal_masuk']) : null;

        return view('penempatan.index', compact('riwayat', 'aktif', 'sebelumnya') + ['jumlahAktif' => $current->count()]);
    }
}
