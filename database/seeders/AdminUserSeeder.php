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
        User::firstOrCreate(
            ['account_id' => 777],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => Hash::make('1234'),
            ]
        );
    }
}
