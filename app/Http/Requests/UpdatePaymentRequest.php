<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,online'],
            'payment_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,completed,failed,refunded'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:10240'], // Максимум 10MB
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
            'amount.required' => 'Введіть суму платежу.',
            'amount.numeric' => 'Сума має бути числом.',
            'amount.min' => 'Сума має бути більше 0.',
            'payment_method.required' => 'Оберіть спосіб оплати.',
            'payment_method.in' => 'Невірний спосіб оплати.',
            'payment_date.required' => 'Оберіть дату платежу.',
            'payment_date.date' => 'Дата має бути валідною.',
            'status.required' => 'Оберіть статус платежу.',
            'status.in' => 'Невірний статус платежу.',
        ];
    }
}
