<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $role = Role::firstOrCreate(['nama_role' => 'Client']);

        return [
            'id_role' => $role->id_role,
            'nama_lengkap' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'no_hp' => fake()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'status_aktif' => 1,
        ];
    }
}
