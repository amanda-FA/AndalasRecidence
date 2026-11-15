<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_gets_role_after_registration(): void
    {
        Role::firstOrCreate([
            'name' => 'Client',
            'guard_name' => 'web',
        ]);

        $response = $this->post('/register', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();

        $user = User::where('email', 'client@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('Client'));
    }
}
