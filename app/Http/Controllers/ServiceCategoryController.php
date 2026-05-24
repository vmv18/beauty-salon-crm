<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ServiceCategory::query();

        // Пошук
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Сортування
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDir = $request->get('sort_dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $categories = $query->withCount('services')->paginate(15)->withQueryString();

        return view('admin.service-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.service-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Завантаження зображення
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('service-categories', 'public');
                $data['image'] = $imagePath;
            }

            $category = ServiceCategory::create($data);

            DB::commit();

            return redirect()->route('service-categories.show', $category)
                ->with('success', 'Категорію успішно створено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при створенні категорії: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceCategory $serviceCategory)
    {
        $serviceCategory->load('services');
        
        return view('admin.service-categories.show', compact('serviceCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCategory $serviceCategory)
    {
        return view('admin.service-categories.edit', compact('serviceCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $serviceCategory)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Завантаження нового зображення
            if ($request->hasFile('image')) {
                // Видалити старе зображення, якщо воно існує
                if ($serviceCategory->image && Storage::disk('public')->exists($serviceCategory->image)) {
                    Storage::disk('public')->delete($serviceCategory->image);
                }
                
                $imagePath = $request->file('image')->store('service-categories', 'public');
                $data['image'] = $imagePath;
            }

            $serviceCategory->update($data);

            DB::commit();

            return redirect()->route('service-categories.show', $serviceCategory)
                ->with('success', 'Категорію успішно оновлено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при оновленні категорії: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        try {
            // Перевірити, чи є послуги в цій категорії
            if ($serviceCategory->services()->count() > 0) {
                return back()->with('error', 'Неможливо видалити категорію, оскільки вона містить послуги.');
            }

            // Видалити зображення, якщо воно існує
            if ($serviceCategory->image && Storage::disk('public')->exists($serviceCategory->image)) {
                Storage::disk('public')->delete($serviceCategory->image);
            }

            $serviceCategory->delete();

            return redirect()->route('service-categories.index')
                ->with('success', 'Категорію успішно видалено.');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні категорії: ' . $e->getMessage());
        }
    }
}
