<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Employee;
use App\Models\Gallery;
use App\Models\Review;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index()
    {
        // Отримати популярні послуги (топ 6)
        $featuredServices = Service::with('category')
            ->where('is_active', true)
            ->withCount(['appointments' => function ($query) {
                $query->whereIn('status', ['scheduled', 'confirmed', 'completed']);
            }])
            ->orderBy('appointments_count', 'desc')
            ->limit(6)
            ->get();

        // Отримати категорії послуг
        $categories = ServiceCategory::ordered()
            ->withCount(['services' => function ($query) {
                $query->where('is_active', true);
            }])
            ->whereHas('services', function ($query) {
                $query->where('is_active', true);
            })
            ->limit(4)
            ->get();

        // Отримати майстрів для відображення
        $featuredEmployees = Employee::with('user')
            ->where('status', 'active')
            ->orderByRating('desc')
            ->limit(4)
            ->get();

        // Дані для галереї з бази даних (тільки 3 для слайдера на головній)
        $galleryImages = Gallery::active()
            ->ordered()
            ->limit(3)
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
        if (empty($galleryImages)) {
            $galleryImages = [
                ['id' => 1, 'title' => 'Стрижка та укладка', 'description' => 'Професійна стрижка з укладкою', 'image' => 'gallery/strizhka-before-after.png'],
                ['id' => 2, 'title' => 'Фарбування волосся', 'description' => 'Сучасне фарбування з доглядом', 'image' => 'gallery/farbuvannya-before-after.png'],
                ['id' => 3, 'title' => 'Манікюр', 'description' => 'Класичний та дизайнерський манікюр', 'image' => 'gallery/manikyur-before-after.png'],
            ];
        }

        // Отримати схвалені відгуки для відображення на головній сторінці
        $testimonials = Review::with(['client.user', 'employee.user', 'service'])
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('public.pages.landing', compact('featuredServices', 'categories', 'featuredEmployees', 'galleryImages', 'testimonials'));
    }
}
