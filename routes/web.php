<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PublicServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PublicEmployeeController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\PublicGalleryController;
use App\Http\Controllers\PublicContactController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\ContactMessageController;

use App\Http\Controllers\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('landing');

// Публічні маршрути (SEO-friendly URLs) - мають бути ПЕРЕД ресурсними маршрутами
Route::get('/services', [PublicServiceController::class, 'index'])->name('public.services');
Route::get('/services/{service}', [PublicServiceController::class, 'show'])->name('public.service-detail');
Route::get('/masters', [PublicEmployeeController::class, 'index'])->name('public.employees');
Route::get('/masters/{employee}', [PublicEmployeeController::class, 'show'])->name('public.employee-profile');
Route::get('/gallery', [PublicGalleryController::class, 'index'])->name('public.gallery');
Route::get('/contact', [PublicContactController::class, 'index'])->name('public.contact');
Route::post('/contact', [PublicContactController::class, 'store'])->name('public.contact.store');

// Публічне бронювання
Route::get('/booking', [PublicBookingController::class, 'create'])->name('public.booking.create');
Route::post('/booking', [PublicBookingController::class, 'store'])->name('public.booking.store');
Route::get('/booking/success/{appointment}', [PublicBookingController::class, 'success'])->name('public.booking.success');


// Маршрути автентифікації
Route::get('/login', function () {
    // Якщо користувач вже авторизований, перенаправити на відповідний dashboard
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('manager')) {
            return redirect()->route('manager.dashboard');
        }
        if ($user->hasRole('master')) {
            return redirect()->route('master.dashboard');
        }
        if ($user->hasRole('client')) {
            return redirect()->route('client.dashboard');
        }
        return redirect('/');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Маршрути реєстрації
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Маршрути відновлення пароля
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);
    
    $status = \Illuminate\Support\Facades\Password::sendResetLink(
        $request->only('email')
    );
    
    return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
    
    $status = \Illuminate\Support\Facades\Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => \Illuminate\Support\Facades\Hash::make($password)
            ])->save();
        }
    );
    
    return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

// Dashboard маршрути для різних ролей
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
    Route::post('contact-messages/{contactMessage}/mark-as-read', [ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-as-read');
    
    // Звіти (тільки для адмінів)
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/clients', [ReportController::class, 'clients'])->name('reports.clients');
    Route::get('reports/employees', [ReportController::class, 'employees'])->name('reports.employees');
    Route::get('reports/services', [ReportController::class, 'services'])->name('reports.services');
});

Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:master'])->prefix('master')->name('master.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.master', ['user' => auth()->user()]);
    })->name('dashboard');
    
    // Записи майстра
    Route::get('/appointments', [AppointmentController::class, 'masterIndex'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'masterShow'])->name('appointments.show');
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
});

Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.client', ['user' => auth()->user()]);
    })->name('dashboard');
    
    // Записи клієнта
    Route::get('/appointments', [AppointmentController::class, 'clientIndex'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'clientShow'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'clientCancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/reschedule', [AppointmentController::class, 'clientReschedule'])->name('appointments.reschedule');
});


// Відгуки (для авторизованих клієнтів)
Route::middleware('auth')->group(function () {
    Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// API маршрути перенесені в routes/api.php

// Маршрути для управління клієнтами (доступні для admin та manager)
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::resource('clients', ClientController::class);
    Route::get('clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::get('clients/import', [ClientController::class, 'showImport'])->name('clients.import.show');
    Route::post('clients/import', [ClientController::class, 'import'])->name('clients.import');
    Route::resource('service-categories', ServiceCategoryController::class);
    // Використовуємо except для виключення index та show, оскільки вони конфліктують з публічними маршрутами
    Route::resource('services', ServiceController::class)->except(['index', 'show']);
    // Додаємо окремі маршрути для адмін-панелі з префіксом
    Route::get('admin/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('admin/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::resource('employees', EmployeeController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::get('employees/{employee}/schedule', [ScheduleController::class, 'forEmployee'])->name('schedules.employee');
    Route::resource('appointments', AppointmentController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('reviews', ReviewController::class)->except(['create', 'store']);
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::resource('galleries', \App\Http\Controllers\GalleryController::class);
    
    Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    
    // Календарний вигляд записів
    Route::get('appointments-calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    
    // API для календаря перенесено в routes/api.php
});
