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
}
