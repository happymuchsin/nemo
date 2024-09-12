<?php

namespace Database\Seeders;

use App\Models\MasterBox;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterBoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $s = MasterBox::whereIn('id', [1, 2])->first();
        if (!$s) {
            MasterBox::insert([
                'id' => 1,
                'master_counter_id' => 1,
                'name' => 'BOX 1 N',
                'rfid' => '00box1',
                'tipe' => 'NORMAL',
            ]);
            MasterBox::insert([
                'id' => 2,
                'master_counter_id' => 1,
                'name' => 'BOX 2 R',
                'rfid' => '00box2',
                'tipe' => 'RETURN',
            ]);
        }
    }
}
