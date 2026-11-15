<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_expected_roles_exist(): void
    {
        $expectedRoles = [
            'Client',
            'Admin Layanan',
            'Fasilitator',
            'Teknisi',
            'GO',
            'Pimpinan',
        ];

        foreach ($expectedRoles as $roleName) {
            $this->assertDatabaseHas('role', [
                'nama_role' => $roleName,
            ]);
        }
    }

    public function test_user_has_correct_role(): void
    {
        $pimpinan = Pengguna::where('email', 'pimpinan@unand.ac.id')->firstOrFail();
        $this->assertTrue($pimpinan->hasRole('Pimpinan'));
        $this->assertFalse($pimpinan->hasRole('Client'));

        $fasilitator = Pengguna::where('email', 'fasilitator@unand.ac.id')->firstOrFail();
        $this->assertTrue($fasilitator->hasRole('Fasilitator'));

        $client = Pengguna::where('email', 'client@unand.ac.id')->firstOrFail();
        $this->assertTrue($client->hasRole('Client'));
        $this->assertNotNull($client->client);
    }
}
