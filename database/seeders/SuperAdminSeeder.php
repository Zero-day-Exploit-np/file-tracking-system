<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@fts.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'department_id' => 1,
            'is_active' => 1,
        ]);
    }
}