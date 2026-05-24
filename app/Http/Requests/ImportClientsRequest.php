<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportClientsRequest extends FormRequest
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
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // Максимум 10MB
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
            'file.required' => 'Будь ласка, виберіть файл для імпорту.',
            'file.file' => 'Завантажений файл не є валідним файлом.',
            'file.mimes' => 'Файл повинен бути у форматі CSV.',
            'file.max' => 'Розмір файлу не повинен перевищувати 10MB.',
        ];
    }
}
