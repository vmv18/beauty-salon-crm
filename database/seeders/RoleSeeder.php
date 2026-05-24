<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Створюємо ролі
        $roles = [
            [
                'name' => 'admin',
                'guard_name' => 'web',
            ],
            [
                'name' => 'manager',
                'guard_name' => 'web',
            ],
            [
                'name' => 'master',
                'guard_name' => 'web',
            ],
            [
                'name' => 'client',
                'guard_name' => 'web',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('Roles created successfully!');
    }
}
