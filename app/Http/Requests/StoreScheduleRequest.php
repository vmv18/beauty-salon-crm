<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
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
            'employee_id' => ['required', 'exists:employees,id'],
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'break_start' => ['nullable', 'date_format:H:i'],
            'break_end' => ['nullable', 'date_format:H:i', 'after:break_start'],
            'is_working' => ['boolean'],
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
            'employee_id.required' => 'Оберіть майстра.',
            'employee_id.exists' => 'Обраний майстер не існує.',
            'day_of_week.required' => 'Оберіть день тижня.',
            'day_of_week.between' => 'День тижня має бути від 1 до 7.',
            'start_time.date_format' => 'Час початку має бути у форматі HH:mm.',
            'end_time.date_format' => 'Час закінчення має бути у форматі HH:mm.',
            'end_time.after' => 'Час закінчення має бути після часу початку.',
            'break_start.date_format' => 'Час початку перерви має бути у форматі HH:mm.',
            'break_end.date_format' => 'Час закінчення перерви має бути у форматі HH:mm.',
            'break_end.after' => 'Час закінчення перерви має бути після часу початку.',
        ];
    }
}
