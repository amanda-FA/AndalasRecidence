<?php

namespace Tests\Feature;

use App\Models\Gedung;
use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class GedungPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function user(string $role = 'Fasilitator'): Pengguna
    {
        return Pengguna::create([
            'id_role' => Role::firstOrCreate(['nama_role' => $role])->id_role,
            'nama_lengkap' => $role, 'email' => $role.'@example.com',
            'username' => $role, 'password' => 'Password123!', 'status_aktif' => 1,
        ]);
    }

    private function fields(): array
    {
        return ['nama' => 'Gedung Foto', 'peruntukan' => 'putra', 'jumlah_lantai' => 2, 'deskripsi' => 'Hunian kampus'];
    }

    private function photo(string $name = 'gedung.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jB1sAAAAASUVORK5CYII='));
    }

    public function test_facilitator_can_upload_and_view_a_building_photo_in_management_and_public_pages(): void
    {
        $this->actingAs($this->user())->get(route('gedung.create'))->assertOk()
            ->assertSee('name="foto"', false)->assertSee('multipart/form-data');
        $this->post(route('gedung.store'), $this->fields() + ['foto' => $this->photo()])
            ->assertRedirect(route('gedung.index'))->assertSessionHasNoErrors();

        $building = Gedung::sole();
        $this->assertStringStartsWith('gedung/', $building->foto);
        Storage::disk('public')->assertExists($building->foto);
        $this->assertDatabaseHas('gedung', ['id_gedung' => $building->id_gedung, 'foto' => $building->foto]);
        $this->get(route('gedung.index'))->assertOk()->assertSee($building->foto_url, false);
        $this->get(route('gedung.edit', $building))->assertOk()->assertSee($building->foto_url, false)
            ->assertSee('Gambar saat ini tetap digunakan');
        $this->app['auth']->forgetGuards();
        $this->get('/')->assertOk()->assertSee($building->foto_url, false);
        $this->get(route('hunian.index'))->assertOk()->assertSee($building->foto_url, false);
        $this->get(route('hunian.show', $building))->assertOk()->assertSee($building->foto_url, false);
        $this->assertGuest();
    }

    public function test_edit_without_upload_preserves_the_photo_and_a_new_upload_replaces_the_file(): void
    {
        $this->actingAs($this->user())->post(route('gedung.store'), $this->fields() + ['foto' => $this->photo()]);
        $building = Gedung::sole();
        $oldPhoto = $building->foto;

        $this->put(route('gedung.update', $building), array_replace($this->fields(), ['nama' => 'Gedung Diperbarui']))
            ->assertSessionHasNoErrors()->assertRedirect(route('gedung.index'));
        $this->assertSame($oldPhoto, $building->fresh()->foto);
        Storage::disk('public')->assertExists($oldPhoto);

        $this->put(route('gedung.update', $building), $this->fields() + ['foto' => $this->photo('baru.png')])
            ->assertSessionHasNoErrors()->assertRedirect(route('gedung.index'));
        $newPhoto = $building->fresh()->foto;
        $this->assertNotSame($oldPhoto, $newPhoto);
        Storage::disk('public')->assertExists($newPhoto);
        Storage::disk('public')->assertMissing($oldPhoto);
        $this->assertCount(1, Storage::disk('public')->allFiles('gedung'));
    }

    public function test_photo_links_use_the_current_host_and_port(): void
    {
        config(['filesystems.disks.public.url' => 'http://localhost/storage']);
        $building = Gedung::create(['nama_gedung' => 'Gedung Port Lokal', 'jenis_penghuni' => 'putra', 'foto' => 'gedung/foto.png']);
        $this->get('http://localhost:8123/hunian/'.$building->id_gedung)->assertOk()
            ->assertSee('http://localhost:8123/storage/gedung/foto.png', false)
            ->assertDontSee('http://localhost/storage/gedung/foto.png', false);
    }

    public function test_photo_is_optional_for_new_and_existing_buildings(): void
    {
        $this->actingAs($this->user())->post(route('gedung.store'), $this->fields())->assertSessionHasNoErrors();
        $building = Gedung::sole();
        $this->assertNull($building->foto);
        $this->assertNull($building->foto_url);
        $this->get(route('gedung.edit', $building))->assertOk();
        $this->put(route('gedung.update', $building), $this->fields())->assertSessionHasNoErrors();
        $this->get('/')->assertOk()->assertSee('residence-building-drawing')->assertDontSee('residence-building-photo');
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_invalid_and_oversized_photos_do_not_create_or_modify_a_building(): void
    {
        $this->actingAs($this->user());
        foreach ([
            UploadedFile::fake()->create('dokumen.txt', 1, 'text/plain'),
            UploadedFile::fake()->createWithContent('gambar.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
            $this->photo()->size(2049),
        ] as $file) {
            $this->post(route('gedung.store'), $this->fields() + ['foto' => $file])->assertSessionHasErrors('foto');
        }
        $this->assertDatabaseCount('gedung', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());

        $this->post(route('gedung.store'), $this->fields() + ['foto' => $this->photo()])->assertSessionHasNoErrors();
        $building = Gedung::sole();
        $oldPhoto = $building->foto;
        $this->put(route('gedung.update', $building), array_replace($this->fields(), ['nama' => 'Tidak disimpan', 'foto' => UploadedFile::fake()->create('bukan-gambar.txt', 1)]))
            ->assertSessionHasErrors('foto');
        $this->assertSame('Gedung Foto', $building->fresh()->nama_gedung);
        $this->assertSame($oldPhoto, $building->fresh()->foto);
        Storage::disk('public')->assertExists($oldPhoto);
    }

    public function test_deleting_a_building_also_removes_its_uploaded_photo(): void
    {
        $this->actingAs($this->user())->post(route('gedung.store'), $this->fields() + ['foto' => $this->photo()]);
        $building = Gedung::sole();
        $photo = $building->foto;
        $this->delete(route('gedung.destroy', $building))->assertRedirect(route('gedung.index'));
        $this->assertDatabaseMissing('gedung', ['id_gedung' => $building->id_gedung]);
        Storage::disk('public')->assertMissing($photo);
    }

    public function test_failed_database_save_preserves_the_previous_photo_and_cleans_up_the_new_upload(): void
    {
        $this->actingAs($this->user())->post(route('gedung.store'), $this->fields() + ['foto' => $this->photo()]);
        $building = Gedung::sole();
        $oldPhoto = $building->foto;
        Gedung::saving(function () {
            throw new RuntimeException('Simulated database save failure');
        });
        $this->withoutExceptionHandling();
        try {
            $this->put(route('gedung.update', $building), $this->fields() + ['foto' => $this->photo('pengganti.png')]);
            $this->fail('The failed save should raise an exception.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated database save failure', $exception->getMessage());
        }
        $this->assertSame($oldPhoto, $building->fresh()->foto);
        Storage::disk('public')->assertExists($oldPhoto);
        $this->assertSame([$oldPhoto], Storage::disk('public')->allFiles('gedung'));
    }

    public function test_only_facilitators_can_save_building_photos(): void
    {
        $this->post(route('gedung.store'), $this->fields() + ['foto' => $this->photo()])->assertRedirect(route('login'));
        $building = Gedung::create(['nama_gedung' => 'Gedung Tetap', 'jenis_penghuni' => 'putra', 'jumlah_lantai' => 1]);
        foreach (['Client', 'AdminLayanan', 'Teknisi'] as $role) {
            $this->actingAs($this->user($role))->post(route('gedung.store'), $this->fields() + ['foto' => $this->photo()])->assertForbidden();
            $this->put(route('gedung.update', $building), $this->fields() + ['foto' => $this->photo()])->assertForbidden();
        }
        $this->assertDatabaseCount('gedung', 1);
        $this->assertNull($building->fresh()->foto);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }
}
