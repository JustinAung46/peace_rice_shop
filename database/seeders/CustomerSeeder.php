<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::updateOrCreate(
            ['name' => 'Walk-in Customer'],
            [
                'phone' => null,
                'credit_balance' => 0,
            ]
        );
    }
}
