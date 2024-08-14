<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // USER
            [
                'name' => 'user-dashboard',
                'display_name' => 'user-dashboard',
                'description' => 'Dashboard User'
            ],
            [
                'name' => 'user-report',
                'display_name' => 'user-report',
                'description' => 'Report'
            ],
            [
                'name' => 'user-needle-report',
                'display_name' => 'user-needle-report',
                'description' => 'Needle Report'
            ],
            [
                'name' => 'user-stock',
                'display_name' => 'user-stock',
                'description' => 'Stock'
            ],
            [
                'name' => 'user-approval',
                'display_name' => 'user-approval',
                'description' => 'Approval'
            ],
            // ADMIN
            // MASTER
            [
                'name' => 'admin-dashboard',
                'display_name' => 'admin-dashboard',
                'description' => 'Dashboard Admin'
            ],
            [
                'name' => 'admin-master',
                'display_name' => 'admin-master',
                'description' => 'Master'
            ],
            [
                'name' => 'admin-master-division',
                'display_name' => 'admin-master-division',
                'description' => 'Master Division'
            ],
            [
                'name' => 'admin-master-position',
                'display_name' => 'admin-master-position',
                'description' => 'Master Position'
            ],
            [
                'name' => 'admin-master-approval',
                'display_name' => 'admin-master-approval',
                'description' => 'Master Approval'
            ],
            [
                'name' => 'admin-master-area',
                'display_name' => 'admin-master-area',
                'description' => 'Master Area'
            ],
            [
                'name' => 'admin-master-line',
                'display_name' => 'admin-master-line',
                'description' => 'Master Line'
            ],
            [
                'name' => 'admin-master-counter',
                'display_name' => 'admin-master-counter',
                'description' => 'Master Counter'
            ],
            [
                'name' => 'admin-master-box',
                'display_name' => 'admin-master-box',
                'description' => 'Master Box'
            ],
            [
                'name' => 'admin-master-placement',
                'display_name' => 'admin-master-placement',
                'description' => 'Master Placement'
            ],
            [
                'name' => 'admin-master-status',
                'display_name' => 'admin-master-status',
                'description' => 'Master Status'
            ],
            [
                'name' => 'admin-master-needle',
                'display_name' => 'admin-master-needle',
                'description' => 'Master Needle'
            ],
            [
                'name' => 'admin-master-buyer',
                'display_name' => 'admin-master-buyer',
                'description' => 'Master Buyer'
            ],
            [
                'name' => 'admin-master-category',
                'display_name' => 'admin-master-category',
                'description' => 'Master Category'
            ],
            [
                'name' => 'admin-master-sub-category',
                'display_name' => 'admin-master-sub-category',
                'description' => 'Master Sub Category'
            ],
            [
                'name' => 'admin-master-sample',
                'display_name' => 'admin-master-sample',
                'description' => 'Master Sample'
            ],
            [
                'name' => 'admin-master-fabric',
                'display_name' => 'admin-master-fabric',
                'description' => 'Master Fabric'
            ],
            [
                'name' => 'admin-master-style',
                'display_name' => 'admin-master-style',
                'description' => 'Master Style'
            ],
            // TOOLS
            [
                'name' => 'admin-tools',
                'display_name' => 'admin-tools',
                'description' => 'Admin Tools'
            ],
            [
                'name' => 'admin-tools-user',
                'display_name' => 'admin-tools-user',
                'description' => 'Master Data User'
            ],
            [
                'name' => 'admin-tools-activity-log',
                'display_name' => 'admin-tools-activity-log',
                'description' => 'Tools Activity Log'
            ],
            [
                'name' => 'admin-tools-permission',
                'display_name' => 'admin-tools-permission',
                'description' => 'Tools Permission'
            ],
            [
                'name' => 'admin-tools-role',
                'display_name' => 'admin-tools-role',
                'description' => 'Tools Role'
            ],
            // PROFILE
            [
                'name' => 'admin-profile',
                'display_name' => 'admin-profile',
                'description' => 'Profile'
            ],
        ];

        foreach ($permissions as $key => $permission) {
            Permission::firstOrCreate([
                'name'          => $permission['name'],
                'display_name'  => $permission['display_name'],
                'description'   => $permission['description']
            ]);
        }

        Artisan::call('cache:forget spatie.permission.cache');
    }
}
