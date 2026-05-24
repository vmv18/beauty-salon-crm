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
        // Спочатку створюємо ролі
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            EmployeeSeeder::class,
            EmployeePhotoSeeder::class,
            GallerySeeder::class,
        ]);
    }
}
