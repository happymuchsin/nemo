<?php

namespace Database\Seeders;

use App\Models\MasterLine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterLine::firstOrCreate([
            'id' => 1,
            'master_area_id' => 2,
            'name' => 'LINE ACENG',
            'created_by' => 'developer',
        ]);
        MasterLine::firstOrCreate([
            'id' => 2,
            'master_area_id' => 2,
            'name' => 'LINE OTONG',
            'created_by' => 'developer',
        ]);
        MasterLine::firstOrCreate([
            'id' => 3,
            'master_area_id' => 2,
            'name' => 'LINE YANTI',
            'created_by' => 'developer',
        ]);
        MasterLine::firstOrCreate([
            'id' => 4,
            'master_area_id' => 2,
            'name' => 'LINE SUNARTI',
            'created_by' => 'developer',
        ]);
        MasterLine::firstOrCreate([
            'id' => 5,
            'master_area_id' => 2,
            'name' => 'LINE SMS MURNI',
            'created_by' => 'developer',
        ]);
        MasterLine::firstOrCreate([
            'id' => 6,
            'master_area_id' => 2,
            'name' => 'LINE SMS YANTI',
            'created_by' => 'developer',
        ]);
    }
}
