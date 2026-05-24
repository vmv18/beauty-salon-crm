<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Отримуємо категорії
        $haircutCategory = ServiceCategory::where('name', 'Стрижки та укладки')->first();
        $coloringCategory = ServiceCategory::where('name', 'Фарбування')->first();
        $facialCategory = ServiceCategory::where('name', 'Догляд за обличчям')->first();
        $manicureCategory = ServiceCategory::where('name', 'Манікюр та педікюр')->first();
        $makeupCategory = ServiceCategory::where('name', 'Макіяж')->first();
        $browsCategory = ServiceCategory::where('name', 'Брові та вії')->first();
        $epilationCategory = ServiceCategory::where('name', 'Епіляція')->first();
        $massageCategory = ServiceCategory::where('name', 'Масаж')->first();

        $services = [
            // Стрижки та укладки
            [
                'category_id' => $haircutCategory->id,
                'name' => 'Жіноча стрижка',
                'description' => 'Професійна стрижка з консультацією та укладкою',
                'duration' => 60,
                'price' => 800.00,
                'is_active' => true,
            ],
            [
                'category_id' => $haircutCategory->id,
                'name' => 'Чоловіча стрижка',
                'description' => 'Класична чоловіча стрижка з укладкою',
                'duration' => 30,
                'price' => 400.00,
                'is_active' => true,
            ],
            [
                'category_id' => $haircutCategory->id,
                'name' => 'Дитяча стрижка',
                'description' => 'Стрижка для дітей до 12 років',
                'duration' => 30,
                'price' => 300.00,
                'is_active' => true,
            ],
            [
                'category_id' => $haircutCategory->id,
                'name' => 'Укладка волосся',
                'description' => 'Укладка волосся феном та щіткою',
                'duration' => 45,
                'price' => 500.00,
                'is_active' => true,
            ],
            [
                'category_id' => $haircutCategory->id,
                'name' => 'Вечірня укладка',
                'description' => 'Складна вечірня укладка з укладанням локонів',
                'duration' => 90,
                'price' => 1200.00,
                'is_active' => true,
            ],

            // Фарбування
            [
                'category_id' => $coloringCategory->id,
                'name' => 'Повне фарбування',
                'description' => 'Повне фарбування волосся з консультацією',
                'duration' => 120,
                'price' => 1500.00,
                'is_active' => true,
            ],
            [
                'category_id' => $coloringCategory->id,
                'name' => 'Мелірування',
                'description' => 'Мелірування волосся (часткове)',
                'duration' => 150,
                'price' => 2000.00,
                'is_active' => true,
            ],
            [
                'category_id' => $coloringCategory->id,
                'name' => 'Омбре/Балаяж',
                'description' => 'Градієнтне фарбування омбре або балаяж',
                'duration' => 180,
                'price' => 2500.00,
                'is_active' => true,
            ],
            [
                'category_id' => $coloringCategory->id,
                'name' => 'Тонування',
                'description' => 'Тонування волосся без аміаку',
                'duration' => 60,
                'price' => 800.00,
                'is_active' => true,
            ],
            [
                'category_id' => $coloringCategory->id,
                'name' => 'Корекція коренів',
                'description' => 'Фарбування тільки коренів волосся',
                'duration' => 90,
                'price' => 1000.00,
                'is_active' => true,
            ],

            // Догляд за обличчям
            [
                'category_id' => $facialCategory->id,
                'name' => 'Чистка обличчя',
                'description' => 'Глибока чистка обличчя з масажем',
                'duration' => 60,
                'price' => 900.00,
                'is_active' => true,
            ],
            [
                'category_id' => $facialCategory->id,
                'name' => 'Пілінг обличчя',
                'description' => 'Хімічний пілінг для оновлення шкіри',
                'duration' => 45,
                'price' => 700.00,
                'is_active' => true,
            ],
            [
                'category_id' => $facialCategory->id,
                'name' => 'Маска для обличчя',
                'description' => 'Омолоджуюча або підживлююча маска',
                'duration' => 30,
                'price' => 500.00,
                'is_active' => true,
            ],
            [
                'category_id' => $facialCategory->id,
                'name' => 'Масаж обличчя',
                'description' => 'Релаксуючий масаж обличчя та шиї',
                'duration' => 30,
                'price' => 600.00,
                'is_active' => true,
            ],

            // Манікюр та педікюр
            [
                'category_id' => $manicureCategory->id,
                'name' => 'Класичний манікюр',
                'description' => 'Класичний обрізний манікюр з покриттям',
                'duration' => 60,
                'price' => 500.00,
                'is_active' => true,
            ],
            [
                'category_id' => $manicureCategory->id,
                'name' => 'Апаратний манікюр',
                'description' => 'Апаратний манікюр без обрізання',
                'duration' => 45,
                'price' => 600.00,
                'is_active' => true,
            ],
            [
                'category_id' => $manicureCategory->id,
                'name' => 'Покриття гель-лаком',
                'description' => 'Покриття нігтів гель-лаком з дизайном',
                'duration' => 90,
                'price' => 800.00,
                'is_active' => true,
            ],
            [
                'category_id' => $manicureCategory->id,
                'name' => 'Класичний педікюр',
                'description' => 'Педикюр з обробкою стоп та покриттям',
                'duration' => 90,
                'price' => 700.00,
                'is_active' => true,
            ],
            [
                'category_id' => $manicureCategory->id,
                'name' => 'Апаратний педікюр',
                'description' => 'Апаратний педікюр з покриттям',
                'duration' => 75,
                'price' => 800.00,
                'is_active' => true,
            ],

            // Макіяж
            [
                'category_id' => $makeupCategory->id,
                'name' => 'Денний макіяж',
                'description' => 'Легкий денний макіяж для щоденного використання',
                'duration' => 45,
                'price' => 600.00,
                'is_active' => true,
            ],
            [
                'category_id' => $makeupCategory->id,
                'name' => 'Вечірній макіяж',
                'description' => 'Яскравий вечірній макіяж з акцентом на очі',
                'duration' => 60,
                'price' => 900.00,
                'is_active' => true,
            ],
            [
                'category_id' => $makeupCategory->id,
                'name' => 'Весільний макіяж',
                'description' => 'Спеціальний весільний макіяж з пробним сеансом',
                'duration' => 120,
                'price' => 2000.00,
                'is_active' => true,
            ],

            // Брові та вії
            [
                'category_id' => $browsCategory->id,
                'name' => 'Корекція брів',
                'description' => 'Корекція форми брів воском або ниткою',
                'duration' => 30,
                'price' => 300.00,
                'is_active' => true,
            ],
            [
                'category_id' => $browsCategory->id,
                'name' => 'Фарбування брів',
                'description' => 'Фарбування брів фарбою',
                'duration' => 20,
                'price' => 250.00,
                'is_active' => true,
            ],
            [
                'category_id' => $browsCategory->id,
                'name' => 'Ламінування брів',
                'description' => 'Ламінування брів для фіксації форми',
                'duration' => 45,
                'price' => 800.00,
                'is_active' => true,
            ],
            [
                'category_id' => $browsCategory->id,
                'name' => 'Нарощування вій (класика)',
                'description' => 'Нарощування вій класичним методом',
                'duration' => 120,
                'price' => 1500.00,
                'is_active' => true,
            ],
            [
                'category_id' => $browsCategory->id,
                'name' => 'Корекція нарощених вій',
                'description' => 'Корекція нарощених вій (через 2-3 тижні)',
                'duration' => 60,
                'price' => 800.00,
                'is_active' => true,
            ],

            // Епіляція
            [
                'category_id' => $epilationCategory->id,
                'name' => 'Епіляція ніг (воск)',
                'description' => 'Воскова епіляція ніг повністю',
                'duration' => 60,
                'price' => 600.00,
                'is_active' => true,
            ],
            [
                'category_id' => $epilationCategory->id,
                'name' => 'Епіляція бікіні (воск)',
                'description' => 'Воскова епіляція зони бікіні',
                'duration' => 45,
                'price' => 500.00,
                'is_active' => true,
            ],
            [
                'category_id' => $epilationCategory->id,
                'name' => 'Шугарінг ніг',
                'description' => 'Епіляція ніг методом шугарінгу',
                'duration' => 60,
                'price' => 700.00,
                'is_active' => true,
            ],

            // Масаж
            [
                'category_id' => $massageCategory->id,
                'name' => 'Релаксуючий масаж',
                'description' => 'Загальний релаксуючий масаж тіла (60 хв)',
                'duration' => 60,
                'price' => 1000.00,
                'is_active' => true,
            ],
            [
                'category_id' => $massageCategory->id,
                'name' => 'Антицелюлітний масаж',
                'description' => 'Антицелюлітний масаж проблемних зон',
                'duration' => 60,
                'price' => 1200.00,
                'is_active' => true,
            ],
            [
                'category_id' => $massageCategory->id,
                'name' => 'Лікувальний масаж спини',
                'description' => 'Лікувальний масаж спини та шиї',
                'duration' => 45,
                'price' => 800.00,
                'is_active' => true,
            ],
        ];

        // Мапінг назв послуг до зображень
        $serviceImageMap = [
            'Жіноча стрижка' => 'services/strizhka-ukladka.png',
            'Чоловіча стрижка' => 'services/cholovicha-strizhka.png',
            'Дитяча стрижка' => 'services/strizhka-ukladka.png',
            'Укладка волосся' => 'services/strizhka-ukladka.png',
            'Вечірня укладка' => 'services/strizhka-ukladka.png',
            'Повне фарбування' => 'services/farbuvannya.png',
            'Мелірування' => 'services/meliruvannya.png',
            'Омбре/Балаяж' => 'services/ombre-balayazh.png',
            'Тонування' => 'services/farbuvannya.png',
            'Корекція коренів' => 'services/farbuvannya.png',
            'Чистка обличчя' => 'services/chystka-oblychchya.png',
            'Пілінг обличчя' => 'services/doglyad-oblychchya.png',
            'Маска для обличчя' => 'services/doglyad-oblychchya.png',
            'Масаж обличчя' => 'services/doglyad-oblychchya.png',
            'Класичний манікюр' => 'services/manikyur.png',
            'Апаратний манікюр' => 'services/aparatnyy-manikyur.png',
            'Покриття гель-лаком' => 'services/pokryttya-gel-lakom.png',
            'Класичний педікюр' => 'services/pedykyur.png',
            'Апаратний педікюр' => 'services/pedykyur.png',
            'Денний макіяж' => 'services/makiyazh.png',
            'Вечірній макіяж' => 'services/vechirniy-makiyazh.png',
            'Весільний макіяж' => 'services/makiyazh.png',
            'Корекція брів' => 'services/brovy-vii.png',
            'Фарбування брів' => 'services/brovy-vii.png',
            'Ламінування брів' => 'services/laminuvannya-briv.png',
            'Нарощування вій (класика)' => 'services/naroshchuvannya-vii.png',
            'Корекція нарощених вій' => 'services/naroshchuvannya-vii.png',
            'Релаксуючий масаж' => 'services/relaksuyuchyy-masazh.png',
            'Антицелюлітний масаж' => 'services/relaksuyuchyy-masazh.png',
            'Лікувальний масаж спини' => 'services/relaksuyuchyy-masazh.png',
        ];

        foreach ($services as $serviceData) {
            // Додаємо зображення, якщо воно є в мапінгу та файл існує
            if (isset($serviceImageMap[$serviceData['name']]) && Storage::disk('public')->exists($serviceImageMap[$serviceData['name']])) {
                $serviceData['image'] = $serviceImageMap[$serviceData['name']];
            }

            $service = Service::firstOrCreate(
                [
                    'category_id' => $serviceData['category_id'],
                    'name' => $serviceData['name'],
                ],
                $serviceData
            );

            // Оновлюємо зображення, якщо воно не було встановлено
            if (!$service->image && isset($serviceImageMap[$serviceData['name']]) && Storage::disk('public')->exists($serviceImageMap[$serviceData['name']])) {
                $service->update(['image' => $serviceImageMap[$serviceData['name']]]);
            }
        }

        $this->command->info('Services created successfully!');
    }
}
