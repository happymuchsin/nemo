<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'id' => 1,
            'username' => 'developer',
            'name' => 'DEVELOPER',
            'master_division_id' => 1,
            'master_position_id' => 1,
            'email' => 'happymuchsin@gmail.com',
            'password' => bcrypt('qwe123'),
        ]);

        // $u = User::find(1);
        // $u->assignRole('developer');
    }
}
