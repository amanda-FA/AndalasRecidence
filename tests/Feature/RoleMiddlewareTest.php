<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_admin_route(): void
    {
        Role::firstOrCreate([
            'name' => 'Client',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create();
        $user->assignRole('Client');

        Route::middleware(['web', 'auth', 'role:Admin Layanan'])
            ->get('/test-admin-access', fn () => 'OK');

        $this->actingAs($user)
            ->get('/test-admin-access')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_route(): void
    {
        Role::firstOrCreate([
            'name' => 'Admin Layanan',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create();
        $user->assignRole('Admin Layanan');

        Route::middleware(['web', 'auth', 'role:Admin Layanan'])
            ->get('/test-admin-access', fn () => 'OK');

        $this->actingAs($user)
            ->get('/test-admin-access')
            ->assertOk();
    }
}
