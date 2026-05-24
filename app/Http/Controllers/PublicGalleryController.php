<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicGalleryController extends Controller
{
    /**
     * Display the gallery page.
     */
    public function index()
    {
        // Використовуємо зображення з бази даних
        $galleryItems = \App\Models\Gallery::active()
            ->ordered()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'image' => $item->image,
                ];
            })
            ->toArray();

        // Якщо немає зображень в БД, використовуємо дефолтні
        if (empty($galleryItems)) {
            $galleryItems = [
                ['id' => 1, 'title' => 'Стрижка та укладка', 'description' => 'Професійна стрижка з укладкою', 'image' => 'gallery/strizhka-before-after.png'],
                ['id' => 2, 'title' => 'Фарбування волосся', 'description' => 'Сучасне фарбування з доглядом', 'image' => 'gallery/farbuvannya-before-after.png'],
                ['id' => 3, 'title' => 'Манікюр', 'description' => 'Класичний та дизайнерський манікюр', 'image' => 'gallery/manikyur-before-after.png'],
                ['id' => 4, 'title' => 'Педикюр', 'description' => 'Догляд за нігтями ніг', 'image' => 'gallery/manikyur-before-after.png'],
                ['id' => 5, 'title' => 'Макіяж', 'description' => 'Вечірній та денний макіяж', 'image' => 'gallery/makiyazh-before-after.png'],
                ['id' => 6, 'title' => 'Догляд за обличчям', 'description' => 'Професійний догляд за шкірою', 'image' => 'gallery/doglyad-oblychchya-results.png'],
                ['id' => 7, 'title' => 'Нарощування волосся', 'description' => 'Результати нарощування волосся', 'image' => 'gallery/naroshchuvannya-volossya-results.png'],
            ];
        }

        return view('public.gallery', compact('galleryItems'));
    }
}
