<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with(['client.user', 'employee.user', 'service', 'appointment']);

        // Фільтрація
        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Пошук
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('client.user', function ($qr) use ($search) {
                      $qr->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('employee.user', function ($qr) use ($search) {
                      $qr->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Перевірити, чи користувач є клієнтом
        $user = auth()->user();
        if (!$user->client) {
            return redirect()->route('landing')
                ->with('error', 'Тільки клієнти можуть залишати відгуки.');
        }

        $appointmentId = $request->get('appointment_id');
        $appointment = null;

        if ($appointmentId) {
            $appointment = Appointment::with(['client.user', 'employee.user', 'service'])
                ->where('id', $appointmentId)
                ->where('client_id', $user->client->id)
                ->where('status', 'completed')
                ->first();

            if (!$appointment) {
                // Визначити правильний маршрут для редиректу залежно від ролі
                $redirectRoute = $user->hasRole('client') ? 'client.appointments.index' : 'appointments.index';
                return redirect()->route($redirectRoute)
                    ->with('error', 'Запис не знайдено або не завершено.');
            }

            // Перевірити, чи вже не залишено відгук
            $existingReview = Review::where('appointment_id', $appointment->id)
                ->where('client_id', $appointment->client_id)
                ->exists();

            if ($existingReview) {
                // Визначити правильний маршрут для редиректу залежно від ролі
                $redirectRoute = $user->hasRole('client') ? 'client.appointments.show' : 'appointments.show';
                return redirect()->route($redirectRoute, $appointment)
                    ->with('error', 'Ви вже залишили відгук на цей запис.');
            }
        }

        return view('admin.reviews.create', compact('appointment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        try {
            // Перевірити, чи користувач є клієнтом
            $user = auth()->user();
            if (!$user->client) {
                return redirect()->route('landing')
                    ->with('error', 'Тільки клієнти можуть залишати відгуки.');
            }

            DB::beginTransaction();

            $appointment = Appointment::findOrFail($request->appointment_id);
            $client = $user->client;

            // Перевірити, чи запис належить клієнту
            if ($appointment->client_id !== $client->id) {
                return redirect()->route('client.appointments.index')
                    ->with('error', 'Ви можете залишити відгук тільки на свій власний запис.');
            }

            // Перевірити, чи запис завершено
            if ($appointment->status !== 'completed') {
                return redirect()->route('client.appointments.show', $appointment)
                    ->with('error', 'Ви можете залишити відгук тільки на завершений запис.');
            }

            // Перевірити, чи вже не залишено відгук
            $existingReview = Review::where('appointment_id', $appointment->id)
                ->where('client_id', $client->id)
                ->exists();

            if ($existingReview) {
                return redirect()->route('client.appointments.show', $appointment)
                    ->with('error', 'Ви вже залишили відгук на цей запис.');
            }

            $review = Review::create([
                'appointment_id' => $appointment->id,
                'client_id' => $client->id,
                'employee_id' => $appointment->employee_id,
                'service_id' => $appointment->service_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_approved' => false, // Потребує модерації
            ]);

            // Автоматично оновити рейтинг майстра
            $this->updateEmployeeRating($appointment->employee_id);

            DB::commit();

            // Визначити правильний маршрут для редиректу залежно від ролі
            $redirectRoute = $user->hasRole('client') ? 'client.appointments.show' : 'appointments.show';
            return redirect()->route($redirectRoute, $appointment)
                ->with('success', 'Відгук успішно додано. Він буде опублікований після модерації.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при додаванні відгуку: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $review->load(['client.user', 'employee.user', 'service', 'appointment']);
        
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $review->load(['client.user', 'employee.user', 'service', 'appointment']);
        
        return view('admin.reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'is_approved' => ['sometimes', 'boolean'],
        ]);

        try {
            DB::beginTransaction();

            $review->update($request->only(['rating', 'comment', 'is_approved']));

            // Оновити рейтинг майстра
            $this->updateEmployeeRating($review->employee_id);

            DB::commit();

            return redirect()->route('reviews.show', $review)
                ->with('success', 'Відгук успішно оновлено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при оновленні відгуку: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        try {
            $employeeId = $review->employee_id;
            
            $review->delete();

            // Оновити рейтинг майстра після видалення відгуку
            $this->updateEmployeeRating($employeeId);

            return redirect()->route('reviews.index')
                ->with('success', 'Відгук успішно видалено.');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні відгуку: ' . $e->getMessage());
        }
    }

    /**
     * Схвалити відгук.
     */
    public function approve(Review $review)
    {
        try {
            DB::beginTransaction();

            $review->update(['is_approved' => true]);

            // Оновити рейтинг майстра
            $this->updateEmployeeRating($review->employee_id);

            DB::commit();

            return back()->with('success', 'Відгук схвалено.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Помилка при схваленні відгуку: ' . $e->getMessage());
        }
    }

    /**
     * Відхилити відгук.
     */
    public function reject(Review $review)
    {
        try {
            $review->update(['is_approved' => false]);

            return back()->with('success', 'Відгук відхилено.');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при відхиленні відгуку: ' . $e->getMessage());
        }
    }

    /**
     * Оновити рейтинг майстра на основі схвалених відгуків.
     */
    private function updateEmployeeRating(int $employeeId): void
    {
        $employee = \App\Models\Employee::find($employeeId);
        
        if (!$employee) {
            return;
        }

        // Розрахувати середній рейтинг зі схвалених відгуків
        $averageRating = Review::where('employee_id', $employeeId)
            ->where('is_approved', true)
            ->avg('rating');

        // Оновити рейтинг майстра (округлити до 2 знаків після коми)
        $employee->update([
            'rating' => round($averageRating ?? 0, 2)
        ]);
    }
}
