<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::with('category');

        // Пошук
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        // Фільтр по категорії
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Фільтр по статусу
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Сортування
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $services = $query->paginate(15)->withQueryString();
        $categories = ServiceCategory::ordered()->get();

        return view('admin.services.index', compact('services', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ServiceCategory::ordered()->get();
        
        return view('admin.services.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Завантаження зображення
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('services', 'public');
                $data['image'] = $imagePath;
            }

            // Встановити is_active за замовчуванням
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            $service = Service::create($data);

            DB::commit();

            return redirect()->route('services.show', $service)
                ->with('success', 'Послугу успішно створено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при створенні послуги: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load('category');
        
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $service->load('category');
        $categories = ServiceCategory::ordered()->get();
        
        return view('admin.services.edit', compact('service', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Завантаження нового зображення
            if ($request->hasFile('image')) {
                // Видалити старе зображення, якщо воно існує
                if ($service->image && Storage::disk('public')->exists($service->image)) {
                    Storage::disk('public')->delete($service->image);
                }
                
                $imagePath = $request->file('image')->store('services', 'public');
                $data['image'] = $imagePath;
            }

            $service->update($data);

            DB::commit();

            return redirect()->route('services.show', $service)
                ->with('success', 'Послугу успішно оновлено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при оновленні послуги: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        try {
            // Видалити зображення, якщо воно існує
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }

            $service->delete();

            return redirect()->route('services.index')
                ->with('success', 'Послугу успішно видалено.');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні послуги: ' . $e->getMessage());
        }
    }
}
