<?php

namespace Database\Seeders;

use App\Models\MasterPosition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterPosition::firstOrCreate([
            'id' => 1,
            'name' => 'DEVELOPER',
        ]);
        MasterPosition::firstOrCreate([
            'id' => 2,
            'name' => 'SEWING',
        ]);
    }
}
