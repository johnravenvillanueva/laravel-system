<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Main admin
        $admin = Admin::create([
            'email'         => 'admin@issang.test',
            'password_hash' => Hash::make('password'),
            'first_name'    => 'Ana',
            'last_name'     => 'Reyes',
            'phone'         => '0917 111 1111',
            'role'          => 'admin',
            'is_active'     => true,
        ]);

        // Staff members
        Admin::create([
            'email'         => 'ben@issang.test',
            'password_hash' => Hash::make('password'),
            'first_name'    => 'Ben',
            'last_name'     => 'Cruz',
            'phone'         => '0917 222 2222',
            'role'          => 'staff',
            'is_active'     => true,
            'created_by'    => $admin->admin_id,
        ]);

        Admin::create([
            'email'         => 'cara@issang.test',
            'password_hash' => Hash::make('password'),
            'first_name'    => 'Cara',
            'last_name'     => 'Lim',
            'phone'         => '0917 333 3333',
            'role'          => 'staff',
            'is_active'     => true,
            'created_by'    => $admin->admin_id,
        ]);
    }
}