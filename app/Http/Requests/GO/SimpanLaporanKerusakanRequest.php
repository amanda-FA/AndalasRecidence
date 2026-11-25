<?php

namespace App\Http\Requests\GO;

use Illuminate\Foundation\Http\FormRequest;

class SimpanLaporanKerusakanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('GO') || auth()->user()->hasRole('General Officer'));
    }

    public function rules(): array
    {
        return [
            'id_fasilitas' => 'required|exists:fasilitas,id_fasilitas',
            'id_pengajuan' => 'nullable|exists:pengajuan_checkout,id_pengajuan',
            'judul_kerusakan' => 'required|string|max:255',
            'deskripsi' => 'required|string|min:10|max:3000',
            'tingkat_urgensi' => 'nullable|in:reguler,mendesak,darurat',
            'kode_aset' => 'nullable|string|max:100',
            'tim_teknisi' => 'nullable|string|max:50',
            'catatan_disposisi' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_jarak_dekat' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_sudut_ruang' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_ruang' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_tambahan' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'id_fasilitas.required' => 'Fasilitas atau inventaris kamar wajib dipilih.',
            'id_fasilitas.exists' => 'Fasilitas yang dipilih tidak valid di database.',
            'judul_kerusakan.required' => 'Judul atau ringkasan kerusakan wajib diisi.',
            'deskripsi.required' => 'Deskripsi lengkap gejala & hasil pemeriksaan wajib diisi.',
            'deskripsi.min' => 'Deskripsi gejala minimal berisi 10 karakter.',
            'foto.image' => 'File foto bukti harus berupa gambar valid (jpg, jpeg, png, webp).',
            'foto.max' => 'Ukuran file foto maksimal 5 MB.',
        ];
    }
}
