<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\TimeBlock;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AvailableTimeController extends Controller
{
    /**
     * Get available time slots for employee and date.
     */
    #[OA\Get(
        path: "/api/available-time",
        summary: "Отримати доступні часові слоти",
        description: "Повертає список доступних часових слотів для майстра та послуги на вказану дату",
        tags: ["Available Time"],
        parameters: [
            new OA\Parameter(
                name: "employee_id",
                in: "query",
                required: true,
                description: "ID майстра",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
            new OA\Parameter(
                name: "service_id",
                in: "query",
                required: true,
                description: "ID послуги",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
            new OA\Parameter(
                name: "date",
                in: "query",
                required: true,
                description: "Дата у форматі Y-m-d",
                schema: new OA\Schema(type: "string", format: "date", example: "2024-12-25")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успішна відповідь",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "available_slots", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "time", type: "string", example: "10:00"),
                                new OA\Property(property: "display", type: "string", example: "10:00"),
                            ]
                        )),
                        new OA\Property(property: "service_duration", type: "integer", example: 60),
                        new OA\Property(property: "working_hours", properties: [
                            new OA\Property(property: "start", type: "string", example: "09:00"),
                            new OA\Property(property: "end", type: "string", example: "18:00"),
                        ]),
                        new OA\Property(property: "min_break", type: "integer", example: 15),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Помилка валідації"),
        ]
    )]
    public function index(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => ['required', 'exists:employees,id'],
                'service_id' => ['required', 'exists:services,id'],
                'date' => ['required', 'date', 'after_or_equal:today'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Помилка валідації: ' . implode(', ', $e->errors()['date'] ?? $e->errors()['employee_id'] ?? $e->errors()['service_id'] ?? ['Невірні параметри запиту']),
            ], 422);
        }

        try {
            $employee = Employee::findOrFail($request->employee_id);
            $service = Service::findOrFail($request->service_id);
            $date = $request->date;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Майстер або послуга не знайдені.',
            ], 404);
        }

        // Перевірка, чи майстер надає цю послугу
        if (!$employee->services()->where('services.id', $service->id)->exists()) {
            return response()->json([
                'error' => 'Цей майстер не надає обрану послугу.',
            ], 422);
        }

        // Отримати день тижня для дати
        $dateObj = \Carbon\Carbon::parse($date);
        $dayOfWeek = $dateObj->dayOfWeekIso; // 1 = Понеділок, 7 = Неділя

        // Отримати розклад для цього дня тижня
        $schedule = $employee->scheduleForDay($dayOfWeek);
        
        // Якщо є розклад для цього дня, використати його
        if ($schedule && $schedule->is_working) {
            $workStartTime = $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '09:00';
            $workEndTime = $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '18:00';
        } else {
            // Якщо немає розкладу або день вихідний, використати загальні робочі години
            if ($schedule && !$schedule->is_working) {
                // День вихідний
                return response()->json([
                    'available_slots' => [],
                    'service_duration' => $service->duration,
                    'message' => 'Майстер не працює в цей день.',
                ]);
            }
            
            $workStartTime = $employee->work_start_time ?? '09:00:00';
            $workEndTime = $employee->work_end_time ?? '18:00:00';
            $workStartTime = \Carbon\Carbon::parse($workStartTime)->format('H:i');
            $workEndTime = \Carbon\Carbon::parse($workEndTime)->format('H:i');
        }
        
        $minBreak = $employee->min_break_between_appointments ?? 15;

        // Отримати зайняті часові слоти з урахуванням мінімального проміжку
        $busySlots = Appointment::where('employee_id', $employee->id)
            ->where('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->get()
            ->map(function ($appointment) use ($minBreak) {
                // appointment_time завжди у форматі H:i:s завдяки accessor
                $timeString = $appointment->appointment_time;
                
                // Додаткова перевірка на випадок, якщо accessor не спрацював
                if (strlen($timeString) > 8) {
                    // Якщо містить подвійний час, витягнути останній
                    if (preg_match('/(\d{2}:\d{2}:\d{2})\s*$/', $timeString, $matches)) {
                        $timeString = $matches[1];
                    } elseif (preg_match_all('/(\d{2}:\d{2}:\d{2}|\d{2}:\d{2})/', $timeString, $allMatches)) {
                        $lastMatch = end($allMatches[0]);
                        $timeString = strlen($lastMatch) === 5 ? $lastMatch . ':00' : $lastMatch;
                    }
                }
                
                // Нормалізувати дату - завжди тільки дата без часу
                $aptDate = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
                $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $aptDate . ' ' . $timeString);
                $end = $start->copy()->addMinutes($appointment->duration);
                
                // Додати мінімальний проміжок до кінця запису
                $endWithBreak = $end->copy()->addMinutes($minBreak);
                
                return [
                    'start' => $start->format('H:i'),
                    'end' => $end->format('H:i'),
                    'end_with_break' => $endWithBreak->format('H:i'),
                ];
            })
            ->toArray();

        // Отримати блокування часу (відпустка, лікарня)
        $timeBlocks = TimeBlock::forEmployee($employee->id)
            ->activeOnDate($date)
            ->get()
            ->map(function ($block) {
                // Якщо start_time та end_time null - весь день заблоковано
                if (!$block->start_time || !$block->end_time) {
                    return [
                        'start' => '00:00',
                        'end' => '23:59',
                        'end_with_break' => '23:59',
                        'type' => $block->type,
                        'reason' => $block->reason,
                    ];
                }
                
                $start = \Carbon\Carbon::parse($block->start_time);
                $end = \Carbon\Carbon::parse($block->end_time);
                
                return [
                    'start' => $start->format('H:i'),
                    'end' => $end->format('H:i'),
                    'end_with_break' => $end->format('H:i'),
                    'type' => $block->type,
                    'reason' => $block->reason,
                ];
            })
            ->toArray();

        // Об'єднати зайняті слоти та блокування
        $busySlots = array_merge($busySlots, $timeBlocks);

        // Отримати обідню перерву з розкладу
        $breakStart = null;
        $breakEnd = null;
        if ($schedule && $schedule->break_start && $schedule->break_end) {
            $breakStart = \Carbon\Carbon::parse($schedule->break_start)->format('H:i');
            $breakEnd = \Carbon\Carbon::parse($schedule->break_end)->format('H:i');
        }

        // Генерувати доступні часові слоти
        $availableSlots = $this->generateTimeSlots(
            $date, 
            $service->duration, 
            $busySlots,
            $workStartTime,
            $workEndTime,
            $minBreak,
            30,
            $breakStart,
            $breakEnd
        );

        try {
            return response()->json([
                'available_slots' => $availableSlots,
                'service_duration' => $service->duration,
                'working_hours' => [
                    'start' => $workStartTime,
                    'end' => $workEndTime,
                ],
                'min_break' => $minBreak,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in AvailableTimeController::index (generating slots)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'error' => 'Помилка при отриманні доступного часу. Спробуйте ще раз або зверніться до адміністратора.',
            ], 500);
        }
    }

    /**
     * Generate available time slots.
     */
    private function generateTimeSlots($date, $serviceDuration, $busySlots, $workStartTime = '09:00', $workEndTime = '18:00', $minBreak = 15, $intervalMinutes = 30, $breakStart = null, $breakEnd = null)
    {
        $slots = [];
        $dateObj = \Carbon\Carbon::parse($date);
        $now = now();

        // Парсинг робочих годин
        $startHour = (int) explode(':', $workStartTime)[0];
        $startMinute = (int) explode(':', $workStartTime)[1];
        $endHour = (int) explode(':', $workEndTime)[0];
        $endMinute = (int) explode(':', $workEndTime)[1];

        // Якщо дата сьогодні, почати з поточного часу + 1 година
        if ($dateObj->isToday()) {
            $startTime = $now->copy()->addHour()->startOfHour();
            $workStart = $dateObj->copy()->hour($startHour)->minute($startMinute);
            if ($startTime->lt($workStart)) {
                $startTime = $workStart;
            }
        } else {
            $startTime = $dateObj->copy()->hour($startHour)->minute($startMinute);
        }

        $endTime = $dateObj->copy()->hour($endHour)->minute($endMinute);

        while ($startTime->lt($endTime)) {
            $slotEnd = $startTime->copy()->addMinutes($serviceDuration);
            
            // Перевірка, чи слот не виходить за межі робочого дня
            if ($slotEnd->gt($endTime)) {
                break;
            }

            // Перевірка, чи слот не попадає в обідню перерву
            if ($breakStart && $breakEnd) {
                $breakStartTime = \Carbon\Carbon::parse($date . ' ' . $breakStart);
                $breakEndTime = \Carbon\Carbon::parse($date . ' ' . $breakEnd);
                
                if ($startTime->lt($breakEndTime) && $slotEnd->gt($breakStartTime)) {
                    $startTime->addMinutes($intervalMinutes);
                    continue;
                }
            }

            // Перевірка, чи слот не перекривається з зайнятими слотами (з урахуванням мінімального проміжку)
            $isAvailable = true;
            foreach ($busySlots as $busySlot) {
                $busyStart = \Carbon\Carbon::parse($date . ' ' . $busySlot['start']);
                $busyEndWithBreak = \Carbon\Carbon::parse($date . ' ' . $busySlot['end_with_break']);

                // Перевірка перекриття з урахуванням мінімального проміжку
                // Слот доступний, якщо він не перекривається з зайнятим слотом + проміжок
                if ($startTime->lt($busyEndWithBreak) && $slotEnd->gt($busyStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $slots[] = [
                    'time' => $startTime->format('H:i'),
                    'display' => $startTime->format('H:i'),
                ];
            }

            // Перехід до наступного слота
            $startTime->addMinutes($intervalMinutes);
        }

        return $slots;
    }

    /**
     * Check if specific time is available.
     */
    #[OA\Post(
        path: "/api/check-availability",
        summary: "Перевірити доступність конкретного часу",
        description: "Перевіряє, чи доступний конкретний час для бронювання",
        tags: ["Available Time"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["employee_id", "service_id", "appointment_date", "appointment_time"],
                properties: [
                    new OA\Property(property: "employee_id", type: "integer", example: 1),
                    new OA\Property(property: "service_id", type: "integer", example: 1),
                    new OA\Property(property: "appointment_date", type: "string", format: "date", example: "2024-12-25"),
                    new OA\Property(property: "appointment_time", type: "string", format: "time", example: "10:00"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Результат перевірки",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "available", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Час доступний для бронювання."),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Помилка валідації"),
        ]
    )]
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'service_id' => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $service = Service::findOrFail($request->service_id);
        $date = $request->appointment_date;
        $time = $request->appointment_time;

        // Перевірка, чи майстер надає цю послугу
        if (!$employee->services()->where('services.id', $service->id)->exists()) {
            return response()->json([
                'available' => false,
                'message' => 'Цей майстер не надає обрану послугу.',
            ]);
        }

        // Нормалізувати дату та час перед парсингом
        $normalizedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
        $normalizedTime = preg_replace('/^.*?(\d{2}:\d{2}(?::\d{2})?).*$/', '$1', $time);
        if (strlen($normalizedTime) === 5) {
            $normalizedTime .= ':00';
        }
        $appointmentDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $normalizedDate . ' ' . $normalizedTime);
        $endTime = $appointmentDateTime->copy()->addMinutes($service->duration);
        $minBreak = $employee->min_break_between_appointments ?? 15;
        $dateObj = \Carbon\Carbon::parse($date);

        // Перевірка, чи час не в минулому
        if ($appointmentDateTime->isPast()) {
            return response()->json([
                'available' => false,
                'message' => 'Не можна бронювати час в минулому.',
            ]);
        }

        // Отримати день тижня для дати
        $dayOfWeek = $dateObj->dayOfWeekIso;
        
        // Отримати розклад для цього дня тижня
        $schedule = $employee->scheduleForDay($dayOfWeek);
        
        // Перевірка робочих годин майстра
        if ($schedule && $schedule->is_working) {
            $workStartTime = $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '09:00';
            $workEndTime = $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '18:00';
        } else {
            if ($schedule && !$schedule->is_working) {
                return response()->json([
                    'available' => false,
                    'message' => 'Майстер не працює в цей день.',
                ]);
            }
            
            $workStartTime = $employee->work_start_time ?? '09:00:00';
            $workEndTime = $employee->work_end_time ?? '18:00:00';
            $workStartTime = \Carbon\Carbon::parse($workStartTime)->format('H:i');
            $workEndTime = \Carbon\Carbon::parse($workEndTime)->format('H:i');
        }
        
        $workStart = \Carbon\Carbon::parse($date . ' ' . $workStartTime);
        $workEnd = \Carbon\Carbon::parse($date . ' ' . $workEndTime);

        if ($appointmentDateTime->lt($workStart) || $endTime->gt($workEnd)) {
            return response()->json([
                'available' => false,
                'message' => 'Обраний час виходить за межі робочих годин майстра (' . $workStartTime . ' - ' . $workEndTime . ').',
            ]);
        }

        // Перевірка обідньої перерви
        if ($schedule && $schedule->break_start && $schedule->break_end) {
            $breakStart = \Carbon\Carbon::parse($schedule->break_start);
            $breakEnd = \Carbon\Carbon::parse($schedule->break_end);
            
            if ($appointmentDateTime->lt($breakEnd) && $endTime->gt($breakStart)) {
                return response()->json([
                    'available' => false,
                    'message' => 'Обраний час попадає в обідню перерву майстра.',
                ]);
            }
        }

        // Перевірка блокувань часу (відпустка, лікарня)
        $timeBlocks = TimeBlock::forEmployee($employee->id)
            ->activeOnDate($date)
            ->get();
            
        foreach ($timeBlocks as $block) {
            if ($block->coversTime($date, $time)) {
                $typeNames = [
                    'vacation' => 'відпустка',
                    'sick_leave' => 'лікарня',
                    'other' => 'блокування',
                ];
                
                return response()->json([
                    'available' => false,
                    'message' => 'Обраний час заблоковано (' . ($typeNames[$block->type] ?? 'блокування') . ').',
                ]);
            }
        }

        // Перевірка на конфлікти з урахуванням мінімального проміжку
        $conflictingAppointments = Appointment::where('employee_id', $employee->id)
            ->where('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->get()
            ->filter(function ($appointment) use ($appointmentDateTime, $endTime, $minBreak) {
                // appointment_time завжди у форматі H:i:s завдяки accessor
                $timeString = $appointment->appointment_time;
                
                // Додаткова перевірка на випадок, якщо accessor не спрацював
                if ($timeString && strlen($timeString) > 8) {
                    // Якщо містить подвійний час, витягнути останній
                    if (preg_match('/(\d{2}:\d{2}:\d{2})\s*$/', $timeString, $matches)) {
                        $timeString = $matches[1];
                    } elseif (preg_match_all('/(\d{2}:\d{2}:\d{2}|\d{2}:\d{2})/', $timeString, $allMatches)) {
                        $lastMatch = end($allMatches[0]);
                        $timeString = strlen($lastMatch) === 5 ? $lastMatch . ':00' : $lastMatch;
                    }
                }
                
                // Нормалізувати дату - завжди тільки дата без часу
                $aptDate = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
                $appStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $aptDate . ' ' . $timeString);
                $appEnd = $appStart->copy()->addMinutes($appointment->duration);
                $appEndWithBreak = $appEnd->copy()->addMinutes($minBreak);

                // Перевірка перекриття з урахуванням мінімального проміжку
                return $appointmentDateTime->lt($appEndWithBreak) && $endTime->gt($appStart);
            });

        if ($conflictingAppointments->isNotEmpty()) {
            return response()->json([
                'available' => false,
                'message' => 'Обраний час зайнятий або не відповідає мінімальному проміжку між записами (' . $minBreak . ' хв). Оберіть інший час.',
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Час доступний для бронювання.',
        ]);
    }
}
