<?php

namespace Database\Seeders;

use App\Models\TimeLimit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $time_limit = TimeLimit::where('tipe', 'needle')->first();
        if (!$time_limit) {
            TimeLimit::create([
                'tipe' => 'needle',
                'waktu' => 40,
            ]);
        }
    }
}
