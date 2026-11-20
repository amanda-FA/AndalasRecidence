<?php

namespace App\Http\Controllers\Gedung;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PilihanFasilitasController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (is_string($request->input('nama_fasilitas'))) {
            $request->merge(['nama_fasilitas' => Str::squish($request->input('nama_fasilitas'))]);
        }
        $data = $request->validate([
            'nama_fasilitas' => ['required', 'string', 'max:100'],
            'lingkup' => ['required', Rule::in(['gedung', 'kamar'])],
        ]);
        [$facility, $created] = DB::transaction(function () use ($data) {
            $facility = Fasilitas::pilihan($data['lingkup'])->whereRaw('LOWER(nama_fasilitas) = ?', [mb_strtolower($data['nama_fasilitas'])])->lockForUpdate()->first();
            if ($facility) return [$facility, false];
            $facility = Fasilitas::pilihan()->whereNull('lingkup')->whereRaw('LOWER(nama_fasilitas) = ?', [mb_strtolower($data['nama_fasilitas'])])->lockForUpdate()->first();
            if ($facility) $facility->update(['lingkup' => $data['lingkup']]);
            else $facility = Fasilitas::create($data + ['aktif' => true]);
            return [$facility, true];
        }, 3);
        return response()->json([
            'id' => $facility->id_fasilitas, 'name' => $facility->nama_fasilitas,
            'message' => $created ? 'Pilihan fasilitas berhasil ditambahkan.' : 'Fasilitas sudah tersedia dan dipilih.',
        ], $created ? 201 : 200);
    }
}
