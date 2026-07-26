<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Clean seeder — creates ONLY the Super Admin account.
 * No sample departments, designations, users, files or movements.
 * Run:  php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== FileTrack System Seeder ===');

        // Create Super Admin (idempotent — safe to run multiple times)
        User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('Password@123'),
                'role'              => 'super_admin',
                'department_id'     => null,
                'designation_id'    => null,
                'is_active'         => true,
                'can_create_file'   => false,
                'must_change_password' => false,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('');
        $this->command->info('=== Seeding complete ===');
        $this->command->line('  Super Admin: superadmin@example.com / Password@123');
    }
}
