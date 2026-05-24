<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportController extends Controller
{
    /**
     * Показати сторінку звітів.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Звіт по доходах.
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        
        $startDate = null;
        $endDate = null;
        $title = '';

        switch ($period) {
            case 'day':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                $title = 'Доходи за день (' . now()->format('d.m.Y') . ')';
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                $title = 'Доходи за тиждень (' . $startDate->format('d.m.Y') . ' - ' . $endDate->format('d.m.Y') . ')';
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $title = 'Доходи за місяць (' . $startDate->format('m.Y') . ')';
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                $title = 'Доходи за рік (' . $startDate->format('Y') . ')';
                break;
        }

        $payments = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with(['client.user', 'appointment.service'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $totalRevenue = $payments->sum('amount');
        
        // Статистика по способам оплати
        $byPaymentMethod = $payments->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        });

        // Статистика по днях (для графіка)
        $byDate = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('d.m.Y');
        })->map(function ($group) {
            return $group->sum('amount');
        });

        $format = $request->get('format', 'html'); // html, pdf, csv

        if ($format === 'pdf') {
            return $this->exportRevenuePDF($payments, $title, $totalRevenue, $byPaymentMethod, $byDate);
        } elseif ($format === 'csv') {
            return $this->exportRevenueCSV($payments, $title, $totalRevenue);
        }

        return view('reports.revenue', compact('payments', 'title', 'totalRevenue', 'byPaymentMethod', 'byDate', 'period'));
    }

    /**
     * Звіт по клієнтах (найбільш активні).
     */
    public function clients(Request $request)
    {
        $limit = $request->get('limit', 20);
        
        $clients = Client::with('user')
            ->withCount(['appointments' => function ($query) {
                $query->whereIn('status', ['scheduled', 'confirmed', 'completed']);
            }])
            ->withSum(['payments' => function ($query) {
                $query->where('status', 'completed');
            }], 'amount')
            ->orderBy('appointments_count', 'desc')
            ->orderBy('payments_sum_amount', 'desc')
            ->limit($limit)
            ->get();

        $format = $request->get('format', 'html');

        if ($format === 'pdf') {
            return $this->exportClientsPDF($clients);
        } elseif ($format === 'csv') {
            return $this->exportClientsCSV($clients);
        }

        return view('reports.clients', compact('clients', 'limit'));
    }

    /**
     * Звіт по майстрах (продуктивність, доходи).
     */
    public function employees(Request $request)
    {
        $employees = Employee::with('user')
            ->withCount(['appointments' => function ($query) {
                $query->whereIn('status', ['scheduled', 'confirmed', 'completed']);
            }])
            ->with(['appointments' => function ($query) {
                $query->whereIn('status', ['scheduled', 'confirmed', 'completed'])
                    ->with('payments');
            }])
            ->where('status', 'active')
            ->get()
            ->map(function ($employee) {
                // Розрахувати доходи майстра
                $revenue = $employee->appointments->flatMap(function ($appointment) {
                    return $appointment->payments->where('status', 'completed');
                })->sum('amount');

                $employee->revenue = $revenue;
                return $employee;
            })
            ->sortByDesc('revenue')
            ->values();

        $format = $request->get('format', 'html');

        if ($format === 'pdf') {
            return $this->exportEmployeesPDF($employees);
        } elseif ($format === 'csv') {
            return $this->exportEmployeesCSV($employees);
        }

        return view('reports.employees', compact('employees'));
    }

    /**
     * Звіт по послугах (популярність).
     */
    public function services(Request $request)
    {
        $services = Service::with('category')
            ->withCount(['appointments' => function ($query) {
                $query->whereIn('status', ['scheduled', 'confirmed', 'completed']);
            }])
            ->with(['appointments' => function ($query) {
                $query->whereIn('status', ['scheduled', 'confirmed', 'completed'])
                    ->with('payments');
            }])
            ->orderBy('appointments_count', 'desc')
            ->get()
            ->map(function ($service) {
                // Розрахувати доходи від послуги
                $revenue = $service->appointments->flatMap(function ($appointment) {
                    return $appointment->payments->where('status', 'completed');
                })->sum('amount');

                $service->revenue = $revenue;
                return $service;
            })
            ->sortByDesc('revenue')
            ->values();

        $format = $request->get('format', 'html');

        if ($format === 'pdf') {
            return $this->exportServicesPDF($services);
        } elseif ($format === 'csv') {
            return $this->exportServicesCSV($services);
        }

        return view('reports.services', compact('services'));
    }

    /**
     * Експорт доходів в PDF.
     */
    private function exportRevenuePDF($payments, $title, $totalRevenue, $byPaymentMethod, $byDate)
    {
        $pdf = PDF::loadView('reports.exports.revenue-pdf', compact('payments', 'title', 'totalRevenue', 'byPaymentMethod', 'byDate'));
        return $pdf->download('revenue-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Експорт доходів в CSV.
     */
    private function exportRevenueCSV($payments, $title, $totalRevenue)
    {
        $filename = 'revenue-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments, $title, $totalRevenue) {
            $file = fopen('php://output', 'w');
            
            // BOM для UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [$title]);
            fputcsv($file, ['Загальна сума: ' . number_format($totalRevenue, 2) . ' грн']);
            fputcsv($file, []);
            fputcsv($file, ['Дата', 'Клієнт', 'Сума', 'Спосіб оплати', 'Запис']);
            
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_date->format('d.m.Y'),
                    $payment->client->user->name,
                    number_format($payment->amount, 2) . ' грн',
                    $payment->payment_method_name,
                    $payment->appointment ? '#' . $payment->appointment->id : '—',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Експорт клієнтів в PDF.
     */
    private function exportClientsPDF($clients)
    {
        $pdf = PDF::loadView('reports.exports.clients-pdf', compact('clients'));
        return $pdf->download('clients-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Експорт клієнтів в CSV.
     */
    private function exportClientsCSV($clients)
    {
        $filename = 'clients-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Клієнт', 'Email', 'Телефон', 'Кількість записів', 'Сума оплат (грн)']);
            
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->user->name,
                    $client->user->email,
                    $client->phone ?? '—',
                    $client->appointments_count,
                    number_format($client->payments_sum_amount ?? 0, 2),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Експорт майстрів в PDF.
     */
    private function exportEmployeesPDF($employees)
    {
        $pdf = PDF::loadView('reports.exports.employees-pdf', compact('employees'));
        return $pdf->download('employees-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Експорт майстрів в CSV.
     */
    private function exportEmployeesCSV($employees)
    {
        $filename = 'employees-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Майстер', 'Спеціалізація', 'Рейтинг', 'Кількість записів', 'Доходи (грн)']);
            
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->user->name,
                    $employee->specialization ?? '—',
                    number_format($employee->rating, 1),
                    $employee->appointments_count,
                    number_format($employee->revenue, 2),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Експорт послуг в PDF.
     */
    private function exportServicesPDF($services)
    {
        $pdf = PDF::loadView('reports.exports.services-pdf', compact('services'));
        return $pdf->download('services-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Експорт послуг в CSV.
     */
    private function exportServicesCSV($services)
    {
        $filename = 'services-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($services) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Послуга', 'Категорія', 'Ціна', 'Кількість записів', 'Доходи (грн)']);
            
            foreach ($services as $service) {
                fputcsv($file, [
                    $service->name,
                    $service->category->name,
                    number_format($service->price, 2) . ' грн',
                    $service->appointments_count,
                    number_format($service->revenue, 2),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
