<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Реєстрація доступна для всіх
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ім\'я обов\'язкове для заповнення.',
            'name.max' => 'Ім\'я не може перевищувати 255 символів.',
            'email.required' => 'Email обов\'язковий для заповнення.',
            'email.email' => 'Email має бути валідною адресою електронної пошти.',
            'email.unique' => 'Користувач з таким email вже існує.',
            'password.required' => 'Пароль обов\'язковий для заповнення.',
            'password.confirmed' => 'Паролі не співпадають.',
            'password.min' => 'Пароль повинен містити мінімум 8 символів.',
            'phone.max' => 'Телефон не може перевищувати 20 символів.',
            'date_of_birth.before' => 'Дата народження не може бути в майбутньому.',
            'gender.in' => 'Стать має бути: male, female або other.',
        ];
    }
}
