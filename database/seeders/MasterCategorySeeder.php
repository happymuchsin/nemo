<?php

namespace Database\Seeders;

use App\Models\MasterCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $s = [
            'JACKET',
            'VEST',
            'SHACKET',
            'PANT',
            'SKIRT',
            'SHORTS',
            'SHIRT',
        ];
        foreach ($s as $s) {
            MasterCategory::firstOrCreate([
                'name' => $s
            ]);
        }
    }
}
