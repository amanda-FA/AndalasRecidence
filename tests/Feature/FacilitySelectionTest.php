<?php

namespace Tests\Feature;

use App\Models\{Fasilitas, Gedung, Kamar, LaporanKerusakan, Pengguna, Role, TipeKamar};
use Database\Seeders\PilihanFasilitasSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class FacilitySelectionTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role = 'Fasilitator'): Pengguna
    {
        $number = Pengguna::count() + 1;
        return Pengguna::create(['id_role' => Role::firstOrCreate(['nama_role' => $role])->id_role, 'nama_lengkap' => $role, 'username' => 'facility'.$number, 'email' => 'facility'.$number.'@example.com', 'password' => 'Password123!', 'status_aktif' => 1]);
    }

    private function building(): Gedung
    {
        return Gedung::create(['nama_gedung' => 'Gedung Fasilitas', 'jenis_penghuni' => 'putra', 'jumlah_lantai' => 2]);
    }

    private function room(Gedung $building): Kamar
    {
        $type = TipeKamar::firstOrCreate(['nama_tipe' => 'Standar'], ['kapasitas' => 2, 'tarif' => 1000000]);
        return Kamar::create(['id_gedung' => $building->id_gedung, 'id_tipeKamar' => $type->id_tipeKamar, 'nomor_kamar' => '1A.01', 'lantai' => 1, 'status_kamar' => 'tersedia']);
    }

    private function roomFields(Gedung $building, array $ids): array
    {
        return ['gedung_id' => $building->id_gedung, 'nomor_kamar' => '1A.02', 'lantai' => 1, 'tipe_kamar' => 'Standar', 'kapasitas_bed' => 2, 'status' => 'tersedia', 'fasilitas_present' => 1, 'fasilitas' => $ids];
    }

    private function buildingFields(array $ids): array
    {
        return ['nama' => 'Gedung Baru', 'peruntukan' => 'putra', 'jumlah_lantai' => 2, 'fasilitas_present' => 1, 'fasilitas' => $ids];
    }

    public function test_standalone_facility_management_is_removed(): void
    {
        $this->assertFalse(Route::has('fasilitas.index'));
        $this->actingAs($this->user())->get('/fasilitas')->assertNotFound();
        $this->get('/fasilitas/create')->assertNotFound();
        $this->post('/fasilitas', [])->assertNotFound();
        $this->get(route('gedung.create'))->assertOk()->assertDontSee('Manajemen Fasilitas')->assertSee('Tambah Fasilitas')->assertSee('Pilih semua');
    }

    public function test_modal_adds_a_reusable_database_choice_and_normalizes_duplicate_names(): void
    {
        $this->actingAs($this->user());
        $created = $this->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => '  Dispenser   Air  ', 'lingkup' => 'gedung'])->assertCreated()->assertJsonPath('name', 'Dispenser Air');
        $id = $created->json('id');
        $this->assertDatabaseHas('fasilitas', ['id_fasilitas' => $id, 'id_gedung' => null, 'id_kamar' => null, 'id_fasilitas_acuan' => null]);
        $this->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => 'dispenser air', 'lingkup' => 'gedung'])->assertOk()->assertJsonPath('id', $id);
        $this->assertDatabaseCount('fasilitas', 1);
        $building = $this->building();
        $room = $this->room($building);
        foreach ([route('gedung.create'), route('gedung.edit', $building)] as $url) {
            $this->get($url)->assertOk()->assertSee('Dispenser Air')->assertSee('data-facility-selector', false)->assertSee('data-facility-dialog', false)->assertSee('Pilih semua');
        }
        $this->get(route('kamar.create'))->assertOk()->assertViewHas('facilityOptions', fn ($items) => $items->isEmpty());
        $this->get(route('kamar.edit', $room))->assertOk()->assertViewHas('facilityOptions', fn ($items) => $items->isEmpty());
        $roomChoice = $this->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => 'Dispenser Air', 'lingkup' => 'kamar'])->assertCreated()->json('id');
        $this->assertNotSame($id, $roomChoice);
        $this->get(route('kamar.edit', $room))->assertOk()->assertViewHas('facilityOptions', fn ($items) => $items->pluck('id_fasilitas')->all() === [$roomChoice]);
        $this->assertSame(1, Fasilitas::pilihan('gedung')->count());
        $this->assertSame(1, Fasilitas::pilihan('kamar')->count());
    }

    public function test_only_facilitators_can_add_choices_and_invalid_names_do_not_save(): void
    {
        $this->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => 'Kipas'])->assertUnauthorized();
        foreach (['Client', 'Admin Layanan', 'Teknisi', 'Pimpinan', 'Guest Officer'] as $role) {
            $this->actingAs($this->user($role))->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => 'Kipas'])->assertForbidden();
        }
        $this->actingAs($this->user());
        foreach (['   ', str_repeat('x', 101), ['nama']] as $name) {
            $this->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => $name, 'lingkup' => 'gedung'])->assertUnprocessable()->assertJsonValidationErrors('nama_fasilitas');
        }
        $this->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => 'Kipas'])->assertUnprocessable()->assertJsonValidationErrors('lingkup');
        $this->assertDatabaseCount('fasilitas', 0);
    }

    public function test_only_checked_choices_are_related_to_new_buildings_and_rooms(): void
    {
        $first = Fasilitas::create(['nama_fasilitas' => 'Meja', 'lingkup' => 'gedung']);
        $roomFirst = Fasilitas::create(['nama_fasilitas' => 'Meja', 'lingkup' => 'kamar']);
        $second = Fasilitas::create(['nama_fasilitas' => 'Kursi', 'lingkup' => 'kamar']);
        $unselected = Fasilitas::create(['nama_fasilitas' => 'Dispenser', 'lingkup' => 'kamar']);
        $this->actingAs($this->user())->post(route('gedung.store'), $this->buildingFields([$first->id_fasilitas]))->assertSessionHasNoErrors();
        $building = Gedung::sole();
        $shared = $building->fasilitas()->sole();
        $this->assertSame($first->id_fasilitas, $shared->id_fasilitas_acuan);
        $this->assertNull($shared->id_kamar);
        $this->post(route('kamar.store'), $this->roomFields($building, [$roomFirst->id_fasilitas, $second->id_fasilitas]))->assertSessionHasNoErrors();
        $room = Kamar::sole();
        $this->assertEqualsCanonicalizing([$roomFirst->id_fasilitas, $second->id_fasilitas], $room->fasilitas()->pluck('id_fasilitas_acuan')->all());
        $this->assertSame(['Baik'], $room->fasilitas()->pluck('kondisi')->unique()->all());
        $this->assertDatabaseMissing('fasilitas', ['id_kamar' => $room->id_kamar, 'id_fasilitas_acuan' => $unselected->id_fasilitas]);
        $this->assertNull($first->fresh()->id_kamar);
        $this->assertNull($first->fresh()->id_gedung);
        $this->get(route('kamar.edit', $room))->assertViewHas('selectedFacilityIds', fn ($ids) => count($ids) === 2);
        $this->get(route('gedung.edit', $building))->assertViewHas('selectedFacilityIds', fn ($ids) => $ids === [$first->id_fasilitas]);
    }

    public function test_inventory_ids_cannot_be_reused_as_choices_or_moved_from_another_room(): void
    {
        $building = $this->building();
        $other = $this->room($building);
        $inventory = Fasilitas::create(['nama_fasilitas' => 'Meja kamar lain', 'id_kamar' => $other->id_kamar, 'id_gedung' => $building->id_gedung]);
        $this->actingAs($this->user())->post(route('kamar.store'), $this->roomFields($building, [$inventory->id_fasilitas]))->assertSessionHasErrors('fasilitas.0');
        $this->post(route('gedung.store'), $this->buildingFields([999999]))->assertSessionHasErrors('fasilitas.0');
        $buildingChoice = Fasilitas::create(['nama_fasilitas' => 'CCTV', 'lingkup' => 'gedung']);
        $roomChoice = Fasilitas::create(['nama_fasilitas' => 'Kasur', 'lingkup' => 'kamar']);
        $this->post(route('kamar.store'), $this->roomFields($building, [$buildingChoice->id_fasilitas]))->assertSessionHasErrors('fasilitas.0');
        $this->post(route('gedung.store'), $this->buildingFields([$roomChoice->id_fasilitas]))->assertSessionHasErrors('fasilitas.0');
        $this->assertDatabaseCount('kamar', 1);
        $this->assertDatabaseCount('gedung', 1);
        $this->assertSame($other->id_kamar, $inventory->fresh()->id_kamar);
    }

    public function test_unchecking_and_reselecting_keeps_report_foreign_keys_and_original_condition(): void
    {
        $building = $this->building();
        $room = $this->room($building);
        $choice = Fasilitas::create(['nama_fasilitas' => 'Lemari', 'lingkup' => 'kamar']);
        $inventory = Fasilitas::create(['id_fasilitas_acuan' => $choice->id_fasilitas, 'id_kamar' => $room->id_kamar, 'id_gedung' => $building->id_gedung, 'nama_fasilitas' => 'Lemari', 'kondisi' => 'Rusak Ringan']);
        $user = $this->user();
        $report = LaporanKerusakan::create(['id_pengguna' => $user->id_pengguna, 'id_fasilitas' => $inventory->id_fasilitas, 'deskripsi' => 'Pintu lemari rusak dan perlu diperbaiki.', 'tanggal_lapor' => today(), 'status_perbaikan' => 'diajukan']);
        $this->actingAs($user)->put(route('kamar.update', $room), $this->roomFields($building, []))->assertSessionHasNoErrors();
        $this->assertSame(0, $room->fasilitas()->count());
        $this->assertFalse($inventory->fresh()->aktif);
        $this->assertSame($inventory->id_fasilitas, $report->fresh()->id_fasilitas);
        $this->put(route('kamar.update', $room), $this->roomFields($building, [$choice->id_fasilitas]))->assertSessionHasNoErrors();
        $this->assertSame($inventory->id_fasilitas, $room->fasilitas()->sole()->id_fasilitas);
        $this->assertSame('Rusak Ringan', $inventory->fresh()->kondisi);
        $this->put(route('kamar.update', $room), $this->roomFields($building, [$choice->id_fasilitas]))->assertSessionHasNoErrors();
        $this->assertSame(1, $room->fasilitas()->count());
        $this->delete(route('kamar.destroy', $room))->assertSessionHas('error');
        $this->assertDatabaseHas('kamar', ['id_kamar' => $room->id_kamar]);
        $this->assertDatabaseHas('laporan_kerusakan', ['id_laporan' => $report->id_laporan, 'id_fasilitas' => $inventory->id_fasilitas]);
    }

    public function test_unused_inventory_is_removed_with_its_room_or_building_while_choices_remain(): void
    {
        $choice = Fasilitas::create(['nama_fasilitas' => 'Meja', 'lingkup' => 'gedung']);
        $roomChoice = Fasilitas::create(['nama_fasilitas' => 'Meja', 'lingkup' => 'kamar']);
        $this->actingAs($this->user())->post(route('gedung.store'), $this->buildingFields([$choice->id_fasilitas]))->assertSessionHasNoErrors();
        $building = Gedung::sole();
        $this->post(route('kamar.store'), $this->roomFields($building, [$roomChoice->id_fasilitas]))->assertSessionHasNoErrors();
        $room = Kamar::sole();
        $this->delete(route('gedung.destroy', $building))->assertSessionHas('error');
        $this->delete(route('kamar.destroy', $room))->assertSessionHas('success');
        $this->assertDatabaseMissing('fasilitas', ['id_kamar' => $room->id_kamar]);
        $this->delete(route('gedung.destroy', $building))->assertSessionHas('success');
        $this->assertDatabaseCount('gedung', 0);
        $this->assertDatabaseCount('kamar', 0);
        $this->assertEqualsCanonicalizing([$choice->id_fasilitas, $roomChoice->id_fasilitas], Fasilitas::pluck('id_fasilitas')->all());
    }

    public function test_changing_shared_facilities_does_not_modify_room_facilities(): void
    {
        $building = $this->building();
        $room = $this->room($building);
        $choice = Fasilitas::create(['nama_fasilitas' => 'Meja', 'lingkup' => 'gedung']);
        $roomFacility = Fasilitas::create(['id_kamar' => $room->id_kamar, 'id_gedung' => $building->id_gedung, 'nama_fasilitas' => 'Meja', 'kondisi' => 'Baik']);
        $this->actingAs($this->user())->put(route('gedung.update', $building), $this->buildingFields([$choice->id_fasilitas]))->assertSessionHasNoErrors();
        $this->put(route('gedung.update', $building), $this->buildingFields([]))->assertSessionHasNoErrors();
        $this->assertTrue($roomFacility->fresh()->aktif);
        $this->assertSame($room->id_kamar, $roomFacility->fresh()->id_kamar);
        $this->assertSame(0, $building->fasilitas()->whereNull('id_kamar')->count());
    }

    public function test_failed_facility_save_rolls_back_the_building_change(): void
    {
        $building = $this->building();
        $choice = Fasilitas::create(['nama_fasilitas' => 'AC', 'lingkup' => 'gedung']);
        Fasilitas::saving(function ($facility) {
            if ($facility->id_gedung) throw new RuntimeException('Facility write failed');
        });
        $this->actingAs($this->user())->withoutExceptionHandling();
        try {
            $this->put(route('gedung.update', $building), $this->buildingFields([$choice->id_fasilitas]));
            $this->fail('Expected failed facility write.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Facility write failed', $exception->getMessage());
        } finally {
            Fasilitas::flushEventListeners();
        }
        $this->assertSame('Gedung Fasilitas', $building->fresh()->nama_gedung);
        $this->assertSame(0, $building->fasilitas()->count());
    }

    public function test_choice_seeding_is_repeatable_and_preserves_existing_inventory_ids_and_conditions(): void
    {
        $building = $this->building();
        $room = $this->room($building);
        $inventory = Fasilitas::create(['id_kamar' => $room->id_kamar, 'id_gedung' => $building->id_gedung, 'nama_fasilitas' => 'Kamar Mandi Dalam', 'kondisi' => 'Rusak Berat']);
        $this->seed(PilihanFasilitasSeeder::class);
        $count = Fasilitas::count();
        $this->seed(PilihanFasilitasSeeder::class);
        $this->assertSame($count, Fasilitas::count());
        $this->assertSame('Rusak Berat', $inventory->fresh()->kondisi);
        $this->assertSame('Kamar Mandi Dalam', $inventory->fresh()->acuan->nama_fasilitas);
        $this->assertSame('kamar', $inventory->fresh()->acuan->lingkup);
        $this->assertSame($room->id_kamar, $inventory->fresh()->id_kamar);
    }

    public function test_forms_start_with_seven_building_and_eight_room_choices_and_keep_custom_additions_separate(): void
    {
        $building = $this->building();
        $room = $this->room($building);
        $legacy = Fasilitas::create(['nama_fasilitas' => 'Fasilitas khusus lama', 'id_kamar' => $room->id_kamar, 'id_gedung' => $building->id_gedung, 'kondisi' => 'Rusak Berat']);
        $hiddenChoice = Fasilitas::create(['nama_fasilitas' => 'Nama inventaris lama']);
        $this->seed(PilihanFasilitasSeeder::class);
        $this->actingAs($this->user());
        foreach ([route('gedung.create'), route('gedung.edit', $building)] as $url) {
            $this->get($url)->assertOk()->assertViewHas('facilityOptions', fn ($items) => $items->count() === 7)
                ->assertSee('Dapur / Pantry')->assertDontSee('AC Pendingin Ruangan')->assertDontSee('Nama inventaris lama');
        }
        foreach ([route('kamar.create'), route('kamar.edit', $room)] as $url) {
            $this->get($url)->assertOk()->assertViewHas('facilityOptions', fn ($items) => $items->count() === 8)
                ->assertSee('AC Pendingin Ruangan')->assertDontSee('Dapur / Pantry')->assertDontSee('Fasilitas khusus lama');
        }
        $this->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => 'Dispenser khusus', 'lingkup' => 'gedung'])->assertCreated();
        $this->get(route('gedung.create'))->assertViewHas('facilityOptions', fn ($items) => $items->count() === 8)->assertSee('Dispenser khusus');
        $this->get(route('kamar.create'))->assertViewHas('facilityOptions', fn ($items) => $items->count() === 8)->assertDontSee('Dispenser khusus');
        $this->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => 'Nama inventaris lama', 'lingkup' => 'kamar'])->assertCreated()->assertJsonPath('id', $hiddenChoice->id_fasilitas);
        $this->get(route('kamar.create'))->assertViewHas('facilityOptions', fn ($items) => $items->count() === 9)->assertSee('Nama inventaris lama');
        $this->get(route('gedung.create'))->assertViewHas('facilityOptions', fn ($items) => $items->count() === 8)->assertDontSee('Nama inventaris lama');
        $this->put(route('kamar.update', $room), $this->roomFields($building, []))->assertSessionHasNoErrors();
        $this->assertTrue($legacy->fresh()->aktif);
        $this->assertSame('Rusak Berat', $legacy->fresh()->kondisi);
        $this->assertSame($room->id_kamar, $legacy->fresh()->id_kamar);
    }
}
