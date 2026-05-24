<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Створюємо адміністратора
        $admin = User::firstOrCreate(
            ['email' => 'admin@beautysalon.com'],
            [
                'name' => 'Адміністратор',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Створюємо менеджера
        $manager = User::firstOrCreate(
            ['email' => 'manager@beautysalon.com'],
            [
                'name' => 'Менеджер',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('manager');

        // Створюємо майстра
        $master = User::firstOrCreate(
            ['email' => 'master@beautysalon.com'],
            [
                'name' => 'Майстер',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $master->assignRole('master');

        // Створюємо клієнта
        $client = User::firstOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Клієнт',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $client->assignRole('client');

        $this->command->info('Test users created successfully!');
        $this->command->info('All users have password: password');
    }
}
