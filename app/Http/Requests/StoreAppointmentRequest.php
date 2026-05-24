<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
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
            'client_id' => ['required', 'exists:clients,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'service_id' => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'photo_before' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'photo_after' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
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

            $employeeId = $this->input('employee_id');
            $serviceId = $this->input('service_id');
            $appointmentDate = $this->input('appointment_date');
            $appointmentTime = $this->input('appointment_time');

            // Перевірка, чи майстер надає цю послугу
            $employee = Employee::find($employeeId);
            $service = Service::find($serviceId);

            if ($employee && $service) {
                if (!$employee->services()->where('services.id', $serviceId)->exists()) {
                    $validator->errors()->add('service_id', 'Цей майстер не надає обрану послугу.');
                }

                // Встановити duration та price з послуги
                $this->merge([
                    'duration' => $service->duration,
                    'price' => $service->price,
                ]);
            }

            // Перевірка доступності часу
            if ($appointmentDate && $appointmentTime) {
                $conflictingAppointment = Appointment::where('employee_id', $employeeId)
                    ->where('appointment_date', $appointmentDate)
                    ->where('appointment_time', $appointmentTime)
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->first();

                if ($conflictingAppointment) {
                    $validator->errors()->add('appointment_time', 'Цей час вже зайнятий. Оберіть інший час.');
                }

                // Перевірка на перекриття часу (якщо тривалість послуги перекривається з іншими записами)
                if ($service) {
                    // Нормалізувати дату та час перед парсингом
                    $normalizedDate = \Carbon\Carbon::parse($appointmentDate)->format('Y-m-d');
                    $normalizedTime = preg_replace('/^.*?(\d{2}:\d{2}(?::\d{2})?).*$/', '$1', $appointmentTime);
                    if (strlen($normalizedTime) === 5) {
                        $normalizedTime .= ':00';
                    }
                    $appointmentDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $normalizedDate . ' ' . $normalizedTime);
                    $endTime = $appointmentDateTime->copy()->addMinutes($service->duration);

                    $overlappingAppointments = Appointment::where('employee_id', $employeeId)
                        ->where('appointment_date', $appointmentDate)
                        ->whereIn('status', ['scheduled', 'confirmed'])
                        ->get()
                        ->filter(function ($appointment) use ($appointmentDateTime, $endTime) {
                            // appointment_time завжди у форматі H:i:s завдяки accessor
                            $timeString = $appointment->appointment_time;
                            // Нормалізувати дату - завжди тільки дата без часу
                            $aptDate = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
                            $appStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $aptDate . ' ' . $timeString);
                            $appEnd = $appStart->copy()->addMinutes($appointment->duration);

                            return $appointmentDateTime->lt($appEnd) && $endTime->gt($appStart);
                        });

                    if ($overlappingAppointments->isNotEmpty()) {
                        $validator->errors()->add('appointment_time', 'Обраний час перекривається з іншим записом. Оберіть інший час.');
                    }
                }
            }

            // Перевірка, чи час не в минулому (якщо дата сьогодні)
            if ($appointmentDate === now()->toDateString() && $appointmentTime) {
                if (\Carbon\Carbon::parse($appointmentDate . ' ' . $appointmentTime)->isPast()) {
                    $validator->errors()->add('appointment_time', 'Не можна створювати записи в минулому часі.');
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
            'client_id.required' => 'Клієнт обов\'язковий для вибору.',
            'client_id.exists' => 'Обраний клієнт не існує.',
            'employee_id.required' => 'Майстер обов\'язковий для вибору.',
            'employee_id.exists' => 'Обраний майстер не існує.',
            'service_id.required' => 'Послуга обов\'язкова для вибору.',
            'service_id.exists' => 'Обрана послуга не існує.',
            'appointment_date.required' => 'Дата запису обов\'язкова.',
            'appointment_date.after_or_equal' => 'Дата запису не може бути в минулому.',
            'appointment_time.required' => 'Час запису обов\'язковий.',
            'appointment_time.date_format' => 'Час має бути у форматі HH:mm.',
        ];
    }
}
