<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,online'],
            'payment_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,completed,failed,refunded'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:10240'], // Максимум 10MB
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            // Якщо платіж прив'язаний до запису, перевірити клієнта
            if ($this->filled('appointment_id')) {
                $appointment = Appointment::find($this->appointment_id);
                if ($appointment && $appointment->client_id != $this->client_id) {
                    $validator->errors()->add('client_id', 'Клієнт не відповідає запису.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Оберіть клієнта.',
            'client_id.exists' => 'Обраний клієнт не існує.',
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
