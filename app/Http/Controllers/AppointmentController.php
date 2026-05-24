<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentNotification;
use App\Notifications\NewAppointmentNotification;
use App\Notifications\AppointmentCancelledNotification;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['client.user', 'employee.user', 'service']);

        // Пошук
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('employee.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Фільтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фільтр по даті
        if ($request->filled('date')) {
            $query->where('appointment_date', $request->date);
        }

        // Фільтр по майстру
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Фільтр по клієнту
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Сортування
        $sortBy = $request->get('sort_by', 'appointment_date');
        $sortDir = $request->get('sort_dir', 'desc');
        
        if ($sortBy === 'appointment_date') {
            $query->orderBy('appointment_date', $sortDir)
                  ->orderBy('appointment_time', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $appointments = $query->paginate(20)->withQueryString();
        $employees = Employee::active()->with('user')->get();
        $clients = Client::active()->with('user')->get();

        return view('admin.appointments.index', compact('appointments', 'employees', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::active()->with('user')->get();
        $employees = Employee::active()->with('user')->get();
        $services = Service::active()->with('category')->get();
        
        return view('admin.appointments.create', compact('clients', 'employees', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['status'] = 'scheduled';

            // Завантажити фото до/після, якщо вони є
            if ($request->hasFile('photo_before')) {
                $data['photo_before'] = $request->file('photo_before')->store('appointments/photos/before', 'public');
            }
            if ($request->hasFile('photo_after')) {
                $data['photo_after'] = $request->file('photo_after')->store('appointments/photos/after', 'public');
            }

            $appointment = Appointment::create($data);
            
            // Завантажити зв'язки для email
            $appointment->load(['client.user', 'employee.user', 'service']);

            DB::commit();

            // Відправити email підтвердження клієнту
            try {
                Mail::to($appointment->client->user->email)
                    ->send(new AppointmentConfirmation($appointment));
            } catch (\Exception $e) {
                \Log::error('Failed to send confirmation email: ' . $e->getMessage());
            }

            // Відправити сповіщення майстру
            try {
                Mail::to($appointment->employee->user->email)
                    ->send(new AppointmentNotification($appointment));
            } catch (\Exception $e) {
                \Log::error('Failed to send notification email to employee: ' . $e->getMessage());
            }

            // Відправити внутрішнє сповіщення адмінам та менеджерам
            $adminUsers = \App\Models\User::role(['admin', 'manager'])->get();
            foreach ($adminUsers as $user) {
                $user->notify(new NewAppointmentNotification($appointment));
            }

            // Відправити внутрішнє сповіщення майстру
            if ($appointment->employee && $appointment->employee->user) {
                $appointment->employee->user->notify(new NewAppointmentNotification($appointment));
            }

            // Відправити внутрішнє сповіщення клієнту
            if ($appointment->client && $appointment->client->user) {
                $appointment->client->user->notify(new NewAppointmentNotification($appointment));
            }

            return redirect()->route('admin.appointments.show', $appointment)
                ->with('success', 'Запис успішно створено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при створенні запису: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['client.user', 'employee.user', 'service.category', 'payments', 'review']);
        
        // Перевірити, чи клієнт може залишити відгук
        $canReview = false;
        $existingReview = null;
        
        if (auth()->check() && auth()->user()->client) {
            $client = auth()->user()->client;
            if ($appointment->client_id === $client->id && $appointment->status === 'completed') {
                $existingReview = \App\Models\Review::where('appointment_id', $appointment->id)
                    ->where('client_id', $client->id)
                    ->first();
                $canReview = !$existingReview;
            }
        }
        
        return view('admin.appointments.show', compact('appointment', 'canReview', 'existingReview'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $appointment->load(['client.user', 'employee.user', 'service.category']);
        
        $clients = Client::active()->with('user')->get();
        $employees = Employee::active()->with('user')->get();
        $services = Service::active()->with('category')->get();
        
        return view('admin.appointments.edit', compact('appointment', 'clients', 'employees', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Завантажити нові фото, якщо вони є
            if ($request->hasFile('photo_before')) {
                // Видалити старе фото, якщо воно існує
                if ($appointment->photo_before && Storage::disk('public')->exists($appointment->photo_before)) {
                    Storage::disk('public')->delete($appointment->photo_before);
                }
                $data['photo_before'] = $request->file('photo_before')->store('appointments/photos/before', 'public');
            } elseif ($request->has('remove_photo_before') && $request->remove_photo_before) {
                if ($appointment->photo_before && Storage::disk('public')->exists($appointment->photo_before)) {
                    Storage::disk('public')->delete($appointment->photo_before);
                }
                $data['photo_before'] = null;
            }
            
            if ($request->hasFile('photo_after')) {
                // Видалити старе фото, якщо воно існує
                if ($appointment->photo_after && Storage::disk('public')->exists($appointment->photo_after)) {
                    Storage::disk('public')->delete($appointment->photo_after);
                }
                $data['photo_after'] = $request->file('photo_after')->store('appointments/photos/after', 'public');
            } elseif ($request->has('remove_photo_after') && $request->remove_photo_after) {
                if ($appointment->photo_after && Storage::disk('public')->exists($appointment->photo_after)) {
                    Storage::disk('public')->delete($appointment->photo_after);
                }
                $data['photo_after'] = null;
            }
            
            $appointment->update($data);

            DB::commit();

            return redirect()->route('admin.appointments.show', $appointment)
                ->with('success', 'Запис успішно оновлено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при оновленні запису: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        try {
            $appointment->delete();

            return redirect()->route('admin.appointments.index')
                ->with('success', 'Запис успішно видалено.');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні запису: ' . $e->getMessage());
        }
    }

    /**
     * Cancel the appointment.
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        // Перевірити права доступу для майстрів
        if (auth()->user()->hasRole('master')) {
            $employee = auth()->user()->employee;
            if (!$employee || $appointment->employee_id !== $employee->id) {
                abort(403, 'Ви не маєте доступу до цього запису.');
            }
            $redirectRoute = 'master.appointments.show';
        } else {
            $redirectRoute = 'admin.appointments.show';
        }

        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // Завантажити зв'язки
            $appointment->load(['client.user', 'employee.user', 'service']);

            DB::commit();

            // Відправити внутрішнє сповіщення адмінам та менеджерам
            $adminUsers = \App\Models\User::role(['admin', 'manager'])->get();
            foreach ($adminUsers as $user) {
                $user->notify(new AppointmentCancelledNotification($appointment, $request->cancellation_reason));
            }

            // Відправити сповіщення клієнту
            try {
                $appointment->client->user->notify(new AppointmentCancelledNotification($appointment, $request->cancellation_reason));
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation notification to client: ' . $e->getMessage());
            }

            return redirect()->route($redirectRoute, $appointment)
                ->with('success', 'Запис успішно скасовано.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Помилка при скасуванні запису: ' . $e->getMessage());
        }
    }

    /**
     * Confirm the appointment.
     */
    public function confirm(Appointment $appointment)
    {
        // Перевірити права доступу для майстрів
        if (auth()->user()->hasRole('master')) {
            $employee = auth()->user()->employee;
            if (!$employee || $appointment->employee_id !== $employee->id) {
                abort(403, 'Ви не маєте доступу до цього запису.');
            }
            $redirectRoute = 'master.appointments.show';
        } else {
            $redirectRoute = 'admin.appointments.show';
        }

        try {
            DB::beginTransaction();

            $appointment->update([
                'status' => 'confirmed',
            ]);

            // Завантажити зв'язки для email
            $appointment->load(['client.user', 'employee.user', 'service']);

            DB::commit();

            // Відправити email підтвердження клієнту
            try {
                Mail::to($appointment->client->user->email)
                    ->send(new AppointmentConfirmation($appointment));
            } catch (\Exception $e) {
                \Log::error('Failed to send confirmation email: ' . $e->getMessage());
            }

            // Відправити внутрішнє сповіщення клієнту про підтвердження
            try {
                if ($appointment->client && $appointment->client->user) {
                    $appointment->client->user->notify(new AppointmentConfirmedNotification($appointment));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send confirmation notification to client: ' . $e->getMessage());
            }

            return redirect()->route($redirectRoute, $appointment)
                ->with('success', 'Запис підтверджено. Клієнту відправлено email підтвердження.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Помилка при підтвердженні запису: ' . $e->getMessage());
        }
    }

    /**
     * Mark appointment as completed.
     */
    public function complete(Appointment $appointment)
    {
        // Перевірка прав доступу
        $user = auth()->user();
        
        // Майстер може позначати тільки свої записи як виконані
        if ($user->hasRole('master')) {
            $employee = $user->employee;
            if (!$employee || $appointment->employee_id !== $employee->id) {
                abort(403, 'Ви можете позначати як виконані тільки свої записи.');
            }
        }
        
        // Адмін та менеджер мають повний доступ
        if (!$user->hasAnyRole(['admin', 'manager', 'master'])) {
            abort(403, 'Доступ заборонено.');
        }

        try {
            DB::beginTransaction();

            $appointment->update([
                'status' => 'completed',
            ]);

            // Завантажити зв'язки
            $appointment->load('client', 'service');

            // Нарахувати бали лояльності (1% від суми послуги, мінімум 10 балів)
            if ($appointment->client && $appointment->price > 0) {
                $points = max(10, (int) round($appointment->price * 0.01)); // 1% від суми, мінімум 10 балів
                $appointment->client->addLoyaltyPoints(
                    $points,
                    $appointment,
                    "Нарахування балів за послугу: {$appointment->service->name}"
                );
            }

            DB::commit();

            // Перенаправлення залежно від ролі користувача
            if ($user->hasRole('master')) {
                $redirectRoute = 'master.appointments.show';
            } elseif ($user->hasAnyRole(['admin', 'manager'])) {
                $redirectRoute = 'admin.appointments.show';
            } else {
                $redirectRoute = 'admin.appointments.show';
            }

            return redirect()->route($redirectRoute, $appointment)
                ->with('success', 'Запис відмічено як виконаний. Клієнту нараховано бали лояльності.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Помилка при оновленні статусу: ' . $e->getMessage());
        }
    }

    /**
     * Display calendar view of appointments.
     */
    public function calendar(Request $request)
    {
        $employees = Employee::active()->with('user')->get();
        
        return view('admin.appointments.calendar', compact('employees'));
    }

    /**
     * Display a listing of master's appointments.
     */
    public function masterIndex(Request $request)
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return redirect()->route('master.dashboard')
                ->with('error', 'Профіль майстра не знайдено.');
        }

        $query = Appointment::where('employee_id', $employee->id)
            ->with(['client.user', 'service']);

        // Фільтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фільтр по даті
        if ($request->filled('date')) {
            $query->where('appointment_date', $request->date);
        } else {
            // За замовчуванням показуємо майбутні записи
            $query->where('appointment_date', '>=', now()->toDateString());
        }

        $appointments = $query->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(15);

        return view('roles.master.appointments.index', compact('appointments'));
    }

    /**
     * Display the specified appointment for master.
     */
    public function masterShow(Appointment $appointment)
    {
        // Перевірити, чи запис належить майстру
        $employee = auth()->user()->employee;
        
        if (!$employee || $appointment->employee_id !== $employee->id) {
            abort(403, 'Ви не маєте доступу до цього запису.');
        }

        $appointment->load(['client.user', 'service.category', 'payments', 'review']);

        return view('roles.master.appointments.show', compact('appointment'));
    }

    /**
     * Reschedule appointment (for master).
     */
    public function reschedule(Request $request, Appointment $appointment)
    {
        $employee = auth()->user()->employee;
        
        if (!$employee || $appointment->employee_id !== $employee->id) {
            abort(403, 'Ви не маєте доступу до цього запису.');
        }

        $request->validate([
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'reschedule_reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            $oldDate = $appointment->appointment_date;
            $oldTime = $appointment->appointment_time;

            $appointment->update([
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . 
                          'Перенесено майстром: ' . $oldDate . ' ' . substr($oldTime, 0, 5) . 
                          ' → ' . $request->appointment_date . ' ' . $request->appointment_time .
                          ($request->reschedule_reason ? "\nПричина: " . $request->reschedule_reason : ''),
            ]);

            $appointment->load(['client.user', 'employee.user', 'service']);

            DB::commit();

            // Відправити email клієнту про перенесення
            try {
                Mail::to($appointment->client->user->email)
                    ->send(new AppointmentConfirmation($appointment));
            } catch (\Exception $e) {
                \Log::error('Failed to send reschedule email: ' . $e->getMessage());
            }

            // Відправити внутрішнє сповіщення клієнту про перенесення
            try {
                if ($appointment->client && $appointment->client->user) {
                    $appointment->client->user->notify(new AppointmentRescheduledNotification($appointment, $oldDate, $oldTime));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send reschedule notification to client: ' . $e->getMessage());
            }

            return redirect()->route('master.appointments.show', $appointment)
                ->with('success', 'Запис успішно перенесено. Клієнту відправлено сповіщення.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при перенесенні запису: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of client's appointments.
     */
    public function clientIndex(Request $request)
    {
        $client = auth()->user()->client;
        
        if (!$client) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Профіль клієнта не знайдено.');
        }

        $query = Appointment::where('client_id', $client->id)
            ->with(['employee.user', 'service']);

        // Фільтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фільтр по даті
        if ($request->filled('date')) {
            $query->where('appointment_date', $request->date);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);

        return view('roles.client.appointments.index', compact('appointments'));
    }

    /**
     * Display the specified appointment for client.
     */
    public function clientShow(Appointment $appointment)
    {
        // Перевірити, чи запис належить клієнту
        $client = auth()->user()->client;
        
        if (!$client || $appointment->client_id !== $client->id) {
            abort(403, 'Ви не маєте доступу до цього запису.');
        }

        $appointment->load(['employee.user', 'service.category', 'payments', 'review']);

        // Перевірити, чи клієнт може залишити відгук
        $canReview = false;
        $existingReview = null;
        
        if ($appointment->status === 'completed') {
            $existingReview = \App\Models\Review::where('appointment_id', $appointment->id)
                ->where('client_id', $client->id)
                ->first();
            $canReview = !$existingReview;
        }

        return view('roles.client.appointments.show', compact('appointment', 'canReview', 'existingReview'));
    }

    /**
     * Cancel appointment (for client).
     */
    public function clientCancel(Request $request, Appointment $appointment)
    {
        $client = auth()->user()->client;
        
        if (!$client || $appointment->client_id !== $client->id) {
            abort(403, 'Ви не маєте доступу до цього запису.');
        }

        // Перевірити, чи можна скасувати (не можна скасувати в минулому або вже скасований/виконаний)
        if ($appointment->status === 'cancelled' || $appointment->status === 'completed') {
            return back()->with('error', 'Неможливо скасувати цей запис.');
        }

        $appointmentDateTime = $appointment->getAppointmentDateTime();
        if ($appointmentDateTime && $appointmentDateTime->isPast()) {
            return back()->with('error', 'Неможливо скасувати запис в минулому.');
        }

        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            $appointment->load(['client.user', 'employee.user', 'service']);

            DB::commit();

            // Відправити сповіщення майстру та адмінам
            try {
                if ($appointment->employee && $appointment->employee->user) {
                    $appointment->employee->user->notify(new AppointmentCancelledNotification($appointment, $request->cancellation_reason));
                }
                
                $adminUsers = \App\Models\User::role(['admin', 'manager'])->get();
                foreach ($adminUsers as $user) {
                    $user->notify(new AppointmentCancelledNotification($appointment, $request->cancellation_reason));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation notifications: ' . $e->getMessage());
            }

            return redirect()->route('client.appointments.show', $appointment)
                ->with('success', 'Запис успішно скасовано.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Помилка при скасуванні запису: ' . $e->getMessage());
        }
    }

    /**
     * Reschedule appointment (for client).
     */
    public function clientReschedule(Request $request, Appointment $appointment)
    {
        $client = auth()->user()->client;
        
        if (!$client || $appointment->client_id !== $client->id) {
            abort(403, 'Ви не маєте доступу до цього запису.');
        }

        // Перевірити, чи можна перенести
        if ($appointment->status === 'cancelled' || $appointment->status === 'completed') {
            return back()->with('error', 'Неможливо перенести цей запис.');
        }

        $request->validate([
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'reschedule_reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            $oldDate = $appointment->appointment_date;
            $oldTime = $appointment->appointment_time;

            $appointment->update([
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . 
                          'Перенесено клієнтом: ' . $oldDate . ' ' . substr($oldTime, 0, 5) . 
                          ' → ' . $request->appointment_date . ' ' . $request->appointment_time .
                          ($request->reschedule_reason ? "\nПричина: " . $request->reschedule_reason : ''),
            ]);

            $appointment->load(['client.user', 'employee.user', 'service']);

            DB::commit();

            // Відправити сповіщення майстру та адмінам
            try {
                if ($appointment->employee && $appointment->employee->user) {
                    $appointment->employee->user->notify(new NewAppointmentNotification($appointment));
                }
                
                $adminUsers = \App\Models\User::role(['admin', 'manager'])->get();
                foreach ($adminUsers as $user) {
                    $user->notify(new NewAppointmentNotification($appointment));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send reschedule notifications: ' . $e->getMessage());
            }

            return redirect()->route('client.appointments.show', $appointment)
                ->with('success', 'Запис успішно перенесено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при перенесенні запису: ' . $e->getMessage());
        }
    }
}
