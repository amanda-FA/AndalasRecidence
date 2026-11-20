<?php

namespace App\Services;

use App\Models\{Fasilitas, Gedung, Kamar};
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FacilitySelectionService
{
    public static function rules(string $scope): array
    {
        return [
            'fasilitas' => ['sometimes', 'array', 'max:200'],
            'fasilitas.*' => ['required', 'integer', 'distinct', Rule::exists('fasilitas', 'id_fasilitas')->whereNull('id_gedung')->whereNull('id_kamar')->whereNull('id_fasilitas_acuan')->where('aktif', true)->where('lingkup', $scope)],
        ];
    }

    public function formData(string $scope, Gedung|Kamar|null $owner = null): array
    {
        $order = array_flip(Fasilitas::DEFAULT_CHOICES[$scope]);
        $options = Fasilitas::pilihan($scope)->orderBy('nama_fasilitas')->get()
            ->sortBy(fn ($option) => $order[$option->nama_fasilitas] ?? 1000)->values();
        $existing = $owner ? $this->inventory($owner)->where('aktif', true)->get() : collect();
        return [
            'facilityScope' => $scope,
            'facilityOptions' => $options,
            'selectedFacilityIds' => $options->filter(fn ($option) => $existing->contains(fn ($item) => $item->id_fasilitas_acuan === $option->id_fasilitas || mb_strtolower($item->nama_fasilitas) === mb_strtolower($option->nama_fasilitas)))->pluck('id_fasilitas')->all(),
        ];
    }

    private function inventory(Gedung|Kamar $owner)
    {
        return $owner instanceof Kamar
            ? Fasilitas::where('id_kamar', $owner->id_kamar)
            : Fasilitas::where('id_gedung', $owner->id_gedung)->whereNull('id_kamar');
    }

    public function deleteUnusedInventory(Gedung|Kamar $owner): bool
    {
        $query = $this->inventory($owner);
        if ((clone $query)->where(fn ($item) => $item->whereHas('laporanKerusakans')->orWhereHas('detailPengecekans'))->exists()) return false;
        $query->delete();
        return true;
    }

    // Called inside the same transaction as the building/room save.
    public function sync(Gedung|Kamar $owner, array $ids): void
    {
        $scope = $owner instanceof Kamar ? 'kamar' : 'gedung';
        $catalog = Fasilitas::pilihan($scope)->lockForUpdate()->get();
        $choices = $catalog->whereIn('id_fasilitas', $ids);
        if ($choices->count() !== count($ids)) {
            throw ValidationException::withMessages(['fasilitas' => 'Pilihan fasilitas berubah. Muat ulang formulir.']);
        }
        $existing = $this->inventory($owner)->lockForUpdate()->get();
        $used = [];
        foreach ($existing as $facility) {
            $option = $catalog->first(fn ($item) => $item->id_fasilitas === $facility->id_fasilitas_acuan || mb_strtolower($item->nama_fasilitas) === mb_strtolower($facility->nama_fasilitas));
            // Legacy inventory outside this form's choices must retain its history and active status.
            if (! $option) {
                if ($owner instanceof Kamar && $facility->id_gedung !== $owner->id_gedung) $facility->update(['id_gedung' => $owner->id_gedung]);
                continue;
            }
            $choice = $choices->firstWhere('id_fasilitas', $option->id_fasilitas);
            $attributes = ['aktif' => $choice !== null];
            if ($owner instanceof Kamar) $attributes['id_gedung'] = $owner->id_gedung;
            if ($choice) {
                $attributes['id_fasilitas_acuan'] = $choice->id_fasilitas;
                $used[] = $choice->id_fasilitas;
            }
            $facility->update($attributes);
        }
        foreach ($choices->whereNotIn('id_fasilitas', $used) as $choice) {
            Fasilitas::create([
                'id_fasilitas_acuan' => $choice->id_fasilitas,
                'nama_fasilitas' => $choice->nama_fasilitas,
                'id_gedung' => $owner->id_gedung,
                'id_kamar' => $owner instanceof Kamar ? $owner->id_kamar : null,
                'kondisi' => 'Baik', 'aktif' => true,
            ]);
        }
    }
}
