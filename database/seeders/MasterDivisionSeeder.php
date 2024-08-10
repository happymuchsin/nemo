<?php

namespace Database\Seeders;

use App\Models\MasterDivision;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterDivision::firstOrCreate([
            'id' => 1,
            'name' => 'DEVELOPER',
        ]);
        MasterDivision::firstOrCreate([
            'id' => 2,
            'name' => 'PRODUCTION',
        ]);
    }
}
