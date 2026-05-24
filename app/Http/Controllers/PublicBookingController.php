<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentNotification;
use App\Notifications\NewAppointmentNotification;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PublicBookingController extends Controller
{
    /**
     * Show the booking form.
     */
    public function create(Request $request)
    {
        $serviceId = $request->get('service_id');
        $employeeId = $request->get('employee_id');
        
        $categories = ServiceCategory::ordered()->with('activeServices')->get();
        $services = Service::active()->with('category')->orderBy('name')->get();
        
        // Якщо обрана послуга, отримати майстрів, які її надають
        $employees = null;
        if ($serviceId) {
            $service = Service::find($serviceId);
            if ($service) {
                $employees = $service->employees()->where('employees.status', 'active')->with('user')->get();
            }
        } else {
            $employees = Employee::active()->with('user')->get();
        }

        return view('public.booking.booking', compact('categories', 'services', 'employees', 'serviceId', 'employeeId'));
    }

    /**
     * Store a new booking.
     */
    public function store(StoreBookingRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $service = Service::findOrFail($data['service_id']);

            // Встановити duration та price з послуги
            $data['duration'] = $service->duration;
            $data['price'] = $service->price;
            $data['status'] = 'scheduled';

            // Якщо користувач не авторизований або не має профілю клієнта, створити або знайти клієнта
            if (!$request->filled('client_id')) {
                if (auth()->check() && auth()->user()->client) {
                    $data['client_id'] = auth()->user()->client->id;
                } else {
                    $client = $this->findOrCreateClient($data);
                    $data['client_id'] = $client->id;
                }
            }

            // Видалити тимчасові поля
            unset($data['name'], $data['email'], $data['phone']);

            $appointment = Appointment::create($data);
            
            // Завантажити зв'язки для email
            $appointment->load(['client.user', 'employee.user', 'service']);

            DB::commit();

            // Відправити email підтвердження клієнту
            try {
                Mail::to($appointment->client->user->email)
                    ->send(new AppointmentConfirmation($appointment));
            } catch (\Exception $e) {
                // Логувати помилку, але не переривати процес
                \Log::error('Failed to send confirmation email: ' . $e->getMessage());
            }

            // Відправити сповіщення майстру
            try {
                Mail::to($appointment->employee->user->email)
                    ->send(new AppointmentNotification($appointment));
            } catch (\Exception $e) {
                // Логувати помилку, але не переривати процес
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

            return redirect()->route('public.booking.success', $appointment)
                ->with('success', 'Ваш запис успішно створено! На вашу email адресу відправлено підтвердження.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при створенні запису: ' . $e->getMessage());
        }
    }

    /**
     * Show booking success page.
     */
    public function success(Appointment $appointment)
    {
        $appointment->load(['client.user', 'employee.user', 'service.category']);
        
        return view('public.booking.success', compact('appointment'));
    }

    /**
     * Find or create client from booking data.
     */
    private function findOrCreateClient(array $data)
    {
        // Спробувати знайти користувача за email
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            // Створити нового користувача
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(uniqid()), // Випадковий пароль
            ]);

            // Призначити роль клієнта
            $user->assignRole('client');
        }

        // Знайти або створити профіль клієнта
        $client = Client::firstOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'],
                'status' => 'active',
            ]
        );

        // Оновити телефон, якщо він змінився
        if ($data['phone'] && $client->phone !== $data['phone']) {
            $client->update(['phone' => $data['phone']]);
        }

        return $client;
    }
}
