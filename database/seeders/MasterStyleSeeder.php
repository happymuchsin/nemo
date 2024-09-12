<?php

namespace Database\Seeders;

use App\Models\MasterStyle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterStyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $s = MasterStyle::whereIn('id', [1, 2])->first();
        if (!$s) {
            MasterStyle::insert([
                'id' => 1,
                'master_buyer_id' => 2,
                'master_category_id' => 1,
                'master_sub_category_id' => 1,
                'master_sample_id' => 1,
                'master_fabric_id' => 1,
                'name' => 'ST24E001',
                'start' => '2024-09-08',
                'end' => '2024-09-14',
                'srf' => '001SEP24',
                'season' => 'SS24',
            ]);
            MasterStyle::insert([
                'id' => 2,
                'master_buyer_id' => 1,
                'master_category_id' => 2,
                'master_sub_category_id' => 2,
                'master_sample_id' => 2,
                'master_fabric_id' => 2,
                'name' => 'ST24ES1',
                'start' => '2024-09-15',
                'end' => '2024-09-21',
                'srf' => '002SEP24',
                'season' => 'SS24',
            ]);
        }
    }
}
