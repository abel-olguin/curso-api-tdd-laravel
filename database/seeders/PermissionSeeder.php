<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'DELETE_RESTAURANT']);
        Permission::create(['name' => 'EDIT_RESTAURANT']);
        Permission::create(['name' => 'CREATE_RESTAURANT']);
    }
}
