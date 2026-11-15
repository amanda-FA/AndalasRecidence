<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class FrontendViewsTest extends TestCase
{
    /**
     * Helper untuk membuat ViewErrorBag kosong agar rendering view mandiri tidak error.
     */
    private function defaultViewData(array $extra = []): array
    {
        return array_merge([
            'errors' => new ViewErrorBag(),
        ], $extra);
    }

    /**
     * Test 1: Memastikan file manifest Vite dan file hasil kompilasi aset (CSS & JS) ada dan valid.
     */
    public function test_vite_manifest_exists_and_contains_built_assets(): void
    {
        $manifestPath = public_path('build/manifest.json');

        $this->assertTrue(File::exists($manifestPath), 'File public/build/manifest.json harus ada.');

        $manifest = json_decode(File::get($manifestPath), true);
        $this->assertIsArray($manifest);
        $this->assertArrayHasKey('resources/css/app.css', $manifest, 'Manifest harus memuat build resources/css/app.css');
        $this->assertArrayHasKey('resources/js/app.js', $manifest, 'Manifest harus memuat build resources/js/app.js');

        // Pastikan file CSS dan JS hasil build benar-benar ada di storage publik
        $cssFile = public_path('build/' . $manifest['resources/css/app.css']['file']);
        $jsFile = public_path('build/' . $manifest['resources/js/app.js']['file']);

        $this->assertTrue(File::exists($cssFile), "File CSS build [{$cssFile}] harus ada.");
        $this->assertTrue(File::exists($jsFile), "File JS build [{$jsFile}] harus ada.");
        $this->assertGreaterThan(0, File::size($cssFile), "File CSS tidak boleh kosong.");
        $this->assertGreaterThan(0, File::size($jsFile), "File JS tidak boleh kosong.");
    }

    /**
     * Test 2: Memastikan Blade template halaman Login terkompilasi dan memuat elemen form UI secara lengkap.
     */
    public function test_login_view_compiles_and_renders_ui_elements(): void
    {
        $view = $this->view('auth.login', $this->defaultViewData());

        $view->assertSee('Andalas Residence');
        $view->assertSee('Masuk Portal');
        $view->assertSee('name="email"', false);
        $view->assertSee('name="password"', false);
        $view->assertSee('type="submit"', false);
        $view->assertSee(route('register'));
    }

    /**
     * Test 3: Memastikan Blade template halaman Register terkompilasi dan memuat form registrasi pengguna.
     */
    public function test_register_view_compiles_and_renders_ui_elements(): void
    {
        $view = $this->view('auth.register', $this->defaultViewData());

        $view->assertSee('Buat Akun Client');
        $view->assertSee('name="name"', false);
        $view->assertSee('name="email"', false);
        $view->assertSee('name="applicant_category"', false);
        $view->assertSee('name="password"', false);
        $view->assertSee('name="password_confirmation"', false);
        $view->assertSee(route('login'));
    }

    /**
     * Test 4: Memastikan Blade template Welcome (Landing Page) terkompilasi dengan baik.
     */
    public function test_welcome_view_compiles_successfully(): void
    {
        $view = $this->view('welcome', $this->defaultViewData());

        $view->assertSee('Log in');
    }

    /**
     * Test 5: Memastikan view Dashboard Okupansi ter-render dengan komponen KPI dan kartu gedung.
     */
    public function test_dashboard_okupansi_view_compiles_with_metrics_and_cards(): void
    {
        $dummyGedungList = collect([
            [
                'id' => 1,
                'kode' => 'MN',
                'nama' => 'Gedung Menpera (D)',
                'nama_singkat' => 'Menpera (D)',
                'blok' => 'Sektor Limau Manis',
                'klaster_grup' => 'putra',
                'klaster_label' => 'Khusus Putra',
                'klaster_color' => 'text-secondary',
                'kamar' => 100,
                'total_bed' => 400,
                'terisi' => 380,
                'tersedia' => 20,
                'persentase' => 95.0,
                'status' => 'Hampir Penuh',
                'status_style' => 'bg-[#ba1a1a]/10 text-error',
            ],
        ]);

        $dummyKpi = [
            'total_bed' => 400,
            'total_terisi' => 380,
            'total_tersedia' => 20,
            'persentase_okupansi' => 95.0,
            'persentase_tersedia' => 5.0,
            'kamar_perbaikan' => 2,
            'total_gedung' => 1,
        ];

        $dummyKomposisi = [
            ['label' => 'Reguler Non-KIPK', 'jumlah' => 200, 'persen' => '52.6%', 'dot' => 'bg-secondary'],
            ['label' => 'Mahasiswa KIP-K', 'jumlah' => 180, 'persen' => '47.4%', 'dot' => 'bg-[#2E9E5B]'],
        ];

        $view = $this->view('dashboard.index', $this->defaultViewData([
            'gedungList' => $dummyGedungList,
            'kpi' => $dummyKpi,
            'komposisi' => $dummyKomposisi,
            'klasterFilter' => 'all',
        ]));

        $view->assertSee('Gedung Menpera (D)');
        $view->assertSee('Total Unit Bed Resmi');
        $view->assertSee('95%');
    }

    /**
     * Test 6: Memastikan view Manajemen Gedung ter-render dengan metrik operasional dan tombol aksi.
     */
    public function test_gedung_index_view_compiles_with_operational_metrics(): void
    {
        $dummyGedungs = collect([]);
        $dummyMetrics = [
            'total_gedung' => 10,
            'gedung_aktif' => 10,
            'total_lantai' => 42,
            'total_kamar' => 798,
            'kamar_tersedia' => 798,
            'total_bed' => 2500,
            'bed_terisi' => 0,
            'okupansi_persen' => 0,
        ];

        $view = $this->view('gedung.index', $this->defaultViewData([
            'gedungs' => $dummyGedungs,
            'metrics' => $dummyMetrics,
        ]));

        $view->assertSee('Manajemen Gedung');
        $view->assertSee('Tambah Gedung Baru');
        $view->assertSee('798');
    }

    /**
     * Test 7: Memastikan rute HTTP publik (/login dan /register) merespons dengan status 200 tanpa error Vite.
     */
    public function test_http_auth_routes_return_ok_status(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);

        $registerResponse = $this->get('/register');
        $registerResponse->assertStatus(200);
    }

    /**
     * Test 8: Memastikan proteksi rute berjalan (pengguna belum login dilarang masuk ke dashboard).
     */
    public function test_protected_routes_require_authentication(): void
    {
        $response = $this->get('/dashboard/okupansi');
        $response->assertRedirect('/login');

        $gedungResponse = $this->get('/gedung');
        $gedungResponse->assertRedirect('/login');
    }

    /**
     * Test 9: Memastikan Pengguna Authenticatable tidak memicu error remember_token.
     */
    public function test_pengguna_model_handles_remember_token_gracefully(): void
    {
        $pengguna = new \App\Models\Pengguna();
        $this->assertSame('', $pengguna->getRememberTokenName());
        $this->assertNull($pengguna->getRememberToken());

        // setRememberToken harus no-op dan tidak mengotori dirty attributes
        $pengguna->setRememberToken('sample-token');
        $this->assertArrayNotHasKey('remember_token', $pengguna->getDirty());
    }
}
