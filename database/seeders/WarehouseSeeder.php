<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Warehouse::create([
            'name' => 'Shop 1 (Main)',
            'location' => 'Main Street'
        ]);

        \App\Models\Warehouse::create([
            'name' => 'Warehouse 2 (Storage)',
            'location' => 'Industrial Zone'
        ]);
    }
}
