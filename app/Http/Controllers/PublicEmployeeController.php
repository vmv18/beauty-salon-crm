<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class PublicEmployeeController extends Controller
{
    /**
     * Display a listing of employees for public.
     */
    public function index(Request $request)
    {
        $query = Employee::with('user', 'services.category')
            ->where('status', 'active');

        // Пошук
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('specialization', 'like', "%{$search}%");
        }

        // Фільтр по спеціалізації
        if ($request->filled('specialization')) {
            $query->where('specialization', 'like', "%{$request->specialization}%");
        }

        // Сортування за рейтингом за замовчуванням
        $sortBy = $request->get('sort', 'rating');
        $sortDir = $request->get('dir', 'desc');
        
        if ($sortBy === 'rating') {
            $query->orderByRating($sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $employees = $query->paginate(12)->withQueryString();

        // Отримати унікальні спеціалізації для фільтра
        $specializations = Employee::where('status', 'active')
            ->whereNotNull('specialization')
            ->distinct()
            ->pluck('specialization')
            ->filter()
            ->sort()
            ->values();

        return view('public.employees', compact('employees', 'specializations'));
    }

    /**
     * Display the specified employee profile.
     */
    public function show(Employee $employee)
    {
        if ($employee->status !== 'active') {
            abort(404);
        }

        $employee->load('user', 'services.category');
        
        // Отримати схвалені відгуки про майстра
        $reviews = \App\Models\Review::where('employee_id', $employee->id)
            ->where('is_approved', true)
            ->with(['client.user', 'service'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Розрахувати статистику відгуків
        $reviewsStats = [
            'total' => $reviews->total(),
            'average' => \App\Models\Review::where('employee_id', $employee->id)
                ->where('is_approved', true)
                ->avg('rating'),
            'by_rating' => \App\Models\Review::where('employee_id', $employee->id)
                ->where('is_approved', true)
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
        ];
        
        return view('public.employee-profile', compact('employee', 'reviews', 'reviewsStats'));
    }
}
