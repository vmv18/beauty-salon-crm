<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Employee;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Schedule::with('employee.user');

        // Фільтр по майстру
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Фільтр по дню тижня
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Фільтр по робочим/вихідним дням
        if ($request->filled('is_working')) {
            $query->where('is_working', $request->is_working);
        }

        $schedules = $query->orderBy('employee_id')->orderBy('day_of_week')->paginate(20)->withQueryString();
        $employees = Employee::active()->with('user')->get();

        return view('admin.schedules.index', compact('schedules', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $employees = Employee::active()->with('user')->get();
        
        // Дні тижня
        $daysOfWeek = [
            1 => 'Понеділок',
            2 => 'Вівторок',
            3 => 'Середа',
            4 => 'Четвер',
            5 => 'П\'ятниця',
            6 => 'Субота',
            7 => 'Неділя',
        ];

        return view('admin.schedules.create', compact('employees', 'daysOfWeek', 'employeeId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request)
    {
        try {
            DB::beginTransaction();

            // Перевірка, чи вже існує розклад для цього майстра та дня
            $existing = Schedule::where('employee_id', $request->employee_id)
                ->where('day_of_week', $request->day_of_week)
                ->first();

            if ($existing) {
                return back()->withInput()
                    ->with('error', 'Розклад для цього майстра та дня тижня вже існує. Використайте редагування.');
            }

            Schedule::create($request->validated());

            DB::commit();

            return redirect()->route('schedules.index')
                ->with('success', 'Розклад успішно створено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при створенні розкладу: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load('employee.user');
        
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $schedule->load('employee.user');
        $daysOfWeek = [
            1 => 'Понеділок',
            2 => 'Вівторок',
            3 => 'Середа',
            4 => 'Четвер',
            5 => 'П\'ятниця',
            6 => 'Субота',
            7 => 'Неділя',
        ];

        return view('admin.schedules.edit', compact('schedule', 'daysOfWeek'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        try {
            DB::beginTransaction();

            $schedule->update($request->validated());

            DB::commit();

            return redirect()->route('schedules.index')
                ->with('success', 'Розклад успішно оновлено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при оновленні розкладу: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $schedule->delete();

            return redirect()->route('schedules.index')
                ->with('success', 'Розклад успішно видалено.');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні розкладу: ' . $e->getMessage());
        }
    }

    /**
     * Show schedule for specific employee.
     */
    public function forEmployee(Employee $employee)
    {
        $employee->load('user', 'schedules');
        
        // Дні тижня
        $daysOfWeek = [
            1 => 'Понеділок',
            2 => 'Вівторок',
            3 => 'Середа',
            4 => 'Четвер',
            5 => 'П\'ятниця',
            6 => 'Субота',
            7 => 'Неділя',
        ];

        // Створити масив розкладів по днях
        $schedulesByDay = [];
        foreach ($daysOfWeek as $dayNum => $dayName) {
            $schedulesByDay[$dayNum] = $employee->schedules()->where('day_of_week', $dayNum)->first();
        }

        return view('admin.schedules.employee', compact('employee', 'daysOfWeek', 'schedulesByDay'));
    }
}
