<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $memberRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'member']);

        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@thetrader.id'],
            [
                'name' => 'Admin TheTrader',
                'password' => bcrypt('password'), // Default password
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole($adminRole);

        // Create Member User
        $memberUser = User::firstOrCreate(
            ['email' => 'member@thetrader.id'],
            [
                'name' => 'Member TheTrader',
                'password' => bcrypt('password'), // Default password
                'email_verified_at' => now(),
            ]
        );
        $memberUser->assignRole($memberRole);

        // Seed Packages
        $this->call(PackageSeeder::class);
    }
}
