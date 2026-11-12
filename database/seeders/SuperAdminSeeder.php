<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'aiawards'],
            [
                'name' => 'Super Admin',
                'password' => 'ai^&awa!Koi$t!a',
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
            ],
        );
    }
}
