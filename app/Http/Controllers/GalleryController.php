<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Gallery::query();

        // Пошук
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Фільтр по статусу
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Сортування
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDir = $request->get('sort_dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $galleries = $query->paginate(15)->withQueryString();

        return view('admin.galleries.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.galleries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGalleryRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Завантаження зображення
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('gallery', 'public');
                $data['image'] = $imagePath;
            }

            // Встановити значення за замовчуванням
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }
            if (!isset($data['sort_order'])) {
                $data['sort_order'] = Gallery::max('sort_order') + 1 ?? 0;
            }

            $gallery = Gallery::create($data);

            DB::commit();

            return redirect()->route('galleries.show', $gallery)
                ->with('success', 'Зображення успішно додано до галереї.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при додаванні зображення: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery)
    {
        return view('admin.galleries.show', compact('gallery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGalleryRequest $request, Gallery $gallery)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Завантаження нового зображення
            if ($request->hasFile('image')) {
                // Видалити старе зображення
                if ($gallery->image && Storage::disk('public')->exists($gallery->image)) {
                    Storage::disk('public')->delete($gallery->image);
                }
                
                $imagePath = $request->file('image')->store('gallery', 'public');
                $data['image'] = $imagePath;
            }

            $gallery->update($data);

            DB::commit();

            return redirect()->route('galleries.show', $gallery)
                ->with('success', 'Зображення успішно оновлено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при оновленні зображення: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery)
    {
        try {
            DB::beginTransaction();

            // Видалити зображення
            if ($gallery->image && Storage::disk('public')->exists($gallery->image)) {
                Storage::disk('public')->delete($gallery->image);
            }

            $gallery->delete();

            DB::commit();

            return redirect()->route('galleries.index')
                ->with('success', 'Зображення успішно видалено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Помилка при видаленні зображення: ' . $e->getMessage());
        }
    }
}
