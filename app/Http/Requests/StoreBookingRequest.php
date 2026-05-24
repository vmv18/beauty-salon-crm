<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Дозволити всім (публічна форма)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'name' => ['required_if:client_id,null', 'string', 'max:255'],
            'email' => ['required_if:client_id,null', 'email', 'max:255'],
            'phone' => ['required_if:client_id,null', 'string', 'max:20'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
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

                // Нормалізувати дату - завжди тільки дата без часу
                $appointmentDate = \Carbon\Carbon::parse($appointmentDate)->format('Y-m-d');
                // Нормалізувати час - завжди тільки час без дати
                $appointmentTime = preg_replace('/^.*?(\d{2}:\d{2}(?::\d{2})?).*$/', '$1', $appointmentTime);
                if (strlen($appointmentTime) === 5) {
                    $appointmentTime .= ':00';
                }
                
                // Перевірка доступності часу
                $appointmentDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . $appointmentTime);
                $endTime = $appointmentDateTime->copy()->addMinutes($service->duration);
                $minBreak = $employee->min_break_between_appointments ?? 15;

                // Перевірка, чи час не в минулому
                if ($appointmentDateTime->isPast()) {
                    $validator->errors()->add('appointment_time', 'Не можна бронювати час в минулому.');
                }

                // Перевірка робочих годин майстра
                $workStartTime = $employee->work_start_time ?? '09:00:00';
                $workEndTime = $employee->work_end_time ?? '18:00:00';
                
                // Конвертувати time в формат H:i
                $workStartTime = \Carbon\Carbon::parse($workStartTime)->format('H:i');
                $workEndTime = \Carbon\Carbon::parse($workEndTime)->format('H:i');
                
                $workStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $appointmentDate . ' ' . $workStartTime);
                $workEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $appointmentDate . ' ' . $workEndTime);

                if ($appointmentDateTime->lt($workStart) || $endTime->gt($workEnd)) {
                    $validator->errors()->add('appointment_time', 'Обраний час виходить за межі робочих годин майстра (' . $workStartTime . ' - ' . $workEndTime . ').');
                }

                // Перевірка на конфлікти з урахуванням мінімального проміжку
                $conflictingAppointments = Appointment::where('employee_id', $employeeId)
                    ->where('appointment_date', $appointmentDate)
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->get()
                    ->filter(function ($appointment) use ($appointmentDateTime, $endTime, $minBreak) {
                        // appointment_time завжди у форматі H:i:s завдяки accessor
                        $timeString = $appointment->appointment_time;
                        // Нормалізувати дату - завжди тільки дата без часу
                        $aptDate = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
                        $appStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $aptDate . ' ' . $timeString);
                        $appEnd = $appStart->copy()->addMinutes($appointment->duration);
                        $appEndWithBreak = $appEnd->copy()->addMinutes($minBreak);

                        // Перевірка перекриття з урахуванням мінімального проміжку
                        return $appointmentDateTime->lt($appEndWithBreak) && $endTime->gt($appStart);
                    });

                if ($conflictingAppointments->isNotEmpty()) {
                    $validator->errors()->add('appointment_time', 'Обраний час зайнятий або не відповідає мінімальному проміжку між записами (' . $minBreak . ' хв). Оберіть інший час.');
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
            'service_id.required' => 'Оберіть послугу.',
            'service_id.exists' => 'Обрана послуга не існує.',
            'employee_id.required' => 'Оберіть майстра.',
            'employee_id.exists' => 'Обраний майстер не існує.',
            'appointment_date.required' => 'Оберіть дату запису.',
            'appointment_date.after_or_equal' => 'Дата запису не може бути в минулому.',
            'appointment_time.required' => 'Оберіть час запису.',
            'appointment_time.date_format' => 'Час має бути у форматі HH:mm.',
            'name.required_if' => 'Введіть ваше ім\'я.',
            'email.required_if' => 'Введіть ваш email.',
            'email.email' => 'Email має бути валідною адресою.',
            'phone.required_if' => 'Введіть ваш телефон.',
        ];
    }
}
