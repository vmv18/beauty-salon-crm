<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Requests\ImportClientsRequest;
use App\Notifications\NewClientNotification;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::with('user');

        // Пошук по імені користувача, email, телефону
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        }

        // Фільтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фільтр по статі
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Сортування
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // Пагінація
        $clients = $query->paginate(15)->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Отримати користувачів без профілю клієнта
        $usersWithoutClient = User::whereDoesntHave('client')->get();
        
        return view('admin.clients.create', compact('usersWithoutClient'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Завантажити фото, якщо воно є
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('clients/photos', 'public');
            }

            $client = Client::create($data);
            
            // Завантажити зв'язки
            $client->load('user');

            DB::commit();

            // Відправити внутрішнє сповіщення адмінам та менеджерам
            $adminUsers = User::role(['admin', 'manager'])->get();
            foreach ($adminUsers as $user) {
                $user->notify(new NewClientNotification($client));
            }

            return redirect()->route('clients.show', $client)
                ->with('success', 'Клієнт успішно створено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при створенні клієнта: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        // Завантажити зв'язані дані
        $client->load(['user', 'payments.appointment.service', 'appointments.service', 'appointments.employee.user', 'loyaltyPoints.appointment']);
        
        // Отримати статистику платежів
        $totalPaid = $client->payments()->where('status', 'completed')->sum('amount');
        $pendingPayments = $client->payments()->where('status', 'pending')->sum('amount');
        
        // Отримати історію записів та платежів
        $appointments = $client->appointments()->latest('appointment_date')->paginate(10, ['*'], 'appointments_page');
        $payments = $client->payments()->latest('payment_date')->paginate(10, ['*'], 'payments_page');
        
        return view('admin.clients.show', compact('client', 'totalPaid', 'pendingPayments', 'appointments', 'payments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $client->load('user');
        
        // Отримати всіх користувачів для вибору
        $users = User::all();
        
        return view('admin.clients.edit', compact('client', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Завантажити нове фото, якщо воно є
            if ($request->hasFile('photo')) {
                // Видалити старе фото, якщо воно існує
                if ($client->photo && Storage::disk('public')->exists($client->photo)) {
                    Storage::disk('public')->delete($client->photo);
                }
                
                $data['photo'] = $request->file('photo')->store('clients/photos', 'public');
            } elseif ($request->has('remove_photo') && $request->remove_photo) {
                // Видалити фото, якщо користувач натиснув кнопку видалення
                if ($client->photo && Storage::disk('public')->exists($client->photo)) {
                    Storage::disk('public')->delete($client->photo);
                }
                $data['photo'] = null;
            }

            $client->update($data);

            DB::commit();

            return redirect()->route('clients.show', $client)
                ->with('success', 'Клієнт успішно оновлено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при оновленні клієнта: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        try {
            $client->delete();

            return redirect()->route('clients.index')
                ->with('success', 'Клієнт успішно видалено.');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні клієнта: ' . $e->getMessage());
        }
    }

    /**
     * Export clients to CSV/Excel.
     */
    public function export(Request $request)
    {
        $query = Client::with('user');

        // Застосувати ті самі фільтри, що й в index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $clients = $query->orderBy('created_at', 'desc')->get();

        $filename = 'clients_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Додати BOM для правильного відображення кирилиці в Excel
        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');
            
            // Додати BOM для UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Заголовки
            fputcsv($file, [
                'ID',
                'Ім\'я',
                'Email',
                'Телефон',
                'Дата народження',
                'Стать',
                'Адреса',
                'Статус',
                'Примітки',
                'Дата створення',
            ], ';');

            // Дані
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->id,
                    $client->user->name ?? '',
                    $client->email ?? $client->user->email ?? '',
                    $client->phone ?? '',
                    $client->date_of_birth ? $client->date_of_birth->format('d.m.Y') : '',
                    $client->gender ?? '',
                    $client->address ?? '',
                    $client->status ?? '',
                    $client->notes ?? '',
                    $client->created_at->format('d.m.Y H:i'),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form.
     */
    public function showImport()
    {
        return view('admin.clients.import');
    }

    /**
     * Import clients from CSV/Excel file.
     */
    public function import(ImportClientsRequest $request)
    {
        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            
            $errors = [];
            $successCount = 0;
            $skipCount = 0;
            $rowNumber = 1; // Починаємо з 1, бо 0 - це заголовки

            // Відкрити файл
            $handle = fopen($path, 'r');
            
            if ($handle === false) {
                return back()->with('error', 'Не вдалося відкрити файл.');
            }

            // Пропустити BOM якщо є
            $firstChars = fread($handle, 3);
            if ($firstChars !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }

            // Пропустити заголовки
            $headers = fgetcsv($handle, 0, ';');
            $rowNumber++;

            DB::beginTransaction();

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $rowNumber++;
                
                // Пропустити порожні рядки
                if (empty(array_filter($row))) {
                    continue;
                }

                // Очікуваний формат: ID, Ім'я, Email, Телефон, Дата народження, Стать, Адреса, Статус, Примітки, Дата створення
                $data = [
                    'name' => trim($row[1] ?? ''),
                    'email' => trim($row[2] ?? ''),
                    'phone' => trim($row[3] ?? ''),
                    'date_of_birth' => !empty($row[4]) ? $this->parseDate($row[4]) : null,
                    'gender' => trim($row[5] ?? ''),
                    'address' => trim($row[6] ?? ''),
                    'status' => trim($row[7] ?? 'active'),
                    'notes' => trim($row[8] ?? ''),
                ];

                // Валідація даних
                $validator = Validator::make($data, [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'max:255'],
                    'phone' => ['nullable', 'string', 'max:20'],
                    'date_of_birth' => ['nullable', 'date', 'before:today'],
                    'gender' => ['nullable', 'in:male,female,other'],
                    'address' => ['nullable', 'string', 'max:500'],
                    'status' => ['nullable', 'in:active,inactive'],
                    'notes' => ['nullable', 'string', 'max:2000'],
                ]);

                if ($validator->fails()) {
                    $errors[] = "Рядок {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    $skipCount++;
                    continue;
                }

                // Перевірити, чи існує користувач з таким email
                $user = User::where('email', $data['email'])->first();
                
                if (!$user) {
                    // Створити нового користувача
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => Hash::make(Str::random(16)), // Випадковий пароль
                    ]);
                    
                    // Призначити роль клієнта
                    $user->assignRole('client');
                } else {
                    // Перевірити, чи вже є профіль клієнта
                    if ($user->client) {
                        $errors[] = "Рядок {$rowNumber}: Користувач з email {$data['email']} вже має профіль клієнта.";
                        $skipCount++;
                        continue;
                    }
                }

                // Створити профіль клієнта
                $client = Client::create([
                    'user_id' => $user->id,
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'date_of_birth' => $data['date_of_birth'],
                    'gender' => $data['gender'],
                    'address' => $data['address'],
                    'status' => $data['status'] ?: 'active',
                    'notes' => $data['notes'],
                ]);

                $successCount++;
            }

            fclose($handle);
            DB::commit();

            $message = "Імпорт завершено. Успішно імпортовано: {$successCount}, пропущено: {$skipCount}.";
            if (!empty($errors)) {
                $message .= " Помилки: " . count($errors);
                session()->flash('import_errors', $errors);
            }

            return redirect()->route('clients.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }

            return back()->with('error', 'Помилка при імпорті: ' . $e->getMessage());
        }
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        // Спробувати різні формати дат
        $formats = ['d.m.Y', 'Y-m-d', 'd/m/Y', 'Y/m/d', 'd-m-Y'];
        
        foreach ($formats as $format) {
            try {
                $date = \Carbon\Carbon::createFromFormat($format, trim($dateString));
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        // Якщо не вдалося розпарсити, спробувати через strtotime
        try {
            $timestamp = strtotime($dateString);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
            // Ігнорувати помилку
        }

        return null;
    }
}

