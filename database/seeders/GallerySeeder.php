<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Мапінг файлів до назв та описів
        $galleryItems = [
            [
                'file' => 'gallery/strizhka-before-after.png',
                'title' => 'Стрижка та укладка',
                'description' => 'Професійна стрижка з укладкою',
                'sort_order' => 1,
            ],
            [
                'file' => 'gallery/farbuvannya-before-after.png',
                'title' => 'Фарбування волосся',
                'description' => 'Сучасне фарбування з доглядом',
                'sort_order' => 2,
            ],
            [
                'file' => 'gallery/manikyur-before-after.png',
                'title' => 'Манікюр',
                'description' => 'Класичний та дизайнерський манікюр',
                'sort_order' => 3,
            ],
            [
                'file' => 'gallery/makiyazh-before-after.png',
                'title' => 'Макіяж',
                'description' => 'Вечірній та денний макіяж',
                'sort_order' => 4,
            ],
            [
                'file' => 'gallery/doglyad-oblychchya-results.png',
                'title' => 'Догляд за обличчям',
                'description' => 'Професійний догляд за шкірою',
                'sort_order' => 5,
            ],
            [
                'file' => 'gallery/naroshchuvannya-volossya-results.png',
                'title' => 'Нарощування волосся',
                'description' => 'Результати нарощування волосся',
                'sort_order' => 6,
            ],
        ];

        foreach ($galleryItems as $item) {
            // Перевіряємо, чи файл існує
            if (Storage::disk('public')->exists($item['file'])) {
                // Перевіряємо, чи запис вже існує
                $existing = Gallery::where('image', $item['file'])->first();
                
                if (!$existing) {
                    Gallery::create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'image' => $item['file'],
                        'sort_order' => $item['sort_order'],
                        'is_active' => true,
                    ]);
                    
                    $this->command->info("Додано зображення: {$item['title']}");
                } else {
                    $this->command->warn("Зображення вже існує: {$item['title']}");
                }
            } else {
                $this->command->error("Файл не знайдено: {$item['file']}");
            }
        }

        $this->command->info('Галерея успішно заповнена!');
    }
}
