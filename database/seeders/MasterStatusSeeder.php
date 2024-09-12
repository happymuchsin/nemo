<?php

namespace Database\Seeders;

use App\Models\MasterStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterStatus::firstOrCreate([
            'id' => 1,
            'name' => 'DEFORMED',
            'created_by' => 'developer'
        ]);
        MasterStatus::firstOrCreate([
            'id' => 2,
            'name' => 'ROUTINE CHANGE',
            'created_by' => 'developer'
        ]);
        MasterStatus::firstOrCreate([
            'id' => 3,
            'name' => 'CHANGE STYLE OR MATERIAL',
            'created_by' => 'developer'
        ]);
        MasterStatus::firstOrCreate([
            'id' => 4,
            'name' => 'BROKEN MISSING FRAGMENT',
            'created_by' => 'developer'
        ]);
        MasterStatus::firstOrCreate([
            'id' => 5,
            'name' => 'REQUEST NEW',
            'created_by' => 'developer'
        ]);
        MasterStatus::firstOrCreate([
            'id' => 6,
            'name' => 'RETURN',
            'created_by' => 'developer'
        ]);
    }
}
