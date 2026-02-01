<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'unit_number' => 'ADM-01',
                'block' => 'A',
                'street' => 'Admin St',
                'user_type' => 'admin',
            ]
        );

        $this->command->info('Admin user created/verified: admin@example.com / password');
    }
}
