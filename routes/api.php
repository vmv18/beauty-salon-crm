<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AvailableTimeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AppointmentCalendarController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Публічні API маршрути (без автентифікації)
Route::get('/available-time', [AvailableTimeController::class, 'index'])->name('api.available-time');
Route::post('/check-availability', [AvailableTimeController::class, 'checkAvailability'])->name('api.check-availability');

// API для отримання майстрів за послугою
Route::get('/services/{service}/employees', [\App\Http\Controllers\Api\ServiceEmployeeController::class, '__invoke'])->name('api.service-employees');

// API для отримання записів клієнта
Route::get('/clients/{client}/appointments', [\App\Http\Controllers\Api\ClientAppointmentController::class, '__invoke'])->name('api.client-appointments');

// API маршрути, що вимагають автентифікації
// Використовуємо 'web' middleware для сесійної автентифікації та 'auth:web' для перевірки авторизації
Route::middleware(['web', 'auth:web'])->group(function () {
    // Сповіщення
    Route::get('/notifications', [NotificationController::class, 'index'])->name('api.notifications');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-read');
    
    // Календар записів (для адмінів та менеджерів)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/appointments-calendar', [AppointmentCalendarController::class, 'index'])->name('api.appointments-calendar');
        Route::post('/appointments/{appointment}/update-time', [AppointmentCalendarController::class, 'updateTime'])->name('api.appointments.update-time');
    });
});

