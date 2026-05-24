<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicServiceController extends Controller
{
    /**
     * Display a listing of services for public.
     */
    public function index(Request $request)
    {
        // Кешуємо категорії на 1 годину
        $categories = Cache::remember('service_categories', 3600, function () {
            return ServiceCategory::ordered()->withCount(['services' => function ($query) {
                $query->where('is_active', true);
            }])->get();
        });

        // Генеруємо ключ кешу на основі параметрів запиту
        $cacheKey = 'services_list:' . md5($request->getQueryString());

        $services = Cache::remember($cacheKey, 1800, function () use ($request) {
            $query = Service::with('category')->where('is_active', true);

            // Фільтр по категорії
            if ($request->filled('category')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('id', $request->category);
                });
            }

            // Пошук
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Сортування
            $sortBy = $request->get('sort', 'name');
            $sortDir = $request->get('dir', 'asc');
            $query->orderBy($sortBy, $sortDir);

            return $query->paginate(12)->withQueryString();
        });

        return view('public.services', compact('services', 'categories'));
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        if (!$service->is_active) {
            abort(404);
        }

        $service->load('category', 'employees.user');
        
        // Кешуємо відгуки на 30 хвилин
        $cacheKey = 'service_reviews:' . $service->id;
        $reviewsData = Cache::remember($cacheKey, 1800, function () use ($service) {
            $reviews = \App\Models\Review::where('service_id', $service->id)
                ->where('is_approved', true)
                ->with(['client.user', 'employee.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            // Розрахувати статистику відгуків
            $reviewsStats = [
                'total' => $reviews->total(),
                'average' => \App\Models\Review::where('service_id', $service->id)
                    ->where('is_approved', true)
                    ->avg('rating'),
                'by_rating' => \App\Models\Review::where('service_id', $service->id)
                    ->where('is_approved', true)
                    ->selectRaw('rating, COUNT(*) as count')
                    ->groupBy('rating')
                    ->pluck('count', 'rating')
                    ->toArray(),
            ];
            
            return ['reviews' => $reviews, 'stats' => $reviewsStats];
        });
        
        // Отримати схожі послуги (кешуємо на 1 годину)
        $relatedServices = Cache::remember('related_services:' . $service->id, 3600, function () use ($service) {
            return Service::where('category_id', $service->category_id)
                ->where('id', '!=', $service->id)
                ->where('is_active', true)
                ->limit(4)
                ->get();
        });

        return view('public.service-detail', [
            'service' => $service,
            'relatedServices' => $relatedServices,
            'reviews' => $reviewsData['reviews'],
            'reviewsStats' => $reviewsData['stats']
        ]);
    }
}
