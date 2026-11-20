<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_gets_role_after_registration(): void
    {
        Role::firstOrCreate(['nama_role' => 'Client']);

        $response = $this->post('/register', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();

        $user = Pengguna::where('email', 'client@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('Client'));
        $this->assertNotNull($user->client);
    }

    public function test_client_login_opens_the_housing_registration_page(): void
    {
        $this->post('/register', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect(route('login'));

        $response = $this->post('/login', [
            'email' => 'client@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('pendaftaran.create'));
        $this->assertAuthenticated();
        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('Pendaftaran Hunian')
            ->assertDontSee("Let's get started");
    }

    public function test_existing_client_session_reaches_the_system_from_home_and_login(): void
    {
        $role = Role::create(['nama_role' => 'Client']);
        $user = Pengguna::create([
            'id_role' => $role->id_role,
            'nama_lengkap' => 'Test Client',
            'email' => 'client@example.com',
            'username' => 'test_client',
            'password' => 'Password123!',
            'status_aktif' => 1,
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()->assertSee('Buka Portal');

        $loginResponse = $this->get('/login');
        $loginResponse->assertRedirect(route('portal'));

        $this->get($loginResponse->headers->get('Location'))
            ->assertRedirect(route('pendaftaran.create'));
    }

    public function test_login_preserves_the_requested_client_page(): void
    {
        $role = Role::create(['nama_role' => 'Client']);
        Pengguna::create([
            'id_role' => $role->id_role,
            'nama_lengkap' => 'Test Client',
            'email' => 'client@example.com',
            'username' => 'test_client',
            'password' => 'Password123!',
            'status_aktif' => 1,
        ]);

        $this->withSession(['url.intended' => route('pembayaran.index')])
            ->post('/login', [
                'email' => 'client@example.com',
                'password' => 'Password123!',
            ])
            ->assertRedirect(route('pembayaran.index'));
    }
}
