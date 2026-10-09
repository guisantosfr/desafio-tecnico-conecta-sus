<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HealthUnit;

class HealthUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'UBS Central', 'city' => 'São Paulo'],
            ['name' => 'UBS Zona Norte', 'city' => 'São Paulo'],
            ['name' => 'UBS Zona Sul', 'city' => 'São Paulo'],
            ['name' => 'UBS Zona Leste', 'city' => 'São Paulo'],
            ['name' => 'UBS Zona Oeste', 'city' => 'São Paulo'],
        ];

        foreach ($units as $unit) {
            HealthUnit::firstOrCreate($unit);
        }
    }
}
