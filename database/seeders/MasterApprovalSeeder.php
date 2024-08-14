<?php

namespace Database\Seeders;

use App\Models\MasterApproval;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $u = User::where('name', 'developer')->first();
        MasterApproval::firstOrCreate([
            'user_id' => $u->id,
        ]);
    }
}
