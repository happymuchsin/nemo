<?php

namespace Database\Seeders;

use App\Models\MasterBuyer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterBuyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterBuyer::firstOrCreate([
            'name' => 'CK',
        ]);
        MasterBuyer::firstOrCreate([
            'name' => 'FJALL RAVEEN',
        ]);
    }
}
