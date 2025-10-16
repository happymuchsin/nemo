<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'developer',
                'description' => 'DEVELOPER PROGRAM'
            ],
            [
                'name' => 'user',
                'description' => 'User'
            ],
            [
                'name' => 'report',
                'description' => 'Report'
            ],
            [
                'name' => 'stock',
                'description' => 'Stock'
            ],
            [
                'name' => 'warehouse',
                'description' => 'Warehouse'
            ],
            [
                'name' => 'dead-stock',
                'description' => 'Dead Stock'
            ],
            [
                'name' => 'adjustment',
                'description' => 'Adjustment'
            ],
            [
                'name' => 'approval',
                'description' => 'Approval'
            ],
            [
                'name' => 'admin',
                'description' => 'Master'
            ],
            [
                'name' => 'needle-control',
                'description' => 'Needle Control'
            ],
            [
                'name' => 'tools',
                'description' => 'Tools'
            ],
            [
                'name' => 'role',
                'description' => 'Tools Role'
            ],
            [
                'name' => 'permission',
                'description' => 'Tools Permission'
            ]
        ];

        foreach ($roles as $key => $role) {
            Role::firstOrCreate([
                'name'          => $role['name'],
                'description'   => $role['description']
            ]);
        }

        // DEVELOPER
        $permission = Permission::get();
        $role = Role::findByName('developer');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // USER
        $permission = Permission::whereIn('name', ['user-dashboard', 'admin-profile'])->get();
        $role = Role::findByName('user');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // REPORT
        $permission = Permission::whereIn('name', ['user-report', 'user-needle-report'])->get();
        $role = Role::findByName('report');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // STOCK
        $permission = Permission::whereIn('name', ['user-stock'])->get();
        $role = Role::findByName('stock');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // WAREHOUSE
        $permission = Permission::whereIn('name', ['user-warehouse'])->get();
        $role = Role::findByName('warehouse');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // DEAD STOCK
        $permission = Permission::whereIn('name', ['user-dead-stock'])->get();
        $role = Role::findByName('dead-stock');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // ADJUSTMENT
        $permission = Permission::whereIn('name', ['user-adjustment'])->get();
        $role = Role::findByName('adjustment');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // APPROVAL
        $permission = Permission::whereIn('name', ['user-approval'])->get();
        $role = Role::findByName('approval');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // ADMIN
        $permission = Permission::whereIn('name', ['admin-dashboard', 'admin-master', 'admin-master-holiday', 'admin-master-division', 'admin-master-position', 'admin-master-approval', 'admin-master-area', 'admin-master-line', 'admin-master-counter', 'admin-master-box', 'admin-master-placement', 'admin-master-status', 'admin-master-needle', 'admin-master-monthly-stock', 'admin-master-morning-stock', 'admin-master-buyer', 'admin-master-buyer', 'admin-master-category', 'admin-master-sub-category', 'admin-master-sample', 'admin-master-fabric', 'admin-master-style', 'admin-tools-user'])->get();
        $role = Role::findByName('admin');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // NEEDLE CONTROL
        $permission = Permission::whereIn('name', ['user-dashboard', 'admin-dashboard', 'admin-tools', 'admin-tools-needle-control'])->get();
        $role = Role::findByName('needle-control');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // TOOLS
        $permission = Permission::whereIn('name', ['admin-dashboard', 'admin-tools', 'admin-tools-activity-log'])->get();
        $role = Role::findByName('tools');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // ROLE
        $permission = Permission::whereIn('name', ['admin-dashboard', 'admin-tools', 'admin-tools-role'])->get();
        $role = Role::findByName('role');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }

        // PERMISSION
        $permission = Permission::whereIn('name', ['admin-dashboard', 'admin-tools', 'admin-tools-permission'])->get();
        $role = Role::findByName('permission');
        foreach ($permission as $p) {
            $role->givePermissionTo([(int) $p->id]);
        }
    }
}
