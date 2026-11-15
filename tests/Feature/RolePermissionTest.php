<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_client_permissions(): void
    {
        $role = Role::findByName('Client', 'web');

        $this->assertTrue($role->hasPermissionTo('pendaftaran.create'));
        $this->assertFalse($role->hasPermissionTo('pendaftaran.manage'));
    }

    public function test_admin_layanan_permissions(): void
    {
        $role = Role::findByName('Admin Layanan', 'web');

        $this->assertTrue($role->hasPermissionTo('pendaftaran.manage'));
        $this->assertTrue($role->hasPermissionTo('bebas_asrama.verify'));
    }

    public function test_fasilitator_permissions(): void
    {
        $role = Role::findByName('Fasilitator', 'web');

        $this->assertTrue($role->hasPermissionTo('penempatan.manage'));
    }

    public function test_teknisi_permissions(): void
    {
        $role = Role::findByName('Teknisi', 'web');

        $this->assertTrue($role->hasPermissionTo('kerusakan.manage'));
    }

    public function test_go_permissions(): void
    {
        $role = Role::findByName('GO', 'web');

        $this->assertTrue($role->hasPermissionTo('kamar.inspect'));
    }

    public function test_pimpinan_permissions(): void
    {
        $role = Role::findByName('Pimpinan', 'web');

        $this->assertTrue($role->hasPermissionTo('dashboard.view'));
    }
}
