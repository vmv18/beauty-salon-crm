<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
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
            'client_id' => ['sometimes', 'required', 'exists:clients,id'],
            'employee_id' => ['sometimes', 'required', 'exists:employees,id'],
            'service_id' => ['sometimes', 'required', 'exists:services,id'],
            'appointment_date' => ['sometimes', 'required', 'date'],
            'appointment_time' => ['sometimes', 'required', 'date_format:H:i'],
            'status' => ['sometimes', Rule::in(['scheduled', 'confirmed', 'completed', 'cancelled'])],
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

            $appointment = $this->route('appointment');
            $employeeId = $this->input('employee_id', $appointment->employee_id);
            $serviceId = $this->input('service_id', $appointment->service_id);
            $appointmentDate = $this->input('appointment_date', $appointment->appointment_date);
            $appointmentTime = $this->input('appointment_time', $appointment->appointment_time);

            // Перевірка, чи майстер надає цю послугу
            if ($this->has('employee_id') || $this->has('service_id')) {
                $employee = Employee::find($employeeId);
                $service = Service::find($serviceId);

                if ($employee && $service) {
                    if (!$employee->services()->where('services.id', $serviceId)->exists()) {
                        $validator->errors()->add('service_id', 'Цей майстер не надає обрану послугу.');
                    }

                    // Оновити duration та price з послуги
                    if ($this->has('service_id')) {
                        $this->merge([
                            'duration' => $service->duration,
                            'price' => $service->price,
                        ]);
                    }
                }
            }

            // Перевірка доступності часу (виключаючи поточний запис)
            if ($this->has('appointment_date') || $this->has('appointment_time') || $this->has('employee_id')) {
                $conflictingAppointment = Appointment::where('employee_id', $employeeId)
                    ->where('appointment_date', $appointmentDate)
                    ->where('appointment_time', $appointmentTime)
                    ->where('id', '!=', $appointment->id)
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->first();

                if ($conflictingAppointment) {
                    $validator->errors()->add('appointment_time', 'Цей час вже зайнятий. Оберіть інший час.');
                }

                // Перевірка на перекриття часу
                $service = Service::find($serviceId);
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
                        ->where('id', '!=', $appointment->id)
                        ->whereIn('status', ['scheduled', 'confirmed'])
                        ->get()
                        ->filter(function ($apt) use ($appointmentDateTime, $endTime) {
                            // appointment_time завжди у форматі H:i:s завдяки accessor
                            $timeString = $apt->appointment_time;
                            // Нормалізувати дату - завжди тільки дата без часу
                            $aptDate = \Carbon\Carbon::parse($apt->appointment_date)->format('Y-m-d');
                            $appStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $aptDate . ' ' . $timeString);
                            $appEnd = $appStart->copy()->addMinutes($apt->duration);

                            return $appointmentDateTime->lt($appEnd) && $endTime->gt($appStart);
                        });

                    if ($overlappingAppointments->isNotEmpty()) {
                        $validator->errors()->add('appointment_time', 'Обраний час перекривається з іншим записом. Оберіть інший час.');
                    }
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
            'appointment_time.required' => 'Час запису обов\'язковий.',
            'appointment_time.date_format' => 'Час має бути у форматі HH:mm.',
            'status.in' => 'Статус має бути: scheduled, confirmed, completed або cancelled.',
        ];
    }
}
