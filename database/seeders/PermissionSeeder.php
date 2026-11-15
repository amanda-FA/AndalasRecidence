<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $rolePermissions = [
            'Client' => [
                'pendaftaran.create',
                'pembayaran.create',
                'penempatan.view',
                'kerusakan.create',
                'checkout.create',
                'surat_checkout.view',
                'surat_bebas_asrama.view',
            ],
            'Admin Layanan' => [
                'pendaftaran.manage',
                'bebas_asrama.verify',
            ],
            'Fasilitator' => [
                'gedung_kamar.manage',
                'penempatan.manage',
                'checkout.verify',
            ],
            'Teknisi' => [
                'kerusakan.manage',
            ],
            'GO' => [
                'kamar.inspect',
            ],
            'Pimpinan' => [
                'dashboard.view',
                'kerusakan.monitor',
                'teknisi.monitor',
                'bebas_asrama.report',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::findByName($roleName, 'web');

            foreach ($permissions as $permissionName) {
                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            }

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
