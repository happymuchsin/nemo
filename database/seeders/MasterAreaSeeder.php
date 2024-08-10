<?php

namespace Database\Seeders;

use App\Models\MasterArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterArea::firstOrCreate([
            'id' => 1,
            'name' => 'SAMPLE ROOM',
            'created_by' => 'developer'
        ]);
        MasterArea::firstOrCreate([
            'id' => 2,
            'name' => 'PRODUCTION 1',
            'created_by' => 'developer'
        ]);
        MasterArea::firstOrCreate([
            'id' => 3,
            'name' => 'PRODUCTION 2',
            'created_by' => 'developer'
        ]);
    }
}
