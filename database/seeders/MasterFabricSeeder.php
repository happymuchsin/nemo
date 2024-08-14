<?php

namespace Database\Seeders;

use App\Models\MasterFabric;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterFabricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $s = [
            'NON STRETCH',
            'WVN',
            '2 LAY WVN',
            '3 LAY WVN',
            'JERSEY',
        ];
        foreach ($s as $s) {
            MasterFabric::firstOrCreate([
                'name' => $s
            ]);
        }
    }
}
