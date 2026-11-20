<?php

namespace Tests\Feature;

use App\Models\LaporanKerusakan;
use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientHousingDatabaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-11-19 10:00:00', 'Asia/Jakarta'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function user(string $name, string $role = 'Client'): Pengguna
    {
        return Pengguna::create([
            'id_role' => Role::firstOrCreate(['nama_role' => $role])->id_role,
            'nama_lengkap' => $name, 'email' => $name.'@example.com', 'username' => $name,
            'password' => 'Password123!', 'status_aktif' => 1,
        ]);
    }

    private function housing(): array
    {
        $owner = $this->user('Fadhil');
        $other = $this->user('Other');
        $kind = DB::table('jenis_client')->insertGetId(['jenis' => \App\Models\JenisClient::CATEGORIES['reguler']]);
        $client = DB::table('client')->insertGetId(['id_pengguna' => $owner->id_pengguna, 'id_jenis_client' => $kind]);
        $period = DB::table('periode_pendaftaran')->insertGetId(['nama_periode' => 'Periode Database', 'tahun_akademik' => '2026/2027', 'tanggal_buka' => '2026-11-01', 'tanggal_tutup' => '2026-11-30', 'status' => 'dibuka']);
        $type = DB::table('tipe_kamar')->insertGetId(['nama_tipe' => 'Standar database', 'kapasitas' => 4, 'tarif' => 1000000]);
        $building = DB::table('gedung')->insertGetId(['nama_gedung' => 'Menpera Database', 'jenis_penghuni' => 'putra', 'jumlah_lantai' => 2]);
        $room = DB::table('kamar')->insertGetId(['id_gedung' => $building, 'id_tipeKamar' => $type, 'nomor_kamar' => '1A.01', 'lantai' => 1, 'status_kamar' => 'tersedia']);
        $oldRoom = DB::table('kamar')->insertGetId(['id_gedung' => $building, 'id_tipeKamar' => $type, 'nomor_kamar' => '2A.01', 'lantai' => 2, 'status_kamar' => 'tersedia']);
        $neighbor = DB::table('kamar')->insertGetId(['id_gedung' => $building, 'id_tipeKamar' => $type, 'nomor_kamar' => 'RAHASIA-OTHER', 'lantai' => 1, 'status_kamar' => 'tersedia']);
        $registration = DB::table('pendaftaran_hunian')->insertGetId(['id_client' => $client, 'id_periode' => $period, 'id_tipeKamar' => $type, 'tanggal_daftar' => '2026-11-01', 'status_pendaftaran' => 'terverifikasi']);
        $placement = DB::table('penempatan_kamar')->insertGetId(['id_pendaftaran' => $registration, 'id_kamar' => $room, 'tanggal_masuk' => '2026-11-19', 'tanggal_keluar' => null, 'status_penempatan' => 'aktif']);
        DB::table('penempatan_kamar')->insert(['id_pendaftaran' => $registration, 'id_kamar' => $oldRoom, 'tanggal_masuk' => '2026-11-02', 'tanggal_keluar' => '2026-11-18', 'status_penempatan' => 'dipindahkan']);
        $otherClient = DB::table('client')->insertGetId(['id_pengguna' => $other->id_pengguna, 'id_jenis_client' => $kind]);
        $otherRegistration = DB::table('pendaftaran_hunian')->insertGetId(['id_client' => $otherClient, 'id_periode' => $period, 'id_tipeKamar' => $type, 'tanggal_daftar' => '2026-11-01', 'status_pendaftaran' => 'terverifikasi']);
        DB::table('penempatan_kamar')->insert(['id_pendaftaran' => $otherRegistration, 'id_kamar' => $neighbor, 'tanggal_masuk' => '2026-11-19', 'status_penempatan' => 'aktif']);
        $facility = DB::table('fasilitas')->insertGetId(['id_kamar' => $room, 'id_gedung' => $building, 'nama_fasilitas' => 'Lampu database', 'kondisi' => 'Rusak Ringan']);
        $shared = DB::table('fasilitas')->insertGetId(['id_kamar' => null, 'id_gedung' => $building, 'nama_fasilitas' => 'Fasilitas bersama database']);
        $forbidden = DB::table('fasilitas')->insertGetId(['id_kamar' => $neighbor, 'id_gedung' => $building, 'nama_fasilitas' => 'Fasilitas rahasia tetangga']);

        return compact('owner', 'other', 'facility', 'shared', 'forbidden', 'placement', 'room');
    }

    public function test_pages_read_only_the_logged_in_clients_database_records(): void
    {
        $data = $this->housing();
        $technician = $this->user('Teknisi', 'Teknisi');
        $report = LaporanKerusakan::create(['id_pengguna' => $data['owner']->id_pengguna, 'id_fasilitas' => $data['facility'], 'deskripsi' => 'Laporan database milik Fadhil', 'tanggal_lapor' => '2026-11-19', 'status_perbaikan' => 'dalam_pengerjaan']);
        DB::table('penugasan_perbaikan')->insert(['id_laporan' => $report->id_laporan, 'id_pengguna' => $technician->id_pengguna]);
        LaporanKerusakan::create(['id_pengguna' => $data['other']->id_pengguna, 'id_fasilitas' => $data['forbidden'], 'deskripsi' => 'RAHASIA LAPORAN PENGHUNI LAIN', 'tanggal_lapor' => '2026-11-19', 'status_perbaikan' => 'diajukan']);

        $placement = $this->actingAs($data['owner'])->get(route('penempatan.index'))->assertOk()
            ->assertSee('1A.01')->assertSee('2A.01')->assertSee('Menpera Database')->assertSee('Standar database')->assertDontSee('RAHASIA-OTHER')->assertDontSee('3A.13');
        $this->assertCount(2, $placement->viewData('riwayat'));
        $this->assertSame($data['room'], $placement->viewData('aktif')['id_kamar']);
        $damage = $this->get(route('kerusakan.index'))->assertOk()->assertSee('Laporan database milik Fadhil')->assertDontSee('RAHASIA LAPORAN PENGHUNI LAIN')->assertDontSee('Fasilitas rahasia tetangga');
        $reports = $damage->viewData('reports');
        $this->assertCount(1, $reports);
        $this->assertSame('Teknisi', $reports[0]['teknisi']);
        $this->assertSame([], $reports[0]['photos']);
        $this->assertNull($reports[0]['tanggal_pengerjaan']);
        $this->assertTrue($damage->viewData('facilities')[$data['shared']]['available']);
    }

    public function test_new_report_is_persisted_and_photo_is_accessible_only_to_its_owner(): void
    {
        Storage::fake('local');
        $data = $this->housing();
        $photo = UploadedFile::fake()->createWithContent('kondisi.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jB1sAAAAASUVORK5CYII='));
        $this->actingAs($data['owner'])->post(route('kerusakan.store'), [
            'jenis_fasilitas' => 'kamar', 'id_fasilitas' => $data['facility'], 'deskripsi' => 'Lampu tidak menyala sejak pagi.', 'foto' => $photo,
            'id_pengguna' => $data['other']->id_pengguna, 'status_perbaikan' => 'selesai', 'tanggal_selesai' => '2026-11-19',
        ])->assertRedirect(route('kerusakan.index'))->assertSessionHasNoErrors();
        $report = LaporanKerusakan::sole();
        $this->assertSame($data['owner']->id_pengguna, $report->id_pengguna);
        $this->assertSame('diajukan', $report->status_perbaikan);
        $this->assertNull($report->tanggal_selesai);
        Storage::disk('local')->assertExists($report->foto);
        $this->get(route('kerusakan.index'))->assertOk()->assertSee('Lampu tidak menyala sejak pagi.');
        $this->get(route('kerusakan.photo', $report))->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->actingAs($data['other'])->get(route('kerusakan.photo', $report))->assertNotFound();
    }

    public function test_report_without_photo_is_saved_and_remains_visible_after_a_new_page_request(): void
    {
        $data = $this->housing();
        $response = $this->actingAs($data['owner'])->post(route('kerusakan.store'), [
            'jenis_fasilitas' => 'bersama', 'id_fasilitas' => $data['shared'],
            'deskripsi' => 'Kerusakan fasilitas bersama yang dilaporkan melalui form.',
        ])->assertRedirect(route('kerusakan.index'))->assertSessionHasNoErrors();
        $report = LaporanKerusakan::sole();
        $response->assertSessionHas('created_report_id', $report->id_laporan)
            ->assertSessionHas('success', 'Laporan kerusakan berhasil diajukan');
        $this->assertSame('diajukan', $report->status_perbaikan);
        $this->assertNull($report->foto);
        $this->assertNull($report->tanggal_selesai);
        $this->assertSame('2026-11-19', $report->tanggal_lapor->toDateString());
        $page = $this->get(route('kerusakan.index'))->assertOk()->assertSee('Laporan kerusakan berhasil diajukan')
            ->assertSee('data-open-report="'.$report->id_laporan.'"', false)->assertSee($report->deskripsi)
            ->assertDontSee('Status awal: Laporan Diajukan.');
        $this->assertSame(1, substr_count($page->getContent(), 'Laporan kerusakan berhasil diajukan'));
        $this->get(route('kerusakan.index'))->assertOk()->assertSee($report->deskripsi)
            ->assertViewHas('reports', fn ($rows) => collect($rows)->contains('id_laporan', $report->id_laporan));
        $this->assertDatabaseCount('laporan_kerusakan', 1);
    }

    public function test_reports_cannot_target_another_clients_room_or_spoof_an_active_placement(): void
    {
        $data = $this->housing();
        $this->actingAs($data['owner'])->post(route('kerusakan.store'), ['jenis_fasilitas' => 'kamar', 'id_fasilitas' => $data['forbidden'], 'deskripsi' => 'Laporan fasilitas kamar penghuni lain.'])->assertSessionHasErrors('id_fasilitas');
        DB::table('penempatan_kamar')->where('id_penempatan', $data['placement'])->update(['status_penempatan' => 'selesai', 'tanggal_keluar' => '2026-11-19']);
        $this->post(route('kerusakan.store'), ['jenis_fasilitas' => 'kamar', 'id_fasilitas' => $data['facility'], 'deskripsi' => 'Laporan tanpa penempatan yang aktif.'])->assertSessionHasErrors('id_fasilitas');
        $this->assertDatabaseCount('laporan_kerusakan', 0);
    }

    public function test_empty_clients_get_empty_states_and_cannot_post_invalid_images(): void
    {
        $data = $this->housing();
        $this->actingAs($data['owner'])->post(route('kerusakan.store'), ['jenis_fasilitas' => 'kamar', 'id_fasilitas' => $data['facility'], 'deskripsi' => 'Laporan dengan foto yang tidak valid.', 'foto' => UploadedFile::fake()->create('bukan-foto.txt', 1, 'text/plain')])->assertSessionHasErrors('foto');
        $empty = $this->user('Empty');
        $this->actingAs($empty)->get(route('penempatan.index'))->assertOk()->assertSee('Belum ada penempatan kamar aktif')->assertViewHas('riwayat', fn ($rows) => $rows->isEmpty());
        $this->get(route('kerusakan.index'))->assertOk()->assertViewHas('reports', [])->assertViewHas('canReport', false);
    }

    public function test_client_pages_and_report_submission_require_the_client_role(): void
    {
        $this->get(route('penempatan.index'))->assertRedirect(route('login'));
        $this->get(route('kerusakan.index'))->assertRedirect(route('login'));
        $this->post(route('kerusakan.store'), [])->assertRedirect(route('login'));
        $admin = $this->user('Admin', 'Admin Layanan');
        $this->actingAs($admin)->get(route('penempatan.index'))->assertForbidden();
        $this->get(route('kerusakan.index'))->assertForbidden();
        $this->post(route('kerusakan.store'), [])->assertForbidden();
    }

    public function test_facility_added_by_facilitator_is_available_in_the_current_residents_report_form(): void
    {
        $data = $this->housing();
        $facilitator = $this->user('Fasilitator', 'Fasilitator');
        $buildingId = DB::table('kamar')->where('id_kamar', $data['room'])->value('id_gedung');
        $choice = $this->actingAs($facilitator)->postJson(route('pilihan-fasilitas.store'), ['nama_fasilitas' => 'Kipas kamar baru', 'lingkup' => 'kamar'])->assertCreated()->json('id');
        $room = \App\Models\Kamar::with('tipeKamar')->findOrFail($data['room']);
        $this->put(route('kamar.update', $room), [
            'gedung_id' => $buildingId, 'nomor_kamar' => $room->nomor_kamar, 'lantai' => $room->lantai,
            'tipe_kamar' => $room->getRelation('tipeKamar')->nama_tipe, 'status' => $room->status_kamar,
            'fasilitas_present' => 1, 'fasilitas' => [$choice],
        ])->assertSessionHasNoErrors();
        $id = DB::table('fasilitas')->where('nama_fasilitas', 'Kipas kamar baru')->where('id_kamar', $room->id_kamar)->value('id_fasilitas');
        $this->assertNotNull($id);
        $this->actingAs($data['owner'])->get(route('kerusakan.index'))->assertOk()->assertSee('Kipas kamar baru')
            ->assertViewHas('facilities', fn ($facilities) => $facilities[$id]['available']);
    }

    public function test_report_rejects_missing_invalid_or_mismatched_facility_type(): void
    {
        $data = $this->housing();
        $request = ['id_fasilitas' => $data['facility'], 'deskripsi' => 'Kerusakan fasilitas yang perlu diperiksa.'];
        $this->actingAs($data['owner'])->post(route('kerusakan.store'), $request)->assertSessionHasErrors('jenis_fasilitas');
        $this->post(route('kerusakan.store'), $request + ['jenis_fasilitas' => 'semua'])->assertSessionHasErrors('jenis_fasilitas');
        $this->post(route('kerusakan.store'), $request + ['jenis_fasilitas' => 'bersama'])->assertSessionHasErrors('id_fasilitas');
        $this->post(route('kerusakan.store'), array_replace($request, ['jenis_fasilitas' => 'kamar', 'id_fasilitas' => $data['shared']]))->assertSessionHasErrors('id_fasilitas');
        $this->assertDatabaseCount('laporan_kerusakan', 0);
    }

    public function test_facility_type_separates_current_room_and_shared_options_and_restores_valid_input(): void
    {
        $data = $this->housing();
        $oldRoom = DB::table('kamar')->where('nomor_kamar', '2A.01')->first();
        $oldFacility = DB::table('fasilitas')->insertGetId(['id_gedung' => $oldRoom->id_gedung, 'id_kamar' => $oldRoom->id_kamar, 'nama_fasilitas' => 'Lemari kamar sebelumnya']);
        LaporanKerusakan::create(['id_pengguna' => $data['owner']->id_pengguna, 'id_fasilitas' => $oldFacility, 'deskripsi' => 'Laporan dari kamar yang ditempati sebelumnya.', 'tanggal_lapor' => '2026-11-18', 'status_perbaikan' => 'selesai', 'tanggal_selesai' => '2026-11-18']);
        $building = DB::table('gedung')->insertGetId(['nama_gedung' => 'Gedung di luar penempatan']);
        DB::table('fasilitas')->insert(['id_gedung' => $building, 'id_kamar' => null, 'nama_fasilitas' => 'Fasilitas bersama gedung lain']);
        foreach (['kamar' => $data['facility'], 'bersama' => $data['shared']] as $type => $facilityId) {
            $response = $this->actingAs($data['owner'])->withSession(['_old_input' => ['jenis_fasilitas' => $type, 'id_fasilitas' => $facilityId]])
                ->get(route('kerusakan.index'))->assertOk();
            preg_match('/<select id="damage-facility".*?<\/select>/s', $response->getContent(), $matches);
            $options = $matches[0];
            $this->assertStringContainsString('value="'.$facilityId.'" selected', $options);
            $this->assertStringContainsString($type === 'kamar' ? 'Lampu database' : 'Fasilitas bersama database', $options);
            $this->assertStringNotContainsString($type === 'kamar' ? 'Fasilitas bersama database' : 'Lampu database', $options);
            $this->assertStringNotContainsString('Fasilitas rahasia tetangga', $options);
            $this->assertStringNotContainsString('Fasilitas bersama gedung lain', $options);
            $this->assertStringNotContainsString('Lemari kamar sebelumnya', $options);
        }
    }
}
