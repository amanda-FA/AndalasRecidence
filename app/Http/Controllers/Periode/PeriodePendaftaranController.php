<?php

namespace App\Http\Controllers\Periode;

use App\Http\Controllers\Controller;
use App\Models\PeriodePendaftaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodePendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tahun' => ['nullable', 'string', 'max:20'],
            'akses' => [
                'nullable',
                Rule::in(['dibuka', 'terjadwal', 'ditutup', 'berakhir']),
            ],
        ]);

        $hariIni = today()->toDateString();

        // Ringkasan seluruh periode, tidak mengikuti filter tabel.
        $semuaPeriode = PeriodePendaftaran::withCount('pendaftaranHunians')
            ->get();

        $ringkasan = [
            'total' => $semuaPeriode->count(),
            'aktif' => $semuaPeriode->filter(function ($periode) {
                return $periode->status === 'dibuka'
                    && today()->gte($periode->tanggal_buka)
                    && today()->lte($periode->tanggal_tutup);
            })->count(),
            'pendaftar' => $semuaPeriode->sum('pendaftaran_hunians_count'),
        ];

        $tahunList = PeriodePendaftaran::select('tahun_akademik')
            ->distinct()
            ->orderByDesc('tahun_akademik')
            ->pluck('tahun_akademik');

        $query = PeriodePendaftaran::withCount('pendaftaranHunians');

        if (!empty($filter['q'])) {
            $query->where('nama_periode', 'like', '%' . $filter['q'] . '%');
        }

        if (!empty($filter['tahun'])) {
            $query->where('tahun_akademik', $filter['tahun']);
        }

        switch ($filter['akses'] ?? '') {
            case 'dibuka':
                $query->where('status', 'dibuka')
                    ->whereDate('tanggal_buka', '<=', $hariIni)
                    ->whereDate('tanggal_tutup', '>=', $hariIni);
                break;

            case 'terjadwal':
                $query->where('status', 'dibuka')
                    ->whereDate('tanggal_buka', '>', $hariIni);
                break;

            case 'ditutup':
                $query->where('status', 'ditutup');
                break;

            case 'berakhir':
                $query->where('status', 'dibuka')
                    ->whereDate('tanggal_tutup', '<', $hariIni);
                break;
        }

        $periodes = $query->orderByDesc('tanggal_buka')
            ->orderByDesc('id_periode')
            ->paginate(10)
            ->withQueryString();

        return view('periode.index', compact(
            'periodes',
            'ringkasan',
            'tahunList'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_periode' => ['required', 'string', 'max:100'],
            'tahun_akademik' => ['required', 'string', 'max:20'],
            'tanggal_buka' => ['required', 'date'],
            'tanggal_tutup' => [
                'required',
                'date',
                'after_or_equal:tanggal_buka',
            ],
            'status' => ['required', Rule::in(['dibuka', 'ditutup'])],
        ], [
            'tanggal_tutup.after_or_equal' =>
                'Tanggal tutup tidak boleh sebelum tanggal buka.',
        ]);

        PeriodePendaftaran::create($data);

        return redirect()->route('periode.index')
            ->with('success', 'Periode pendaftaran berhasil ditambahkan.');
    }

    public function updateStatus(
        Request $request,
        PeriodePendaftaran $periode
    ) {
        $data = $request->validate([
            'status' => ['required', Rule::in(['dibuka', 'ditutup'])],
        ]);

        $periode->update($data);

        return redirect()->route('periode.index')
            ->with('success', 'Status periode berhasil diperbarui.');
    }

        public function destroy(PeriodePendaftaran $periode)
    {
        $pesanError = \Illuminate\Support\Facades\DB::transaction(
            function () use ($periode) {
                $data = PeriodePendaftaran::whereKey($periode->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($data->status !== 'ditutup') {
                    return 'Tutup periode terlebih dahulu sebelum menghapusnya.';
                }

                if ($data->pendaftaranHunians()->exists()) {
                    return 'Periode tidak dapat dihapus karena sudah memiliki pendaftar.';
                }

                $data->delete();

                return null;
            }
        );

        if ($pesanError !== null) {
            return redirect()->route('periode.index')
                ->with('error', $pesanError);
        }

        return redirect()->route('periode.index')
            ->with('success', 'Periode berhasil dihapus.');
    }

}