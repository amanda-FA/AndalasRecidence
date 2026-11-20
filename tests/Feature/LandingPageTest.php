<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Gedung;
use App\Models\Kamar;
use App\Models\Pengguna;
use App\Models\PeriodePendaftaran;
use App\Models\Role;
use App\Models\TipeKamar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LandingPageTest extends TestCase
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

    private function housing(): array
    {
        $building = Gedung::create(['nama_gedung' => 'Hunian Data Asli', 'jenis_penghuni' => 'putra', 'jumlah_lantai' => 2, 'keterangan' => 'Informasi hunian dari database']);
        $type = TipeKamar::create(['nama_tipe' => 'Standar Dua', 'kapasitas' => 2, 'tarif' => 2500000]);
        $room = Kamar::create(['id_gedung' => $building->id_gedung, 'id_tipeKamar' => $type->id_tipeKamar, 'nomor_kamar' => '1A.01', 'lantai' => 1, 'status_kamar' => 'tersedia']);
        Fasilitas::create(['id_gedung' => $building->id_gedung, 'nama_fasilitas' => 'Meja Belajar', 'kondisi' => 'Baik']);

        return compact('building', 'type', 'room');
    }

    public function test_guests_can_browse_landing_catalog_and_building_detail_without_login(): void
    {
        $data = $this->housing();
        $this->get('/')->assertOk()->assertSee('Hunian Data Asli')->assertSee('Meja Belajar')->assertSee('Standar Dua')
            ->assertSee(route('login'))->assertSee(route('register'))->assertDontSee('name="password"', false)
            ->assertViewHas('buildingCount', 1)->assertViewHas('roomCount', 1)->assertViewHas('typeCount', 1);
        $this->get(route('hunian.index'))->assertOk()->assertSee('Hunian Data Asli');
        $this->get(route('hunian.show', $data['building']))->assertOk()->assertSee('1A.01')->assertSee('Rp 2.500.000')
            ->assertViewHas('building', fn ($building) => $building['kamar_tersedia'] === 1 && $building['sisa_bed'] === 2);
        $this->assertGuest();
        $this->assertDatabaseCount('gedung', 1);
    }

    public function test_public_availability_counts_current_placements_without_disclosing_resident_data(): void
    {
        $data = $this->housing();
        $user = Pengguna::create(['id_role' => Role::create(['nama_role' => 'Client'])->id_role, 'nama_lengkap' => 'PENGHUNI RAHASIA', 'email' => 'PRIVATE-EMAIL@example.com', 'username' => 'private_person', 'password' => 'Password123!', 'status_aktif' => 1]);
        $kind = DB::table('jenis_client')->insertGetId(['jenis' => \App\Models\JenisClient::CATEGORIES['reguler']]);
        $client = DB::table('client')->insertGetId(['id_pengguna' => $user->id_pengguna, 'id_jenis_client' => $kind]);
        $period = PeriodePendaftaran::create(['nama_periode' => 'Periode saat ini', 'tahun_akademik' => '2026/2027', 'tanggal_buka' => '2026-11-19', 'tanggal_tutup' => '2026-11-19', 'status' => 'dibuka']);
        $registration = DB::table('pendaftaran_hunian')->insertGetId(['id_client' => $client, 'id_periode' => $period->id_periode, 'id_tipeKamar' => $data['type']->id_tipeKamar, 'tanggal_daftar' => '2026-11-19', 'status_pendaftaran' => 'terverifikasi']);
        foreach ([['2026-11-19', null], ['2026-11-20', null], ['2026-10-01', '2026-11-18']] as [$start, $end]) {
            DB::table('penempatan_kamar')->insert(['id_pendaftaran' => $registration, 'id_kamar' => $data['room']->id_kamar, 'tanggal_masuk' => $start, 'tanggal_keluar' => $end, 'status_penempatan' => 'aktif']);
        }
        Kamar::create(['id_gedung' => $data['building']->id_gedung, 'id_tipeKamar' => $data['type']->id_tipeKamar, 'nomor_kamar' => '1A.02', 'lantai' => 1, 'status_kamar' => 'perbaikan']);
        $this->get(route('hunian.show', $data['building']))->assertOk()->assertDontSee('PENGHUNI RAHASIA')->assertDontSee('PRIVATE-EMAIL')
            ->assertViewHas('building', fn ($building) => $building['sisa_bed'] === 1 && $building['kamar_tersedia'] === 1)
            ->assertSee('Dalam perbaikan');
    }

    public function test_period_statuses_follow_database_and_dates_including_both_boundary_days(): void
    {
        foreach ([['Dibuka Hari Ini', '2026-11-19', '2026-11-19', 'dibuka'], ['Periode Mendatang', '2026-11-20', '2026-12-01', 'dibuka'], ['Periode Selesai', '2026-11-01', '2026-11-18', 'dibuka']] as [$name, $start, $end, $status]) {
            PeriodePendaftaran::create(['nama_periode' => $name, 'tahun_akademik' => '2026/2027', 'tanggal_buka' => $start, 'tanggal_tutup' => $end, 'status' => $status]);
        }
        $response = $this->get('/')->assertOk()->assertSee('Dibuka Hari Ini')->assertSee('Periode Mendatang')->assertSee('Periode Selesai')->assertViewHas('hasOpenPeriod', true);
        $this->assertSame(['Dibuka', 'Akan Datang', 'Ditutup'], $response->viewData('periods')->pluck('status')->all());
        $this->assertSame(1, substr_count($response->getContent(), '>Daftar Sekarang '));
        $this->get(route('pendaftaran.create'))->assertRedirect(route('login'));
        $this->assertSame(route('pendaftaran.create'), session('url.intended'));
        $staff = Pengguna::create(['id_role' => Role::create(['nama_role' => 'Fasilitator'])->id_role, 'nama_lengkap' => 'Staff', 'email' => 'staff@example.com', 'username' => 'staff', 'password' => 'Password123!', 'status_aktif' => 1]);
        $this->actingAs($staff)->get('/')->assertOk()->assertSee('Pendaftaran hunian diperuntukkan bagi akun Client.')
            ->assertDontSee('>Daftar Sekarang ', false);
    }

    public function test_empty_data_renders_honest_empty_states_without_fake_periods(): void
    {
        $this->get('/')->assertOk()->assertSee('Informasi hunian sedang disiapkan')->assertSee('Jadwal pendaftaran belum tersedia')
            ->assertViewHas('hasOpenPeriod', false)->assertViewHas('buildingCount', 0);
        $this->get(route('hunian.index'))->assertOk()->assertSee('Hunian belum ditemukan');
        $this->get('/hunian/99999')->assertNotFound();
    }

    public function test_catalog_filters_and_paginates_public_rooms(): void
    {
        $data = $this->housing();
        Gedung::create(['nama_gedung' => 'Gedung Putri', 'jenis_penghuni' => 'putri']);
        $this->get(route('hunian.index', ['peruntukan' => 'putra', 'q' => 'Asli']))->assertOk()->assertSee('Hunian Data Asli')->assertDontSee('Gedung Putri');
        for ($i = 2; $i <= 14; $i++) {
            Kamar::create(['id_gedung' => $data['building']->id_gedung, 'id_tipeKamar' => $data['type']->id_tipeKamar, 'nomor_kamar' => sprintf('1A.%02d', $i), 'lantai' => 1, 'status_kamar' => 'tersedia']);
        }
        $this->get(route('hunian.show', ['gedung' => $data['building']->id_gedung, 'page' => 2]))->assertOk()->assertSee('1A.13')->assertDontSee('1A.01');
    }

    public function test_public_home_and_portal_keep_each_existing_role_destination(): void
    {
        foreach (['Client' => 'pendaftaran.create', 'Fasilitator' => 'gedung.index', 'Admin Layanan' => 'periode.index', 'Pimpinan' => 'dashboard.okupansi'] as $role => $route) {
            $user = Pengguna::create(['id_role' => Role::create(['nama_role' => $role])->id_role, 'nama_lengkap' => $role, 'email' => str_replace(' ', '', $role).'@example.com', 'username' => str_replace(' ', '', $role), 'password' => 'Password123!', 'status_aktif' => 1]);
            $this->actingAs($user)->get('/')->assertOk()->assertSee('Buka Portal');
            $this->get(route('portal'))->assertRedirect(route($route));
            $this->get(route('login'))->assertRedirect(route('portal'));
        }
    }
}
