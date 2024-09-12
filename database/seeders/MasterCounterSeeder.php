<?php

namespace Database\Seeders;

use App\Models\MasterCounter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterCounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $s = MasterCounter::where('id', 1)->first();
        if (!$s) {
            MasterCounter::insert([
                'id' => 1,
                'master_area_id' => 2,
                'name' => 'COUNTER 1',
            ]);
        }
    }
}
