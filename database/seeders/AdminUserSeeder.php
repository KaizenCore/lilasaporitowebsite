<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Lila (FrizzBoss Admin)',
            'email' => 'lila@frizzboss.com',
            'password' => Hash::make('password'), // Change this in production!
            'phone_number' => null,
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user created: lila@frizzboss.com / password');
    }
}
