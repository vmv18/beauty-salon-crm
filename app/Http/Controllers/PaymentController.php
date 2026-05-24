<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['client.user', 'appointment']);

        // Пошук
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('amount', 'like', "%{$search}%");
        }

        // Фільтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фільтр по способу оплати
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Фільтр по клієнту
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Фільтр по запису
        if ($request->filled('appointment_id')) {
            $query->where('appointment_id', $request->appointment_id);
        }

        // Фільтр по даті
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        // Сортування
        $sortBy = $request->get('sort_by', 'payment_date');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $payments = $query->paginate(20)->withQueryString();
        $clients = Client::active()->with('user')->get();

        return view('admin.payments.index', compact('payments', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $appointmentId = $request->get('appointment_id');
        $clientId = $request->get('client_id');
        
        $appointment = null;
        $client = null;
        
        if ($appointmentId) {
            $appointment = Appointment::with(['client.user', 'payments'])->findOrFail($appointmentId);
            $client = $appointment->client;
        } elseif ($clientId) {
            $client = Client::with('user')->findOrFail($clientId);
        }

        $clients = Client::active()->with('user')->get();
        $appointments = $client 
            ? Appointment::where('client_id', $client->id)
                ->whereIn('status', ['scheduled', 'confirmed', 'completed'])
                ->with('service')
                ->orderBy('appointment_date', 'desc')
                ->get()
            : collect();

        return view('admin.payments.create', compact('clients', 'appointments', 'appointment', 'client'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Якщо не вказано дату, використати поточну
            if (!isset($data['payment_date'])) {
                $data['payment_date'] = now()->toDateString();
            }
            
            // Завантажити документ, якщо він є
            if ($request->hasFile('document')) {
                $data['document'] = $request->file('document')->store('payments/documents', 'public');
            }

            $payment = Payment::create($data);

            DB::commit();

            $redirectRoute = $request->appointment_id 
                ? route('appointments.show', $request->appointment_id)
                : route('payments.show', $payment);

            return redirect($redirectRoute)
                ->with('success', 'Платіж успішно створено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при створенні платежу: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['client.user', 'appointment.service', 'appointment.employee.user']);
        
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $payment->load(['client.user', 'appointment']);
        
        return view('admin.payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Завантажити новий документ, якщо він є
            if ($request->hasFile('document')) {
                // Видалити старий документ, якщо він існує
                if ($payment->document && Storage::disk('public')->exists($payment->document)) {
                    Storage::disk('public')->delete($payment->document);
                }
                $data['document'] = $request->file('document')->store('payments/documents', 'public');
            } elseif ($request->has('remove_document') && $request->remove_document) {
                // Видалити документ, якщо користувач натиснув кнопку видалення
                if ($payment->document && Storage::disk('public')->exists($payment->document)) {
                    Storage::disk('public')->delete($payment->document);
                }
                $data['document'] = null;
            }

            $payment->update($data);

            DB::commit();

            return redirect()->route('payments.show', $payment)
                ->with('success', 'Платіж успішно оновлено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при оновленні платежу: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        try {
            $payment->delete();

            return redirect()->route('payments.index')
                ->with('success', 'Платіж успішно видалено.');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні платежу: ' . $e->getMessage());
        }
    }
}
