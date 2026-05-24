<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'user_id' => ['required', 'exists:users,id', 'unique:employees,user_id'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'hire_date' => ['nullable', 'date', 'before_or_equal:today'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'on_leave'])],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
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
            'user_id.unique' => 'Цей користувач вже має профіль майстра.',
            'photo.image' => 'Файл має бути зображенням.',
            'photo.mimes' => 'Зображення має бути у форматі: jpeg, png, jpg або gif.',
            'photo.max' => 'Розмір зображення не повинен перевищувати 2MB.',
            'rating.min' => 'Рейтинг не може бути менше 0.',
            'rating.max' => 'Рейтинг не може бути більше 5.',
            'hire_date.before_or_equal' => 'Дата найму не може бути в майбутньому.',
            'status.in' => 'Статус має бути: active, inactive або on_leave.',
        ];
    }
}
