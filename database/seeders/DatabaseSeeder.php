<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Creates the default admin user for the DOST SDN Logbook system.
     */
    public function run(): void
    {
        // Default admin account — change password immediately after first login
        User::firstOrCreate(
            ['email' => 'admin@dost-sdn.gov.ph'],
            [
                'name'     => 'DOST SDN Admin',
                'password' => Hash::make('Admin@2026!'),
            ]
        );

        $this->command->info('Admin user created:');
        $this->command->info('  Email   : admin@dost-sdn.gov.ph');
        $this->command->info('  Password: Admin@2026!');
        $this->command->warn('  ⚠  Change the default password after first login!');
    }
}
