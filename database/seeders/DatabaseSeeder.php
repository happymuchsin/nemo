<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\MasterApproval;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            // MasterDivisionSeeder::class,
            // MasterPositionSeeder::class,
            // MasterStatusSeeder::class,
            // MasterAreaSeeder::class,
            // MasterLineSeeder::class,
            UserSeeder::class,
            // KaryawanSeeder::class,
            // MasterApprovalSeeder::class,
            // MasterNeedleSeeder::class,
            // MasterBuyerSeeder::class,
            // MasterCategorySeeder::class,
            // MasterSubCategorySeeder::class,
            // MasterSampleSeeder::class,
            // MasterFabricSeeder::class,
        ]);

        if (env('APP_ENV') == 'local') {
            $this->call([
                // MasterCounterSeeder::class,
                // MasterBoxSeeder::class,
                // MasterStyleSeeder::class,
                // StockSeeder::class,
            ]);
        }
    }
}
