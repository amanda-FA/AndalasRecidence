<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_admin_route(): void
    {
        $clientRole = Role::firstOrCreate(['nama_role' => 'Client']);
        Role::firstOrCreate(['nama_role' => 'Admin Layanan']);

        $user = User::factory()->create([
            'id_role' => $clientRole->id_role,
        ]);

        Route::middleware(['web', 'auth', 'role:Admin Layanan'])
            ->get('/test-admin-access', fn () => 'OK');

        $this->actingAs($user)
            ->get('/test-admin-access')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_route(): void
    {
        $adminRole = Role::firstOrCreate(['nama_role' => 'Admin Layanan']);

        $user = User::factory()->create([
            'id_role' => $adminRole->id_role,
        ]);

        Route::middleware(['web', 'auth', 'role:Admin Layanan'])
            ->get('/test-admin-access', fn () => 'OK');

        $this->actingAs($user)
            ->get('/test-admin-access')
            ->assertOk();
    }
}
