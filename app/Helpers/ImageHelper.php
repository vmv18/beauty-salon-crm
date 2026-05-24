<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Отримати зображення послуги або дефолтне
     */
    public static function getServiceImage($service, $defaultPath = null)
    {
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            return Storage::url($service->image);
        }

        // Мапінг назв послуг до зображень
        $serviceImageMap = [
            'Жіноча стрижка' => 'services/strizhka-ukladka.png',
            'Чоловіча стрижка' => 'services/cholovicha-strizhka.png',
            'Укладка волосся' => 'services/strizhka-ukladka.png',
            'Вечірня укладка' => 'services/strizhka-ukladka.png',
            'Повне фарбування' => 'services/farbuvannya.png',
            'Мелірування' => 'services/meliruvannya.png',
            'Омбре/Балаяж' => 'services/ombre-balayazh.png',
            'Тонування' => 'services/farbuvannya.png',
            'Корекція коренів' => 'services/farbuvannya.png',
            'Класичний манікюр' => 'services/manikyur.png',
            'Апаратний манікюр' => 'services/aparatnyy-manikyur.png',
            'Покриття гель-лаком' => 'services/pokryttya-gel-lakom.png',
            'Класичний педікюр' => 'services/pedykyur.png',
            'Апаратний педікюр' => 'services/pedykyur.png',
            'Денний макіяж' => 'services/makiyazh.png',
            'Вечірній макіяж' => 'services/vechirniy-makiyazh.png',
            'Весільний макіяж' => 'services/makiyazh.png',
            'Чистка обличчя' => 'services/chystka-oblychchya.png',
            'Пілінг обличчя' => 'services/doglyad-oblychchya.png',
            'Маска для обличчя' => 'services/doglyad-oblychchya.png',
            'Масаж обличчя' => 'services/doglyad-oblychchya.png',
            'Корекція брів' => 'services/brovy-vii.png',
            'Фарбування брів' => 'services/brovy-vii.png',
            'Ламінування брів' => 'services/laminuvannya-briv.png',
            'Нарощування вій (класика)' => 'services/naroshchuvannya-vii.png',
            'Корекція нарощених вій' => 'services/naroshchuvannya-vii.png',
            'Релаксуючий масаж' => 'services/relaksuyuchyy-masazh.png',
            'Антицелюлітний масаж' => 'services/relaksuyuchyy-masazh.png',
            'Лікувальний масаж спини' => 'services/relaksuyuchyy-masazh.png',
        ];

        $serviceName = $service->name;
        if (isset($serviceImageMap[$serviceName]) && Storage::disk('public')->exists($serviceImageMap[$serviceName])) {
            return Storage::url($serviceImageMap[$serviceName]);
        }

        // Якщо немає мапінгу, використовуємо зображення категорії
        if ($service->category) {
            $categoryImageMap = [
                'Стрижки та укладки' => 'service-categories/strizhky-ukladky.png',
                'Фарбування' => 'service-categories/farbuvannya.png',
                'Манікюр та педікюр' => 'service-categories/manikyur-pedykyur.png',
                'Макіяж' => 'service-categories/makiyazh.png',
            ];

            $categoryName = $service->category->name;
            if (isset($categoryImageMap[$categoryName]) && Storage::disk('public')->exists($categoryImageMap[$categoryName])) {
                return Storage::url($categoryImageMap[$categoryName]);
            }
        }

        return $defaultPath ?? Storage::url('services/strizhka-ukladka.png');
    }

    /**
     * Отримати зображення категорії послуги або дефолтне
     */
    public static function getCategoryImage($category, $defaultPath = null)
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            return Storage::url($category->image);
        }

        $categoryImageMap = [
            'Стрижки та укладки' => 'service-categories/strizhky-ukladky.png',
            'Фарбування' => 'service-categories/farbuvannya.png',
            'Манікюр та педікюр' => 'service-categories/manikyur-pedykyur.png',
            'Макіяж' => 'service-categories/makiyazh.png',
        ];

        if (isset($categoryImageMap[$category->name]) && Storage::disk('public')->exists($categoryImageMap[$category->name])) {
            return Storage::url($categoryImageMap[$category->name]);
        }

        return $defaultPath ?? Storage::url('service-categories/strizhky-ukladky.png');
    }

    /**
     * Отримати фото майстра або дефолтне
     */
    public static function getEmployeePhoto($employee, $defaultPath = null)
    {
        if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
            return Storage::url($employee->photo);
        }

        // Мапінг спеціалізацій до фото
        $specializationPhotoMap = [
            'Стрижка' => 'employees/master-stylist.png',
            'Фарбування' => 'employees/master-colorist.png',
            'Манікюр' => 'employees/master-manikyurist.png',
            'Педикюр' => 'employees/master-manikyurist.png',
            'Макіяж' => 'employees/master-vizazhist.png',
        ];

        if ($employee->specialization && isset($specializationPhotoMap[$employee->specialization])) {
            $photoPath = $specializationPhotoMap[$employee->specialization];
            if (Storage::disk('public')->exists($photoPath)) {
                return Storage::url($photoPath);
            }
        }

        return $defaultPath ?? Storage::url('employees/master-stylist.png');
    }
}

