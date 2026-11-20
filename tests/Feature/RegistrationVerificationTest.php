<?php

namespace Tests\Feature;

use App\Models\{BerkasPendaftaran, Client, Gedung, JenisBerkas, JenisClient, Kamar, PendaftaranHunian, Pengguna, PenempatanKamar, PeriodePendaftaran, Role, TipeKamar};
use Database\Seeders\{JenisBerkasSeeder, JenisClientSeeder, VerifikasiPendaftaranDemoSeeder};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationVerificationTest extends TestCase
{
    use RefreshDatabase;

    private const SELF_SELECTION = ['reguler', 's1_non_maba', 'international_private', 'guest'];
    private const ADMIN_SELECTION = ['kipk', 'international_funded', 'summer_course'];

    private Pengguna $admin;
    private Kamar $room;
    private Kamar $femaleRoom;
    private PeriodePendaftaran $period;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->seed([JenisClientSeeder::class, JenisBerkasSeeder::class]);
        $this->admin = $this->user('Admin Layanan');
        $type = TipeKamar::create(['nama_tipe' => 'Standar Dua', 'kapasitas' => 2, 'tarif' => 1000000]);
        foreach (['putra' => 'Gedung Asrama Menpera (D)', 'putri' => 'Gedung Asrama Oren (F)'] as $gender => $name) {
            $building = Gedung::create(['nama_gedung' => $name, 'jenis_penghuni' => $gender, 'jumlah_lantai' => 2]);
            $room = Kamar::create(['id_gedung' => $building->id_gedung, 'id_tipeKamar' => $type->id_tipeKamar, 'nomor_kamar' => $gender === 'putra' ? '101' : '201', 'lantai' => 1, 'status_kamar' => 'tersedia']);
            if ($gender === 'putra') $this->room = $room; else $this->femaleRoom = $room;
        }
        $this->period = PeriodePendaftaran::create(['nama_periode' => 'Periode Aktif', 'tahun_akademik' => '2026/2027', 'tanggal_buka' => today()->subDay(), 'tanggal_tutup' => today()->addMonth(), 'status' => 'dibuka']);
    }

    private function user(string $role = 'Client'): Pengguna
    {
        $number = Pengguna::count() + 1;
        return Pengguna::create(['id_role' => Role::firstOrCreate(['nama_role' => $role])->id_role, 'nama_lengkap' => 'Client Verifikasi '.$number, 'email' => 'verif'.$number.'@example.com', 'username' => 'verif'.$number, 'password' => 'Password123!', 'status_aktif' => 1]);
    }

    private function registration(string $category = 'kipk', string $gender = 'laki-laki', bool $complete = true): PendaftaranHunian
    {
        $user = $this->user();
        $client = Client::create(['id_pengguna' => $user->id_pengguna, 'id_jenis_client' => JenisClient::where('jenis', JenisClient::CATEGORIES[$category])->value('id_jenis_client'), 'jenis_kelamin' => $gender, 'status_KIPK' => $category === 'kipk']);
        $registration = PendaftaranHunian::create(['id_client' => $client->id_client, 'id_periode' => $this->period->id_periode, 'id_tipeKamar' => in_array($category, self::SELF_SELECTION, true) ? $this->room->id_tipeKamar : null, 'id_kamar' => in_array($category, self::SELF_SELECTION, true) ? $this->room->id_kamar : null, 'tanggal_daftar' => now(), 'status_pendaftaran' => 'menunggu_verifikasi']);
        if ($complete) foreach (JenisBerkas::all() as $type) {
            $path = 'pendaftaran/'.$registration->id_pendaftaran.'/'.$type->id_jenis_berkas.'.png';
            Storage::disk('local')->put($path, file_get_contents(public_path('images/logo_andalasresidence.png')));
            BerkasPendaftaran::create(['id_pendaftaran' => $registration->id_pendaftaran, 'id_jenis_berkas' => $type->id_jenis_berkas, 'path_file' => $path, 'tanggal_upload' => today()]);
        }
        return $registration;
    }

    private function assignment(?Kamar $room = null, int $bed = 1): array
    {
        $room ??= $this->room;
        return ['id_gedung' => $room->id_gedung, 'id_kamar' => $room->id_kamar, 'nomor_bed' => $bed, 'tanggal_masuk' => today()->toDateString(), 'rencana_keluar' => today()->addMonth()->toDateString(), 'jadwal_kunci_mulai' => today()->format('Y-m-d').'T09:00', 'jadwal_kunci_selesai' => today()->format('Y-m-d').'T12:00', 'lokasi_kunci' => 'Kantor Fasilitator', 'penanggung_jawab' => 'Petugas Uji', 'kontak_penanggung_jawab' => '081234567890'];
    }

    private function scheduleData(): array
    {
        return \Illuminate\Support\Arr::except($this->assignment(), ['id_gedung','id_kamar','nomor_bed']);
    }

    private function applicant(string $category = 'reguler'): Pengguna
    {
        $user = $this->user();
        Client::create(['id_pengguna' => $user->id_pengguna,'id_jenis_client' => JenisClient::where('jenis',JenisClient::CATEGORIES[$category])->value('id_jenis_client'),'jenis_kelamin' => 'laki-laki','status_KIPK' => $category === 'kipk']);
        return $user;
    }

    private function application(): array
    {
        $data = ['id_periode' => $this->period->id_periode,'id_gedung' => $this->room->id_gedung,'id_tipeKamar' => $this->room->id_tipeKamar,'id_kamar' => $this->room->id_kamar,'nomor_identitas' => '2611502001','jenis_kelamin' => 'laki-laki','alamat_asal' => 'Padang','persetujuan' => 1,'berkas' => [],'id_jenis_berkas' => []];
        foreach (JenisBerkas::all() as $kind) {
            $data['berkas'][$kind->id_jenis_berkas] = \Illuminate\Http\UploadedFile::fake()->createWithContent('dokumen.png',file_get_contents(public_path('images/logo_andalasresidence.png')));
            $data['id_jenis_berkas'][$kind->id_jenis_berkas] = $kind->id_jenis_berkas;
        }
        return $data;
    }

    public function test_all_self_selection_categories_save_the_selected_room_and_verify_without_creating_a_placement(): void
    {
        foreach (self::SELF_SELECTION as $category) {
            $user = $this->applicant($category);
            $this->actingAs($user)->get(route('pendaftaran.create'))->assertOk()->assertSee('name="id_kamar"',false)->assertSee('name="id_gedung"',false);
            $this->post(route('pendaftaran.store'),$this->application())->assertSessionHasNoErrors();
            $record = PendaftaranHunian::where('id_client',$user->client->id_client)->sole();
            $this->assertSame($this->room->id_kamar,$record->id_kamar);
            $this->assertSame($this->room->id_gedung,$record->kamar->id_gedung);
            $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.verify',$record))->assertRedirect(route('verifikasi-pendaftaran.jadwal',$record));
            $this->assertSame('menunggu_verifikasi',$record->fresh()->status_pendaftaran);
            $this->post(route('verifikasi-pendaftaran.jadwal.store',$record),$this->scheduleData())->assertSessionHasNoErrors()->assertRedirect(route('verifikasi-pendaftaran.index'));
            $this->assertSame('terverifikasi',$record->fresh()->status_pendaftaran);
            $this->assertSame(0,$record->penempatanKamars()->count());
        }
    }

    public function test_client_room_choice_rejects_wrong_building_gender_type_and_unavailable_rooms(): void
    {
        $this->actingAs($this->applicant());
        $data = $this->application(); $data['id_gedung'] = $this->femaleRoom->id_gedung;
        $this->post(route('pendaftaran.store'),$data)->assertSessionHasErrors('id_kamar');
        $data = $this->application(); $data['jenis_kelamin'] = 'perempuan';
        $this->post(route('pendaftaran.store'),$data)->assertSessionHasErrors('id_kamar');
        $anotherType = TipeKamar::create(['nama_tipe' => 'Tipe Berbeda','kapasitas' => 4,'tarif' => 2000000]);
        $data = $this->application(); $data['id_tipeKamar'] = $anotherType->id_tipeKamar;
        $this->post(route('pendaftaran.store'),$data)->assertSessionHasErrors('id_kamar');
        $this->room->update(['status_kamar' => 'perbaikan']);
        $this->post(route('pendaftaran.store'),$this->application())->assertSessionHasErrors('id_kamar');
        $this->assertDatabaseCount('pendaftaran_hunian',0);
        $this->assertSame([],Storage::disk('local')->allFiles());
    }

    public function test_regular_schedule_requires_complete_dates_key_details_and_keeps_the_clients_room(): void
    {
        $record = $this->registration('reguler');
        $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.jadwal.store',$record),[])
            ->assertSessionHasErrors(['tanggal_masuk','rencana_keluar','jadwal_kunci_mulai','jadwal_kunci_selesai','lokasi_kunci','penanggung_jawab','kontak_penanggung_jawab']);
        $this->assertSame('menunggu_verifikasi',$record->fresh()->status_pendaftaran);
        $this->assertNull($record->fresh()->tanggal_masuk);
        $bad = $this->scheduleData(); $bad['rencana_keluar'] = today()->subDay()->toDateString();
        $this->post(route('verifikasi-pendaftaran.jadwal.store',$record),$bad)->assertSessionHasErrors('rencana_keluar');
        $bad = $this->scheduleData(); $bad['tanggal_masuk'] = today()->addDay()->toDateString();
        $this->post(route('verifikasi-pendaftaran.jadwal.store',$record),$bad)->assertSessionHasErrors('jadwal_kunci_mulai');
        $forged = $this->scheduleData() + ['id_kamar' => $this->femaleRoom->id_kamar,'id_gedung' => $this->femaleRoom->id_gedung];
        $this->post(route('verifikasi-pendaftaran.jadwal.store',$record),$forged)->assertSessionHasErrors(['id_kamar','id_gedung']);
        $this->assertSame($this->room->id_kamar,$record->fresh()->id_kamar);
        $this->assertDatabaseCount('penempatan_kamar',0);
    }

    public function test_schedule_endpoint_does_not_allow_clients_or_skip_room_assignment_for_admin_placed_categories(): void
    {
        $regular = $this->registration('reguler');
        $this->actingAs($regular->client->pengguna)->get(route('verifikasi-pendaftaran.jadwal',$regular))->assertForbidden();
        $this->post(route('verifikasi-pendaftaran.jadwal.store',$regular),$this->scheduleData())->assertForbidden();
        foreach (self::ADMIN_SELECTION as $category) {
            $record = $this->registration($category);
            $this->actingAs($this->admin)->get(route('verifikasi-pendaftaran.jadwal',$record))->assertForbidden();
            $this->post(route('verifikasi-pendaftaran.jadwal.store',$record),$this->scheduleData())->assertForbidden();
            $this->assertSame('menunggu_verifikasi',$record->fresh()->status_pendaftaran);
        }
    }

    public function test_admin_placement_categories_cannot_submit_their_own_room_choice(): void
    {
        foreach (self::ADMIN_SELECTION as $category) {
            $this->actingAs($this->applicant($category))->get(route('pendaftaran.create'))->assertOk()
                ->assertDontSee('name="id_kamar"', false)->assertDontSee('name="id_gedung"', false)
                ->assertSee('Gedung dan kamar ditentukan Admin Layanan');
            $this->post(route('pendaftaran.store'),$this->application())->assertSessionHasErrors(['id_kamar','id_gedung','id_tipeKamar']);
        }
        $this->assertDatabaseCount('pendaftaran_hunian',0);
    }

    public function test_admin_placement_categories_can_register_without_choosing_a_room(): void
    {
        foreach (self::ADMIN_SELECTION as $category) {
            $user = $this->applicant($category);
            $data = \Illuminate\Support\Arr::except($this->application(), ['id_kamar', 'id_gedung', 'id_tipeKamar']);
            $this->actingAs($user)->post(route('pendaftaran.store'), $data)->assertSessionHasNoErrors();
            $record = $user->client->pendaftaranHunians()->sole();
            $this->assertNull($record->id_kamar);
            $this->assertNull($record->id_tipeKamar);
            $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.verify', $record))
                ->assertRedirect(route('verifikasi-pendaftaran.penempatan', $record));
        }
    }

    public function test_queue_and_history_have_independent_chronological_order_and_hide_document_and_id_columns(): void
    {
        $new = $this->registration('reguler'); $new->update(['tanggal_daftar' => now()->subHour()]);
        $old = $this->registration('kipk'); $old->update(['tanggal_daftar' => now()->subHours(5)]);
        $middle = $this->registration('guest'); $middle->update(['tanggal_daftar' => now()->subHours(3)]);
        $latestReview = $this->registration('reguler'); $latestReview->update(['status_pendaftaran' => 'terverifikasi','tanggal_daftar' => now()->subDays(10),'tanggal_verifikasi' => now()->subMinute()]);
        $olderReview = $this->registration('guest'); $olderReview->update(['status_pendaftaran' => 'terverifikasi','tanggal_daftar' => now()->subHours(2),'tanggal_verifikasi' => now()->subMinutes(30)]);
        $response = $this->actingAs($this->admin)->get(route('verifikasi-pendaftaran.index'))->assertOk();
        $this->assertSame([$old->id_pendaftaran,$middle->id_pendaftaran,$new->id_pendaftaran],$response->viewData('waiting')->getCollection()->pluck('id_pendaftaran')->all());
        $this->assertSame([$latestReview->id_pendaftaran,$olderReview->id_pendaftaran],$response->viewData('history')->getCollection()->pluck('id_pendaftaran')->all());
        $html = $response->getContent();
        preg_match_all('/<th\b[^>]*>(.*?)<\/th>/s',$html,$headers);
        $labels = array_map('trim',$headers[1]);
        $this->assertSame('Tanggal &amp; Jam',$labels[0]);
        $this->assertNotContains('ID',$labels);
        $this->assertNotContains('Berkas',$labels);
        $this->assertSame(2,substr_count($html,'data-verification-table='));
    }

    public function test_both_verification_tables_paginate_ten_rows_with_independent_arrows_and_preserved_filters(): void
    {
        $waitingIds = [];
        $historyIds = [];
        for ($index = 0; $index < 12; $index++) {
            $waiting = $this->registration('reguler', complete: false);
            $waiting->update(['tanggal_daftar' => now()->subHours(12 - $index)]);
            $waitingIds[] = $waiting->id_pendaftaran;
            $history = $this->registration('reguler', complete: false);
            $history->update(['status_pendaftaran' => 'terverifikasi', 'tanggal_verifikasi' => now()->subMinutes($index)]);
            $historyIds[] = $history->id_pendaftaran;
        }
        $filters = ['q' => 'Client Verifikasi', 'jenis' => $waiting->client->id_jenis_client, 'periode' => $this->period->id_periode];
        $response = $this->actingAs($this->admin)->get(route('verifikasi-pendaftaran.index', $filters))->assertOk();
        foreach (['waiting' => $waitingIds, 'history' => $historyIds] as $key => $ids) {
            $paginator = $response->viewData($key);
            $this->assertSame(10, $paginator->count());
            $this->assertSame(12, $paginator->total());
            $this->assertSame(array_slice($ids, 0, 10), $paginator->getCollection()->pluck('id_pendaftaran')->all());
            $this->assertNull($paginator->previousPageUrl());
            $this->assertStringEndsWith('#verification-'.$key, $paginator->nextPageUrl());
            $response->assertSee('href="'.e($paginator->nextPageUrl()).'"', false);
            $next = $this->get($paginator->nextPageUrl())->assertOk();
            $this->assertSame(2, $next->viewData($key)->currentPage());
            $this->assertSame(1, $next->viewData($key === 'waiting' ? 'history' : 'waiting')->currentPage());
        }
        $response->assertSee('Halaman 1 dari 2')->assertSee('chevron_left')->assertSee('chevron_right');

        $last = $this->get(route('verifikasi-pendaftaran.index', $filters + ['antrean_page' => 2, 'riwayat_page' => 2]))->assertOk();
        foreach (['waiting' => $waitingIds, 'history' => $historyIds] as $key => $ids) {
            $paginator = $last->viewData($key);
            $this->assertSame(array_slice($ids, 10), $paginator->getCollection()->pluck('id_pendaftaran')->all());
            $this->assertSame(2, $paginator->count());
            $this->assertNull($paginator->nextPageUrl());
            $last->assertSee('href="'.e($paginator->previousPageUrl()).'"', false);
            parse_str(parse_url($paginator->previousPageUrl(), PHP_URL_QUERY), $query);
            foreach ($filters as $name => $value) $this->assertSame((string) $value, $query[$name]);
            $this->assertSame('1', $query[$key === 'waiting' ? 'antrean_page' : 'riwayat_page']);
            $this->assertSame('2', $query[$key === 'waiting' ? 'riwayat_page' : 'antrean_page']);
        }
        $last->assertSee('Menampilkan 11–12 dari 12 pendaftaran')->assertSee('Halaman 2 dari 2');
    }

    public function test_realistic_sample_records_have_rooms_where_required_and_do_not_duplicate(): void
    {
        $this->seed(\Database\Seeders\PendaftaranContohSeeder::class);
        $this->seed(\Database\Seeders\PendaftaranContohSeeder::class);
        $records = PendaftaranHunian::whereHas('client.pengguna',fn($q) => $q->where('email','like','%@example.test'))->get();
        $this->assertCount(8,$records);
        foreach ($records as $record) {
            $this->assertStringNotContainsString('Uji',$record->client->pengguna->nama_lengkap);
            $this->assertSame(3,$record->berkasPendaftarans()->count());
            if (! $record->client->requiresAdminPlacement()) $this->assertNotNull($record->id_kamar);
        }
    }

    public function test_seven_categories_are_seeded_and_registration_saves_the_selected_category(): void
    {
        $this->assertEqualsCanonicalizing([
            'Maba Lokal KIP-K', 'Maba Lokal NON KIP-K', 'Mahasiswa S1 Non Maba',
            'Internasional/S2/S3 pribadi', 'Internasional/S2/S3 ditanggung', 'Non Mahasiswa (UMUM)', 'Summer Course',
        ], JenisClient::pluck('jenis')->all());
        $form = $this->get(route('register'))->assertOk();
        foreach (JenisClient::CATEGORIES as $key => $name) {
            $form->assertSee($name);
            $this->post(route('register'), ['name' => 'Pendaftar '.$key, 'email' => $key.'@example.com', 'applicant_category' => $key, 'password' => 'Password123!', 'password_confirmation' => 'Password123!'])->assertRedirect(route('login'));
            $client = Pengguna::where('email',$key.'@example.com')->firstOrFail()->client;
            $this->assertSame($name, $client->jenisClient->jenis);
            $this->assertSame($key === 'kipk', $client->status_KIPK);
        }
        $this->seed(JenisClientSeeder::class);
        $this->assertDatabaseCount('jenis_client', 7);
    }

    public function test_legacy_categories_are_mapped_without_losing_registration_documents_or_placements(): void
    {
        $records = [];
        foreach (['reguler' => 'Mahasiswa Reguler', 'kipk' => 'Mahasiswa KIP-K', 'guest' => 'Non-Mahasiswa / Tamu'] as $code => $oldName) {
            $record = $this->registration($code);
            $record->client->jenisClient->update(['jenis' => $oldName]);
            $record->client->update(['status_KIPK' => $code !== 'kipk']);
            $records[$code] = $record;
        }
        $placement = PenempatanKamar::create(\Illuminate\Support\Arr::except($this->assignment(), ['id_gedung']) + [
            'id_pendaftaran' => $records['kipk']->id_pendaftaran, 'id_admin' => $this->admin->id_pengguna, 'status_penempatan' => 'aktif',
        ]);
        $legacy = JenisClient::create(['jenis' => 'Mahasiswa Pascasarjana']);
        $private = $this->applicant('international_private')->client;
        $private->update(['id_jenis_client' => $legacy->id_jenis_client]);
        $registrationsBefore = PendaftaranHunian::all()->toArray();
        $documentsBefore = BerkasPendaftaran::all()->toArray();
        $placementBefore = $placement->fresh()->toArray();

        $this->seed(JenisClientSeeder::class);
        $ids = JenisClient::pluck('id_jenis_client', 'jenis')->all();
        $this->seed(JenisClientSeeder::class);
        $this->assertSame($ids, JenisClient::pluck('id_jenis_client', 'jenis')->all());
        $this->assertDatabaseCount('jenis_client', 7);
        foreach ($records as $code => $record) {
            $client = $record->client->fresh();
            $this->assertSame($record->client->id_jenis_client, $client->id_jenis_client);
            $this->assertSame(JenisClient::CATEGORIES[$code], $client->jenisClient->jenis);
            $this->assertSame($code === 'kipk', $client->status_KIPK);
        }
        $this->assertSame(JenisClient::CATEGORIES['international_private'], $private->fresh()->jenisClient->jenis);
        $this->assertSame($registrationsBefore, PendaftaranHunian::all()->toArray());
        $this->assertSame($documentsBefore, BerkasPendaftaran::all()->toArray());
        $this->assertSame($placementBefore, $placement->fresh()->toArray());
    }

    public function test_admin_listing_and_full_detail_use_database_and_filter_records(): void
    {
        $kip = $this->registration('kipk'); $regular = $this->registration('reguler');
        $this->actingAs($this->admin)->get(route('verifikasi-pendaftaran.index'))->assertOk()->assertSee('Ditempatkan Admin')->assertSee('Tanggal &amp; Jam', false)->assertSee($kip->client->pengguna->nama_lengkap)->assertDontSee('Data contoh')->assertDontSee('verification-detail-dialog');
        $this->get(route('verifikasi-pendaftaran.index', ['jenis' => $kip->client->id_jenis_client]))->assertOk()->assertSee($kip->client->pengguna->nama_lengkap)->assertDontSee($regular->client->pengguna->nama_lengkap)->assertViewHas('waiting', fn ($rows) => $rows->total() === 1);
        $this->get(route('verifikasi-pendaftaran.show',$kip))->assertOk()->assertSee('Berkas Persyaratan')->assertSee('Fotocopy KTP');
    }

    public function test_all_self_selection_categories_save_their_housing_schedule_and_verify_without_an_admin_placement(): void
    {
        foreach (self::SELF_SELECTION as $category) {
            $record = $this->registration($category);
            $response = $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.verify',$record));
            $response->assertRedirect(route('verifikasi-pendaftaran.jadwal',$record));
            $this->assertSame('menunggu_verifikasi',$record->fresh()->status_pendaftaran);
            $this->get(route('verifikasi-pendaftaran.jadwal',$record))->assertOk()
                ->assertSeeInOrder(['Gedung dan Kamar Pilihan Client','Gedung Asrama Menpera (D)','101','1. Atur Periode Hunian','2. Jadwal Pengambilan Kunci'])
                ->assertDontSee('name="id_kamar"',false)->assertDontSee('name="id_gedung"',false)->assertDontSee('name="pilihan_bed"',false);
            $response = $this->post(route('verifikasi-pendaftaran.jadwal.store',$record),$this->scheduleData());
            $response->assertSessionHasNoErrors()->assertRedirect(route('verifikasi-pendaftaran.index'))
                ->assertSessionHas('success', 'Pendaftaran berhasil diverifikasi dan jadwal hunian berhasil disimpan.');
            $this->followRedirects($response)->assertOk()
                ->assertSeeInOrder(['Pendaftaran berhasil diverifikasi dan jadwal hunian berhasil disimpan.', 'Ringkasan verifikasi pendaftaran'])
                ->assertSee('check_circle');
            $this->assertSame('terverifikasi',$record->fresh()->status_pendaftaran);
            $this->assertSame($this->admin->id_pengguna,$record->fresh()->id_admin);
            $this->assertNotNull($record->fresh()->tanggal_verifikasi);
            $this->assertSame(today()->toDateString(),$record->fresh()->tanggal_masuk->toDateString());
            $this->assertSame(today()->addMonth()->toDateString(),$record->fresh()->rencana_keluar->toDateString());
            $this->assertSame('Kantor Fasilitator',$record->fresh()->lokasi_kunci);
            $this->assertSame('09:00',$record->fresh()->jadwal_kunci_mulai->format('H:i'));
            $this->assertSame('12:00',$record->fresh()->jadwal_kunci_selesai->format('H:i'));
            $this->get(route('verifikasi-pendaftaran.show',$record))->assertOk()->assertSee('Kantor Fasilitator')->assertSee('Petugas Uji');
            $this->post(route('verifikasi-pendaftaran.jadwal.store',$record),$this->scheduleData())->assertSessionHasErrors('verifikasi');
            $this->assertSame(0,$record->penempatanKamars()->count());
            $this->post(route('verifikasi-pendaftaran.verify',$record))->assertSessionHasErrors('verifikasi');
        }
    }

    public function test_all_admin_placement_categories_require_a_room_and_save_real_room_bed_and_key_details(): void
    {
        foreach (self::ADMIN_SELECTION as $index => $category) {
            $room = $this->room;
            if ($index === 2) {
                $room = $this->room->replicate();
                $room->nomor_kamar = '102';
                $room->save();
            }
            $bed = $index % 2 + 1;
            $record = $this->registration($category);
            $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.verify',$record))->assertRedirect(route('verifikasi-pendaftaran.penempatan',$record));
            $this->assertSame('menunggu_verifikasi',$record->fresh()->status_pendaftaran);
            $this->get(route('verifikasi-pendaftaran.penempatan',$record))->assertOk()->assertSee('Gedung Asrama Menpera (D)')->assertSee('Standar Dua');
            $data = $this->assignment(room:$room, bed:$bed);
            unset($data['id_kamar'],$data['nomor_bed']);
            $data['pilihan_bed'] = $room->id_kamar.':'.$bed;
            $response = $this->post(route('verifikasi-pendaftaran.assign',$record),$data);
            $response->assertSessionHasNoErrors()->assertRedirect(route('verifikasi-pendaftaran.index'))
                ->assertSessionHas('success', 'Pendaftaran terverifikasi dan penempatan kamar berhasil disimpan.');
            $this->followRedirects($response)->assertOk()
                ->assertSeeInOrder(['Pendaftaran terverifikasi dan penempatan kamar berhasil disimpan.', 'Ringkasan verifikasi pendaftaran'])
                ->assertSee('check_circle');
            $placement = $record->penempatanKamars()->sole();
            $this->assertSame($room->id_kamar,$placement->id_kamar);
            $this->assertSame($bed,$placement->nomor_bed);
            $this->assertSame('Kantor Fasilitator',$placement->lokasi_kunci);
            $this->assertSame('Petugas Uji',$placement->penanggung_jawab);
            $this->assertSame('terverifikasi',$record->fresh()->status_pendaftaran);
            $this->assertSame($room->id_tipeKamar,$record->fresh()->id_tipeKamar);
            $this->actingAs($record->client->pengguna)->get(route('penempatan.index'))->assertOk()->assertSee($room->nomor_kamar)->assertSee('Kantor Fasilitator');
        }
    }

    public function test_occupied_beds_invalid_capacity_and_unavailable_rooms_are_rejected_atomically(): void
    {
        $first = $this->registration(); $second = $this->registration('summer_course');
        $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.assign',$first),$this->assignment())->assertSessionHasNoErrors();
        $this->post(route('verifikasi-pendaftaran.assign',$second),$this->assignment())->assertSessionHasErrors('nomor_bed');
        $this->post(route('verifikasi-pendaftaran.assign',$second),$this->assignment(bed:3))->assertSessionHasErrors('id_kamar');
        $this->room->update(['status_kamar' => 'perbaikan']);
        $this->post(route('verifikasi-pendaftaran.assign',$second),$this->assignment(bed:2))->assertSessionHasErrors('id_kamar');
        $this->assertSame('menunggu_verifikasi',$second->fresh()->status_pendaftaran);
        $this->assertNull($second->fresh()->id_tipeKamar);
        $this->assertDatabaseCount('penempatan_kamar',1);
    }

    public function test_gender_and_building_mismatch_and_bad_key_schedule_are_rejected(): void
    {
        $record = $this->registration('kipk','perempuan');
        $this->actingAs($this->admin)->get(route('verifikasi-pendaftaran.penempatan',$record))->assertOk()->assertSee('Gedung Asrama Oren (F)')->assertDontSee('Gedung Asrama Menpera (D)');
        $this->post(route('verifikasi-pendaftaran.assign',$record),$this->assignment())->assertSessionHasErrors('id_kamar');
        $bad = $this->assignment($this->femaleRoom); $bad['id_gedung'] = $this->room->id_gedung;
        $this->post(route('verifikasi-pendaftaran.assign',$record),$bad)->assertSessionHasErrors('id_kamar');
        $bad = $this->assignment($this->femaleRoom); $bad['jadwal_kunci_selesai'] = today()->format('Y-m-d').'T08:00';
        $this->post(route('verifikasi-pendaftaran.assign',$record),$bad)->assertSessionHasErrors('jadwal_kunci_selesai');
        $this->assertDatabaseCount('penempatan_kamar',0);
    }

    public function test_legacy_occupants_without_recorded_bed_positions_block_new_bed_assignments(): void
    {
        $legacy = $this->registration('reguler');
        PenempatanKamar::create(['id_pendaftaran' => $legacy->id_pendaftaran,'id_kamar' => $this->room->id_kamar,'tanggal_masuk' => today()->subDay(),'status_penempatan' => 'aktif']);
        $record = $this->registration();
        $this->actingAs($this->admin)->get(route('verifikasi-pendaftaran.penempatan',$record))->assertOk()->assertSee('Perlu Pendataan');
        $this->post(route('verifikasi-pendaftaran.assign',$record),$this->assignment(bed:2))->assertSessionHasErrors('nomor_bed');
        $this->assertDatabaseCount('penempatan_kamar',1);
        $this->assertSame('menunggu_verifikasi',$record->fresh()->status_pendaftaran);
    }

    public function test_incomplete_or_missing_files_cannot_be_verified(): void
    {
        $record = $this->registration('reguler',complete:false);
        $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.jadwal.store',$record),$this->scheduleData())->assertSessionHasErrors('berkas');
        $record = $this->registration('reguler');
        Storage::disk('local')->delete($record->berkasPendaftarans()->first()->path_file);
        $this->post(route('verifikasi-pendaftaran.jadwal.store',$record),$this->scheduleData())->assertSessionHasErrors('berkas');
        $this->assertSame('menunggu_verifikasi',$record->fresh()->status_pendaftaran);
    }

    public function test_admin_can_classify_legacy_documents_without_changing_the_file_or_registration_status(): void
    {
        $record = $this->registration('reguler', complete: false);
        $path = 'pendaftaran/berkas-arsip.png';
        Storage::disk('local')->put($path, file_get_contents(public_path('images/logo_andalasresidence.png')));
        $document = $record->berkasPendaftarans()->create(['id_jenis_berkas' => null, 'path_file' => $path, 'tanggal_upload' => today()]);
        $url = route('verifikasi-pendaftaran.berkas.jenis', [$record, $document]);
        $type = JenisBerkas::where('nama_berkas', 'Fotocopy KTP')->firstOrFail();
        $this->post($url, ['id_jenis_berkas' => $type->id_jenis_berkas])->assertRedirect(route('login'));
        $this->actingAs($record->client->pengguna)->post($url, ['id_jenis_berkas' => $type->id_jenis_berkas])->assertForbidden();
        $this->actingAs($this->admin)->get(route('verifikasi-pendaftaran.show', $record))->assertOk()
            ->assertSee('Berkas Lama Belum Diklasifikasikan')->assertSee('Simpan Jenis Berkas');
        $this->get(route('verifikasi-pendaftaran.berkas', [$record, $document]))->assertOk();
        $other = $this->registration();
        $this->post(route('verifikasi-pendaftaran.berkas.jenis', [$other, $document]), ['id_jenis_berkas' => $type->id_jenis_berkas])->assertNotFound();
        $this->post($url, [])->assertSessionHasErrors('id_jenis_berkas');
        $this->post($url, ['id_jenis_berkas' => 99999])->assertSessionHasErrors('id_jenis_berkas');
        $this->assertNull($document->fresh()->id_jenis_berkas);
        $this->post($url, ['id_jenis_berkas' => $type->id_jenis_berkas])->assertSessionHasNoErrors()
            ->assertRedirect(route('verifikasi-pendaftaran.show', $record))->assertSessionHas('success', 'Jenis berkas lama berhasil disimpan.');
        $this->assertSame($type->id_jenis_berkas, $document->fresh()->id_jenis_berkas);
        $this->assertSame($path, $document->fresh()->path_file);
        Storage::disk('local')->assertExists($path);
        $this->assertSame('menunggu_verifikasi', $record->fresh()->status_pendaftaran);
        $this->get(route('verifikasi-pendaftaran.show', $record))->assertOk()->assertDontSee('Berkas Lama Belum Diklasifikasikan');
        $this->post($url, ['id_jenis_berkas' => JenisBerkas::whereKeyNot($type->id_jenis_berkas)->first()->id_jenis_berkas])->assertSessionHasErrors('id_jenis_berkas');
        $this->assertSame($type->id_jenis_berkas, $document->fresh()->id_jenis_berkas);
    }

    public function test_unclassified_legacy_documents_must_be_resolved_even_if_the_three_required_types_exist(): void
    {
        $record = $this->registration('reguler');
        $path = 'pendaftaran/arsip-tambahan.png';
        Storage::disk('local')->put($path, file_get_contents(public_path('images/logo_andalasresidence.png')));
        $document = $record->berkasPendaftarans()->create(['id_jenis_berkas' => null, 'path_file' => $path, 'tanggal_upload' => today()]);
        $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.jadwal.store', $record), $this->scheduleData())->assertSessionHasErrors('berkas');
        $this->assertSame('menunggu_verifikasi', $record->fresh()->status_pendaftaran);
        $this->assertNull($record->fresh()->tanggal_verifikasi);
        $this->post(route('verifikasi-pendaftaran.berkas.jenis', [$record, $document]), ['id_jenis_berkas' => JenisBerkas::firstOrFail()->id_jenis_berkas])->assertSessionHasNoErrors();
        $this->get(route('verifikasi-pendaftaran.show', $record))->assertOk()->assertSee(route('verifikasi-pendaftaran.berkas', [$record, $document]));
        $this->post(route('verifikasi-pendaftaran.jadwal.store', $record), $this->scheduleData())->assertSessionHasNoErrors()->assertRedirect(route('verifikasi-pendaftaran.index'));
        $this->assertSame('terverifikasi', $record->fresh()->status_pendaftaran);
        $this->assertDatabaseHas('berkas_pendaftaran', ['id_berkas' => $document->id_berkas, 'path_file' => $path]);
    }

    public function test_one_client_cannot_hold_overlapping_rooms_but_a_later_stay_can_use_the_same_bed(): void
    {
        $first = $this->registration();
        $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.assign',$first),$this->assignment())->assertSessionHasNoErrors();
        $second = $first->replicate();
        $second->fill(['status_pendaftaran' => 'menunggu_verifikasi','tanggal_verifikasi' => null,'id_admin' => null])->save();
        foreach ($first->berkasPendaftarans as $document) $second->berkasPendaftarans()->create($document->only(['id_jenis_berkas','path_file','tanggal_upload']));
        $otherRoom = Kamar::create(['id_gedung' => $this->room->id_gedung,'id_tipeKamar' => $this->room->id_tipeKamar,'nomor_kamar' => '102','lantai' => 1,'status_kamar' => 'tersedia']);
        $this->post(route('verifikasi-pendaftaran.assign',$second),$this->assignment($otherRoom))->assertSessionHasErrors('penempatan');
        $future = $this->assignment();
        $future['tanggal_masuk'] = today()->addMonth()->addDay()->toDateString();
        $future['rencana_keluar'] = today()->addMonths(2)->toDateString();
        $future['jadwal_kunci_mulai'] = $future['tanggal_masuk'].'T09:00';
        $future['jadwal_kunci_selesai'] = $future['tanggal_masuk'].'T12:00';
        $this->post(route('verifikasi-pendaftaran.assign',$second),$future)->assertSessionHasNoErrors();
        $this->assertDatabaseCount('penempatan_kamar',2);
        $this->assertSame(1,PenempatanKamar::forUser($first->client->id_pengguna)->current()->count());
    }

    public function test_a_failed_verification_write_rolls_back_its_room_reservation(): void
    {
        $record = $this->registration();
        PendaftaranHunian::updating(function () { throw new \RuntimeException('Simulated write failure'); });
        $this->actingAs($this->admin)->withoutExceptionHandling();
        try {
            $this->post(route('verifikasi-pendaftaran.assign',$record),$this->assignment());
            $this->fail('The database write should fail.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated write failure',$exception->getMessage());
        }
        $this->assertDatabaseCount('penempatan_kamar',0);
        $this->assertSame('menunggu_verifikasi',$record->fresh()->status_pendaftaran);
        $this->assertNull($record->fresh()->id_tipeKamar);
    }

    public function test_document_access_and_actions_require_admin_and_document_must_belong_to_registration(): void
    {
        $record = $this->registration(); $other = $this->registration();
        $document = $record->berkasPendaftarans()->first();
        $url = route('verifikasi-pendaftaran.berkas',[$record,$document]);
        $this->get($url)->assertRedirect(route('login'));
        $this->actingAs($record->client->pengguna)->get($url)->assertForbidden();
        $this->post(route('verifikasi-pendaftaran.assign',$record),$this->assignment())->assertForbidden();
        $this->actingAs($this->admin)->get($url)->assertOk();
        $this->get(route('verifikasi-pendaftaran.berkas',[$other,$document]))->assertNotFound();
    }

    public function test_rejection_and_revision_notes_are_persisted_and_processed_records_cannot_be_overwritten(): void
    {
        $record = $this->registration();
        $this->actingAs($this->admin)->post(route('verifikasi-pendaftaran.decision',$record),['status' => 'ditolak'])->assertSessionHasErrors('catatan');
        $response = $this->post(route('verifikasi-pendaftaran.decision',$record),['status' => 'perlu_perbaikan','catatan' => 'Dokumen perlu diperjelas.']);
        $response->assertSessionHasNoErrors()->assertRedirect(route('verifikasi-pendaftaran.index'))
            ->assertSessionHas('success', 'Pendaftaran berhasil dikembalikan.');
        $this->followRedirects($response)->assertOk()
            ->assertSeeInOrder(['Pendaftaran berhasil dikembalikan.', 'Ringkasan verifikasi pendaftaran'])
            ->assertSee('check_circle');
        $this->assertSame('perlu_perbaikan',$record->fresh()->status_pendaftaran);
        $this->post(route('verifikasi-pendaftaran.decision',$record),['status' => 'ditolak','catatan' => 'Persyaratan tidak sesuai.'])->assertSessionHasNoErrors();
        $this->post(route('verifikasi-pendaftaran.assign',$record),$this->assignment())->assertSessionHasErrors('verifikasi');
        $this->assertSame('ditolak',$record->fresh()->status_pendaftaran);
    }

    public function test_demo_seeder_creates_seven_pending_applications_and_twenty_one_private_files_without_duplicates(): void
    {
        $this->seed(VerifikasiPendaftaranDemoSeeder::class);
        $this->seed(VerifikasiPendaftaranDemoSeeder::class);
        $records = PendaftaranHunian::whereHas('client.pengguna',fn($q) => $q->where('email','like','uji.verifikasi.%@andalas.test'))->get();
        $this->assertCount(7,$records);
        foreach ($records as $record) {
            $this->assertSame('menunggu_verifikasi',$record->status_pendaftaran);
            $this->assertSame(3,$record->berkasPendaftarans()->count());
            $this->assertSame(! $record->client->requiresAdminPlacement(), $record->id_kamar !== null);
            foreach ($record->berkasPendaftarans as $file) Storage::disk('local')->assertExists($file->path_file);
        }
    }
}
