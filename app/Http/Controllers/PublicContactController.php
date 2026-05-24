<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PublicContactController extends Controller
{
    /**
     * Display the contact page.
     */
    public function index()
    {
        return view('public.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Зберегти повідомлення в базу даних
            $contactMessage = ContactMessage::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'message' => $validated['message'],
            ]);

            // Відправити email адміністратору
            try {
                $adminEmail = config('mail.from.address', 'info@beautysalon.com');
                Mail::to($adminEmail)->send(new ContactFormMail($contactMessage));
            } catch (\Exception $e) {
                // Логувати помилку відправки email, але не переривати процес
                Log::error('Failed to send contact form email: ' . $e->getMessage());
            }

            return back()->with('success', 'Дякуємо за ваше повідомлення! Ми зв\'яжемося з вами найближчим часом.');
        } catch (\Exception $e) {
            Log::error('Failed to save contact message: ' . $e->getMessage());
            
            return back()->with('error', 'Виникла помилка при відправці повідомлення. Будь ласка, спробуйте пізніше або зв\'яжіться з нами безпосередньо.')
                ->withInput();
        }
    }
}
