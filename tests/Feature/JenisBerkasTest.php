<?php

namespace Tests\Feature;

use App\Models\BerkasPendaftaran;
use App\Models\Client;
use App\Models\JenisBerkas;
use App\Models\JenisClient;
use App\Models\PendaftaranHunian;
use App\Models\Pengguna;
use App\Models\PeriodePendaftaran;
use App\Models\Role;
use Database\Seeders\JenisBerkasSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class JenisBerkasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->seed(JenisBerkasSeeder::class);
    }

    private function context(): array
    {
        $user = Pengguna::create([
            'id_role' => Role::firstOrCreate(['nama_role' => 'Client'])->id_role,
            'nama_lengkap' => 'Client Berkas', 'email' => 'berkas@example.com',
            'username' => 'client_berkas', 'password' => 'Password123!', 'status_aktif' => 1,
        ]);
        $kind = JenisClient::firstOrCreate(['jenis' => JenisClient::CATEGORIES['kipk']]);
        $client = Client::create(['id_pengguna' => $user->id_pengguna, 'id_jenis_client' => $kind->id_jenis_client, 'status_KIPK' => 1, 'alamat_asal' => 'Alamat awal']);
        $period = PeriodePendaftaran::create(['nama_periode' => 'Periode Uji Berkas', 'tahun_akademik' => '2026/2027', 'tanggal_buka' => today()->subDay(), 'tanggal_tutup' => today()->addDay(), 'status' => 'dibuka']);

        return compact('user', 'client', 'period');
    }

    private function upload(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('dokumen.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jB1sAAAAASUVORK5CYII='));
    }

    private function payload(PeriodePendaftaran $period): array
    {
        return [
            'id_periode' => $period->id_periode, 'nomor_identitas' => '1234567890123456',
            'jenis_kelamin' => 'laki-laki', 'alamat_asal' => 'Alamat diperbarui', 'persetujuan' => '1',
            'berkas' => [$this->upload()], 'id_jenis_berkas' => [JenisBerkas::orderBy('id_jenis_berkas')->value('id_jenis_berkas')],
        ];
    }

    private function registration(array $context): PendaftaranHunian
    {
        return PendaftaranHunian::create(['id_client' => $context['client']->id_client, 'id_periode' => $context['period']->id_periode, 'tanggal_daftar' => today(), 'status_pendaftaran' => 'menunggu_verifikasi']);
    }

    public function test_seeder_creates_the_three_requested_types_and_can_be_run_again_without_duplicates(): void
    {
        $before = JenisBerkas::orderBy('id_jenis_berkas')->pluck('nama_berkas', 'id_jenis_berkas')->all();
        $this->assertSame(['Fotocopy Kartu Keluarga', 'Fotocopy KTP', 'Hasil Tes Kesehatan'], array_values($before));
        $this->seed(JenisBerkasSeeder::class);
        $this->assertSame($before, JenisBerkas::orderBy('id_jenis_berkas')->pluck('nama_berkas', 'id_jenis_berkas')->all());
        $this->assertDatabaseCount('jenis_berkas', 3);
    }

    public function test_form_uses_types_from_the_database(): void
    {
        $context = $this->context();
        JenisBerkas::create(['nama_berkas' => 'Dokumen Tambahan']);
        $this->actingAs($context['user'])->get(route('pendaftaran.create'))->assertOk()
            ->assertSee('Fotocopy Kartu Keluarga')->assertSee('Fotocopy KTP')->assertSee('Hasil Tes Kesehatan')
            ->assertSee('Dokumen Tambahan')->assertViewHas('jenisBerkas', fn ($types) => $types->count() === 4);
    }

    public function test_uploaded_files_are_linked_to_the_selected_types_and_registration(): void
    {
        $context = $this->context();
        $ids = JenisBerkas::orderBy('id_jenis_berkas')->pluck('id_jenis_berkas')->all();
        $payload = array_replace($this->payload($context['period']), ['berkas' => [$this->upload(), $this->upload(), $this->upload()], 'id_jenis_berkas' => $ids]);
        $this->actingAs($context['user'])->post(route('pendaftaran.store'), $payload)
            ->assertSessionHasNoErrors()->assertRedirect(route('pendaftaran.create'));

        $registration = PendaftaranHunian::sole();
        $documents = $registration->berkasPendaftarans()->with('jenisBerkas')->orderBy('id_berkas')->get();
        $this->assertSame($ids, $documents->pluck('id_jenis_berkas')->all());
        $this->assertCount(3, $documents);
        foreach ($documents as $document) {
            Storage::disk('local')->assertExists($document->path_file);
            $this->assertTrue($document->pendaftaranHunian->is($registration));
            $this->assertTrue($document->jenisBerkas->berkasPendaftarans->contains('id_berkas', $document->id_berkas));
        }
        $this->assertSame('Alamat diperbarui', $context['client']->fresh()->alamat_asal);
    }

    public function test_missing_unknown_or_unmatched_file_types_do_not_save_files_or_registration(): void
    {
        $context = $this->context();
        $this->actingAs($context['user']);
        $missing = $this->payload($context['period']);
        unset($missing['id_jenis_berkas']);
        $this->post(route('pendaftaran.store'), $missing)->assertSessionHasErrors('id_jenis_berkas');
        $unknown = array_replace($this->payload($context['period']), ['id_jenis_berkas' => [99999]]);
        $this->post(route('pendaftaran.store'), $unknown)->assertSessionHasErrors('id_jenis_berkas.0');
        $unmatched = array_replace($this->payload($context['period']), ['berkas' => [$this->upload(), $this->upload()]]);
        $this->post(route('pendaftaran.store'), $unmatched)->assertSessionHasErrors('id_jenis_berkas');
        $this->assertDatabaseCount('pendaftaran_hunian', 0);
        $this->assertDatabaseCount('berkas_pendaftaran', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
        $this->assertSame('Alamat awal', $context['client']->fresh()->alamat_asal);
    }

    public function test_database_rejects_a_nonexistent_document_type(): void
    {
        $registration = $this->registration($this->context());
        try {
            BerkasPendaftaran::create(['id_pendaftaran' => $registration->id_pendaftaran, 'id_jenis_berkas' => 99999, 'path_file' => 'pendaftaran/uji.png', 'tanggal_upload' => today()]);
            $this->fail('A non-null type must reference an existing file type.');
        } catch (QueryException $exception) {
            $this->assertDatabaseCount('berkas_pendaftaran', 0);
        }
    }

    public function test_new_uploads_still_require_a_document_type(): void
    {
        $context = $this->context();
        $data = $this->payload($context['period']);
        $data['id_jenis_berkas'] = [null];
        $this->actingAs($context['user'])->post(route('pendaftaran.store'), $data)->assertSessionHasErrors('id_jenis_berkas.0');
        $this->assertDatabaseCount('pendaftaran_hunian', 0);
        $this->assertDatabaseCount('berkas_pendaftaran', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_original_type_migration_preserves_existing_unclassified_documents_and_their_files(): void
    {
        $registration = $this->registration($this->context());
        $migration = require database_path('migrations/2026_11_20_000002_create_jenis_berkas_and_link_berkas_pendaftaran.php');
        $migration->down();
        $this->assertFalse(Schema::hasColumn('berkas_pendaftaran', 'id_jenis_berkas'));
        foreach (['kk-lama.png', 'ktp-lama.png', 'kesehatan-lama.png'] as $name) {
            $path = 'pendaftaran/'.$name;
            Storage::disk('local')->put($path, 'Isi asli '.$name);
            DB::table('berkas_pendaftaran')->insert(['id_pendaftaran' => $registration->id_pendaftaran, 'path_file' => $path, 'tanggal_upload' => '2026-11-01']);
        }
        $before = DB::table('berkas_pendaftaran')->orderBy('id_berkas')->get();
        $migration->up();
        $this->seed(JenisBerkasSeeder::class);
        $this->assertDatabaseCount('jenis_berkas', 3);
        $this->assertDatabaseCount('berkas_pendaftaran', 3);
        foreach ($before as $original) {
            $after = DB::table('berkas_pendaftaran')->where('id_berkas', $original->id_berkas)->first();
            $this->assertSame((array) $original, \Illuminate\Support\Arr::except((array) $after, ['id_jenis_berkas']));
            $this->assertNull($after->id_jenis_berkas);
            $this->assertSame('Isi asli '.basename($original->path_file), Storage::disk('local')->get($original->path_file));
        }
    }

    public function test_followup_migration_handles_installations_with_the_original_required_column(): void
    {
        $registration = $this->registration($this->context());
        $document = $registration->berkasPendaftarans()->create(['id_jenis_berkas' => JenisBerkas::firstOrFail()->id_jenis_berkas, 'path_file' => 'pendaftaran/terklasifikasi.png', 'tanggal_upload' => today()]);
        $before = $document->fresh()->toArray();
        Schema::table('berkas_pendaftaran', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->unsignedInteger('id_jenis_berkas')->nullable(false)->change();
        });
        $migration = require database_path('migrations/2026_11_20_000008_allow_unclassified_legacy_registration_documents.php');
        $migration->up();
        $legacy = $registration->berkasPendaftarans()->create(['id_jenis_berkas' => null, 'path_file' => 'pendaftaran/berkas-lama.png', 'tanggal_upload' => today()]);
        $this->assertNull($legacy->fresh()->id_jenis_berkas);
        $this->assertSame($before, $document->fresh()->toArray());
        $migration->down();
        $this->assertDatabaseHas('berkas_pendaftaran', ['id_berkas' => $legacy->id_berkas, 'id_jenis_berkas' => null]);
    }

    public function test_a_type_used_by_a_document_cannot_be_deleted(): void
    {
        $registration = $this->registration($this->context());
        $type = JenisBerkas::firstOrFail();
        $document = BerkasPendaftaran::create(['id_pendaftaran' => $registration->id_pendaftaran, 'id_jenis_berkas' => $type->id_jenis_berkas, 'path_file' => 'pendaftaran/uji.png', 'tanggal_upload' => today()]);
        try {
            $type->delete();
            $this->fail('Used file types must be protected from deletion.');
        } catch (QueryException $exception) {
            $this->assertDatabaseHas('jenis_berkas', ['id_jenis_berkas' => $type->id_jenis_berkas]);
            $this->assertDatabaseHas('berkas_pendaftaran', ['id_berkas' => $document->id_berkas]);
        }
    }
}
