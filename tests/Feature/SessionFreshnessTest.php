<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionFreshnessTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): Pengguna
    {
        return Pengguna::create([
            'id_role' => Role::firstOrCreate(['nama_role' => $role])->id_role,
            'nama_lengkap' => 'Session Test', 'email' => 'session@example.com', 'username' => 'session_test',
            'password' => 'Password123!', 'status_aktif' => 1,
        ]);
    }

    public function test_login_and_authenticated_forms_are_not_stored_as_stale_pages(): void
    {
        $login = $this->get('/login')->assertOk();
        $this->assertTrue($login->headers->hasCacheControlDirective('no-store'));
        $this->assertTrue($login->headers->hasCacheControlDirective('private'));

        $this->actingAs($this->user('Client'));
        foreach (['penempatan.index', 'kerusakan.index'] as $route) {
            $page = $this->get(route($route))->assertOk();
            $this->assertTrue($page->headers->hasCacheControlDirective('no-store'));
            $this->assertTrue($page->headers->hasCacheControlDirective('private'));
        }
    }

    public function test_old_tab_token_is_rejected_after_login_and_current_token_allows_logout(): void
    {
        // Laravel skips CSRF in the testing environment; enable the real middleware check here.
        $this->app['env'] = 'local';
        $user = $this->user('Client');
        $oldToken = str_repeat('a', 40);
        $this->withSession(['_token' => $oldToken])->post('/login', [
            '_token' => $oldToken, 'email' => $user->email, 'password' => 'Password123!',
        ])->assertRedirect(route('pendaftaran.create'));
        $newToken = $this->app['session']->token();
        $this->assertNotSame($oldToken, $newToken);

        $this->post('/logout', ['_token' => $oldToken])->assertStatus(419);
        $this->assertAuthenticated();
        $this->assertSame($user->getAuthIdentifier(), auth()->id());
        $this->get(route('penempatan.index'))->assertOk();
        $this->post('/logout', ['_token' => $newToken])->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_stale_form_does_not_write_data_and_a_current_token_can_save(): void
    {
        $this->app['env'] = 'local';
        $user = $this->user('Fasilitator');
        $currentToken = str_repeat('b', 40);
        $form = ['nama_fasilitas' => 'Lampu lobi', 'lingkup' => 'gedung'];
        $this->actingAs($user)->withSession(['_token' => $currentToken])
            ->postJson(route('pilihan-fasilitas.store'), $form + ['_token' => str_repeat('c', 40)])->assertStatus(419);
        $this->assertDatabaseCount('fasilitas', 0);
        $this->postJson(route('pilihan-fasilitas.store'), $form + ['_token' => $currentToken])->assertCreated();
        $this->assertDatabaseCount('fasilitas', 1);
    }
}
