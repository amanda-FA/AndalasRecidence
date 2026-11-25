<?php

namespace App\Http\Requests\GO;

use Illuminate\Foundation\Http\FormRequest;

class SimpanPengecekanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('GO') || auth()->user()->hasRole('General Officer'));
    }

    public function rules(): array
    {
        return [
            'catatan' => 'nullable|string|max:2000',
            'kondisi_kamar' => 'nullable|in:baik,rusak_ringan,rusak_berat',
            'checklist_a' => 'nullable|array',
            'checklist_a.*' => 'in:baik,rusak_ringan,rusak_berat',
            'checklist_b' => 'nullable|array',
            'checklist_b.*' => 'in:baik,rusak_ringan,rusak_berat',
            'checklist_c' => 'nullable|array',
            'checklist_c.*' => 'in:baik,rusak_ringan,rusak_berat',
        ];
    }

    public function messages(): array
    {
        return [
            'kondisi_kamar.in' => 'Kondisi kamar harus bernilai baik, rusak ringan, atau rusak berat.',
            'checklist_a.*.in' => 'Kondisi item Bagian A harus bernilai valid.',
            'checklist_b.*.in' => 'Kondisi item Bagian B harus bernilai valid.',
            'checklist_c.*.in' => 'Kondisi item Bagian C harus bernilai valid.',
        ];
    }
}
