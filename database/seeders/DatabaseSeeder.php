<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Fork-friendly seeder.
     * Run: php artisan db:seed
     * All seeders are idempotent — safe to run multiple times.
     */
    public function run(): void
    {
        $this->command->info('=== FileTrack System Seeder ===');

        $this->call(DepartmentSeeder::class);   // Creates 5 departments
        $this->call(DesignationSeeder::class);  // Creates designations per dept
        $this->call(SuperAdminSeeder::class);   // superadmin@filetrack.local
        $this->call(SampleUsersSeeder::class);  // admin + 2 users

        $this->command->info('');
        $this->command->info('=== Seeding complete. Login credentials: ===');
        $this->command->line('  Super Admin: superadmin@filetrack.local / Admin@1234');
        $this->command->line('  Admin:       admin@filetrack.local      / Admin@1234');
        $this->command->line('  User 1:      user1@filetrack.local      / User@1234');
        $this->command->line('  User 2:      user2@filetrack.local      / User@1234');
    }
}
