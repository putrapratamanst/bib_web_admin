<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@bib.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@bib.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create Approver User
        User::updateOrCreate(
            ['email' => 'approver@bib.com'],
            [
                'name' => 'Approver User',
                'email' => 'approver@bib.com',
                'password' => Hash::make('password'),
                'role' => 'approver',
            ]
        );

        $this->command->info('Default users created successfully!');
        $this->command->info('Admin: admin@bib.com / password');
        $this->command->info('Approver: approver@bib.com / password');
    }
}
