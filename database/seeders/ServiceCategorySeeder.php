<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Стрижки та укладки',
                'description' => 'Професійні стрижки для чоловіків та жінок, укладки та стайлінг',
                'sort_order' => 1,
            ],
            [
                'name' => 'Фарбування',
                'description' => 'Фарбування волосся, мелірування, омбре, балаяж',
                'sort_order' => 2,
            ],
            [
                'name' => 'Догляд за обличчям',
                'description' => 'Чистка обличчя, маски, пілінги, масаж обличчя',
                'sort_order' => 3,
            ],
            [
                'name' => 'Манікюр та педікюр',
                'description' => 'Класичний та апаратний манікюр, педікюр, дизайн нігтів',
                'sort_order' => 4,
            ],
            [
                'name' => 'Макіяж',
                'description' => 'Денний та вечірній макіяж, весільний макіяж, навчання',
                'sort_order' => 5,
            ],
            [
                'name' => 'Брові та вії',
                'description' => 'Корекція та фарбування брів, нарощування вій, ламінування',
                'sort_order' => 6,
            ],
            [
                'name' => 'Епіляція',
                'description' => 'Воскова епіляція, шугарінг, лазерна епіляція',
                'sort_order' => 7,
            ],
            [
                'name' => 'Масаж',
                'description' => 'Релаксуючий масаж, лікувальний масаж, антицелюлітний',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Service categories created successfully!');
    }
}
