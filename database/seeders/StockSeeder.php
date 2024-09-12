<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stock::firstOrCreate([
            'master_area_id' => 2,
            'master_counter_id' => 1,
            'master_box_id' => 1,
            'master_needle_id' => 1,
            'in' => 50,
            'out' => 0,
            'is_clear' => 'not',
        ]);
    }
}
