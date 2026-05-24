<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Дозволити тільки авторизованим користувачам з відповідними ролями
        return auth()->check() && (auth()->user()->hasRole(['admin', 'manager']));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id', 'unique:clients,user_id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // Максимум 5MB
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
            'user_id.required' => 'Користувач обов\'язковий для вибору.',
            'user_id.exists' => 'Обраний користувач не існує.',
            'user_id.unique' => 'Цей користувач вже має профіль клієнта.',
            'email.email' => 'Email має бути валідною адресою електронної пошти.',
            'date_of_birth.before' => 'Дата народження не може бути в майбутньому.',
            'gender.in' => 'Стать має бути: male, female або other.',
            'status.in' => 'Статус має бути: active або inactive.',
        ];
    }
}
