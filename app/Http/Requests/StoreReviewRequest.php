<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Дозволити тільки авторизованим користувачам
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment_id' => ['required', 'exists:appointments,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('appointment_id')) {
                $appointment = \App\Models\Appointment::find($this->input('appointment_id'));
                
                if (!$appointment) {
                    return;
                }

                // Перевірити, чи запис завершений
                if ($appointment->status !== 'completed') {
                    $validator->errors()->add('appointment_id', 'Відгук можна залишити тільки після завершення послуги.');
                }

                // Перевірити, чи користувач є клієнтом цього запису
                $user = auth()->user();
                $client = $user->client;
                
                if (!$client || $client->id !== $appointment->client_id) {
                    $validator->errors()->add('appointment_id', 'Ви можете залишити відгук тільки на свої записи.');
                }

                // Перевірити, чи вже не залишено відгук на цей запис
                $existingReview = \App\Models\Review::where('appointment_id', $appointment->id)
                    ->where('client_id', $client->id)
                    ->exists();
                
                if ($existingReview) {
                    $validator->errors()->add('appointment_id', 'Ви вже залишили відгук на цей запис.');
                }
            }
        });
    }
}
