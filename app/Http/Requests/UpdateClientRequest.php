<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('client');

        return [
            'user_id' => ['sometimes', 'required', 'exists:users,id', Rule::unique('clients', 'user_id')->ignore($clientId)],
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
