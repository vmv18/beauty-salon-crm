<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $currentMonthStart = now()->startOfMonth()->toDateString();
        $currentMonthEnd = now()->endOfMonth()->toDateString();
        $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = now()->subMonth()->endOfMonth()->toDateString();

        // Записи на сьогодні
        $todayAppointments = Appointment::where('appointment_date', $today)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->with(['client.user', 'employee.user', 'service'])
            ->orderBy('appointment_time')
            ->get();

        // Записи на завтра
        $tomorrowAppointments = Appointment::where('appointment_date', $tomorrow)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->with(['client.user', 'employee.user', 'service'])
            ->orderBy('appointment_time')
            ->get();

        // Доходи за поточний місяць
        $currentMonthRevenue = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        // Доходи за минулий місяць
        $lastMonthRevenue = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        // Доходи за останні 7 днів (для графіка)
        $revenueLast7Days = [];
        $revenueLast7DaysDates = [];
        $revenueLast7DaysAmounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenue = Payment::where('status', 'completed')
                ->where('payment_date', $date)
                ->sum('amount');
            $revenueLast7DaysDates[] = now()->subDays($i)->format('d.m');
            $revenueLast7DaysAmounts[] = (float) $revenue;
        }

        // Кількість нових клієнтів за місяць
        $newClientsThisMonth = Client::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->count();

        $newClientsLastMonth = Client::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->count();

        // Популярні послуги (топ 5)
        $popularServices = Service::with('category')
            ->withCount(['appointments' => function ($query) {
                $query->whereIn('status', ['scheduled', 'confirmed', 'completed']);
            }])
            ->orderBy('appointments_count', 'desc')
            ->limit(5)
            ->get();

        // Зайнятість майстрів (кількість записів на сьогодні)
        $employeeWorkload = Employee::with('user')
            ->withCount(['appointments' => function ($query) use ($today) {
                $query->where('appointment_date', $today)
                    ->whereIn('status', ['scheduled', 'confirmed']);
            }])
            ->where('status', 'active')
            ->orderBy('appointments_count', 'desc')
            ->get();

        // Статистика за останні 30 днів (для графіка доходів)
        $revenueLast30Days = [];
        $revenueLast30DaysDates = [];
        $revenueLast30DaysAmounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenue = Payment::where('status', 'completed')
                ->where('payment_date', $date)
                ->sum('amount');
            $revenueLast30DaysDates[] = now()->subDays($i)->format('d.m');
            $revenueLast30DaysAmounts[] = (float) $revenue;
        }

        // Статистика по способам оплати
        $paymentMethodsStats = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd])
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(function ($item) {
                $methodNames = [
                    'cash' => 'Готівка',
                    'card' => 'Картка',
                    'online' => 'Онлайн',
                ];
                return [$methodNames[$item->payment_method] ?? $item->payment_method => (float) $item->total];
            });

        // Статистика по статусах записів
        $appointmentsByStatus = Appointment::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                $statusNames = [
                    'scheduled' => 'Заплановано',
                    'confirmed' => 'Підтверджено',
                    'completed' => 'Виконано',
                    'cancelled' => 'Скасовано',
                ];
                return [$statusNames[$item->status] ?? $item->status => (int) $item->count];
            });

        return view('dashboard.index', compact(
            'todayAppointments',
            'tomorrowAppointments',
            'currentMonthRevenue',
            'lastMonthRevenue',
            'revenueLast7DaysDates',
            'revenueLast7DaysAmounts',
            'revenueLast30DaysDates',
            'revenueLast30DaysAmounts',
            'newClientsThisMonth',
            'newClientsLastMonth',
            'popularServices',
            'employeeWorkload',
            'paymentMethodsStats',
            'appointmentsByStatus'
        ));
    }
}
