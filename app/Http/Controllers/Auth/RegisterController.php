<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
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
        
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            // Створити користувача
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Призначити роль клієнта
            $user->assignRole('client');

            // Створити профіль клієнта
            $client = Client::create([
                'user_id' => $user->id,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'status' => 'active',
            ]);

            DB::commit();

            // Автоматично авторизувати користувача після реєстрації
            Auth::login($user);

            // Відправити сповіщення адмінам та менеджерам про нового клієнта
            $adminUsers = User::role(['admin', 'manager'])->get();
            foreach ($adminUsers as $adminUser) {
                $adminUser->notify(new \App\Notifications\NewClientNotification($client));
            }

            return redirect()->route('client.dashboard')
                ->with('success', 'Реєстрація успішна! Ласкаво просимо до Beauty Salon CRM.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Помилка при реєстрації: ' . $e->getMessage());
        }
    }
}
