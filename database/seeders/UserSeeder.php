<?php

namespace Database\Seeders;

use App\Enums\Roles;
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
        $user = User::factory()->create([
            'name'      => 'User',
            'last_name' => 'Test',
            'email'     => 'example@example.com',
        ]);//
        $user->assignRole(Roles::USER->value);
        /*$user->givePermissionTo([
            'DELETE_RESTAURANT',
            'EDIT_RESTAURANT',
            'CREATE_RESTAURANT'
        ]);*/
        $admin = User::factory()->create([
            'name'      => 'Admin',
            'last_name' => 'Test',
            'email'     => 'admin@admin.com',
        ]);//
        $admin->assignRole(Roles::ADMIN->value);
    }
}
