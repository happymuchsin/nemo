<?php

namespace Database\Seeders;

use App\Models\MasterSample;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $s = [
            'PROTO',
            'PROTO2',
            'SZ SET',
            'PPS',
            'TOP',
        ];
        foreach ($s as $s) {
            MasterSample::firstOrCreate([
                'name' => $s
            ]);
        }
    }
}
