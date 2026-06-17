<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        Designation::insert([
            [
                'department_id' => 1,
                'name' => 'Department Admin',
                'is_active' => 1,
            ],
            [
                'department_id' => 1,
                'name' => 'Officer',
                'is_active' => 1,
            ],
            [
                'department_id' => 1,
                'name' => 'Clerk',
                'is_active' => 1,
            ],
        ]);
    }
}