<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Мапінг назв категорій до зображень
        $categoryImageMap = [
            'Стрижки та укладки' => 'service-categories/strizhky-ukladky.png',
            'Фарбування' => 'service-categories/farbuvannya.png',
            'Манікюр та педікюр' => 'service-categories/manikyur-pedykyur.png',
            'Макіяж' => 'service-categories/makiyazh.png',
        ];

        foreach ($categoryImageMap as $categoryName => $imagePath) {
            $category = ServiceCategory::where('name', $categoryName)->first();
            
            if ($category) {
                // Перевіряємо, чи файл існує
                if (Storage::disk('public')->exists($imagePath)) {
                    // Оновлюємо зображення, якщо воно не встановлено або відрізняється
                    if (!$category->image || $category->image !== $imagePath) {
                        $category->update(['image' => $imagePath]);
                        $this->command->info("Оновлено зображення для категорії: {$categoryName}");
                    } else {
                        $this->command->warn("Зображення вже встановлено для категорії: {$categoryName}");
                    }
                } else {
                    $this->command->error("Файл не знайдено: {$imagePath}");
                }
            } else {
                $this->command->error("Категорія не знайдена: {$categoryName}");
            }
        }

        $this->command->info('Зображення категорій послуг успішно оновлено!');
    }
}
