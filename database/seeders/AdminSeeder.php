<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Management;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create or update default admin
        Management::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@studynest.com',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'department' => 'Management',
                'work_location' => 'Head Office',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Admin created successfully!');
        $this->command->info('Username: admin');
        $this->command->info('Password: admin123');
    }
}
