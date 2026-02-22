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
        User::updateOrCreate(
            ['account_id' => 999],
            [
                'name' => 'Admin',
                'email' => 'admin@peace.com',
                'role' => 'admin',
                'password' => Hash::make('9999'),
            ]
        );
    }
}
