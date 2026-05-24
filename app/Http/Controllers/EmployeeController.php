<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with('user', 'services');

        // Пошук
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('specialization', 'like', "%{$search}%");
        }

        // Фільтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Сортування
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        if ($sortBy === 'rating') {
            $query->orderByRating($sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $employees = $query->paginate(15)->withQueryString();

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Отримати користувачів без профілю майстра
        $usersWithoutEmployee = User::whereDoesntHave('employee')->get();
        $services = Service::active()->orderBy('name')->get();
        
        return view('admin.employees.create', compact('usersWithoutEmployee', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Завантаження фото
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('employees', 'public');
                $data['photo'] = $photoPath;
            }

            // Встановити значення за замовчуванням
            if (!isset($data['status'])) {
                $data['status'] = 'active';
            }
            if (!isset($data['rating'])) {
                $data['rating'] = 0.00;
            }

            // Зберегти послуги окремо
            $services = $data['services'] ?? [];
            unset($data['services']);

            $employee = Employee::create($data);

            // Призначити послуги
            if (!empty($services)) {
                $employee->services()->attach($services);
            }

            DB::commit();

            return redirect()->route('employees.show', $employee)
                ->with('success', 'Майстра успішно створено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при створенні майстра: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load('user', 'services.category');
        
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $employee->load('user', 'services');
        
        // Отримати всіх користувачів для вибору
        $users = User::all();
        $services = Service::active()->orderBy('name')->get();
        
        return view('admin.employees.edit', compact('employee', 'users', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Завантаження нового фото
            if ($request->hasFile('photo')) {
                // Видалити старе фото, якщо воно існує
                if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                    Storage::disk('public')->delete($employee->photo);
                }
                
                $photoPath = $request->file('photo')->store('employees', 'public');
                $data['photo'] = $photoPath;
            }

            // Зберегти послуги окремо
            $services = $data['services'] ?? [];
            unset($data['services']);

            $employee->update($data);

            // Синхронізувати послуги
            $employee->services()->sync($services);

            DB::commit();

            return redirect()->route('employees.show', $employee)
                ->with('success', 'Майстра успішно оновлено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при оновленні майстра: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Видалити фото, якщо воно існує
            if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }

            $employee->delete();

            return redirect()->route('employees.index')
                ->with('success', 'Майстра успішно видалено.');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні майстра: ' . $e->getMessage());
        }
    }
}
