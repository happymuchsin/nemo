<?php

namespace Database\Seeders;

use App\Models\MasterSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $s = [
            'PADDED',
            'DUCK DOWN',
            'SYNTHETIC DOWN',
            '2 LAY SEAMSEAL',
            '3 LAYER SEAMSEAL',
            'WASH',
            'KNITTED',
            'SOFTSHELL',
        ];
        foreach ($s as $s) {
            MasterSubCategory::firstOrCreate([
                'name' => $s
            ]);
        }
    }
}
