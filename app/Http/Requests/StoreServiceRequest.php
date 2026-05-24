<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
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
            'category_id' => ['required', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'duration' => ['required', 'integer', 'min:1', 'max:480'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
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
            'category_id.required' => 'Категорія обов\'язкова.',
            'category_id.exists' => 'Обрана категорія не існує.',
            'name.required' => 'Назва послуги обов\'язкова.',
            'duration.required' => 'Тривалість обов\'язкова.',
            'duration.min' => 'Тривалість має бути мінімум 1 хвилина.',
            'duration.max' => 'Тривалість не може перевищувати 480 хвилин.',
            'price.required' => 'Ціна обов\'язкова.',
            'price.numeric' => 'Ціна має бути числом.',
            'price.min' => 'Ціна не може бути від\'ємною.',
            'image.image' => 'Файл має бути зображенням.',
            'image.mimes' => 'Зображення має бути у форматі: jpeg, png, jpg або gif.',
            'image.max' => 'Розмір зображення не повинен перевищувати 2MB.',
        ];
    }
}
