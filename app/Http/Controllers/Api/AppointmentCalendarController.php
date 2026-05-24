<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AppointmentCalendarController extends Controller
{
    /**
     * Get appointments in FullCalendar format.
     */
    #[OA\Get(
        path: "/api/appointments-calendar",
        summary: "Отримати записи для календаря",
        description: "Повертає записи у форматі FullCalendar для відображення в календарі",
        tags: ["Appointments"],
        security: [["session" => []]],
        parameters: [
            new OA\Parameter(
                name: "employee_id",
                in: "query",
                required: false,
                description: "ID майстра для фільтрації",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
            new OA\Parameter(
                name: "status",
                in: "query",
                required: false,
                description: "Статус запису",
                schema: new OA\Schema(type: "string", enum: ["scheduled", "confirmed", "completed", "cancelled"], example: "scheduled")
            ),
            new OA\Parameter(
                name: "start",
                in: "query",
                required: false,
                description: "Початкова дата діапазону",
                schema: new OA\Schema(type: "string", format: "date", example: "2024-12-01")
            ),
            new OA\Parameter(
                name: "end",
                in: "query",
                required: false,
                description: "Кінцева дата діапазону",
                schema: new OA\Schema(type: "string", format: "date", example: "2024-12-31")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успішна відповідь",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "title", type: "string", example: "Іван Іванов - Стрижка"),
                            new OA\Property(property: "start", type: "string", format: "date-time", example: "2024-12-25T10:00:00+00:00"),
                            new OA\Property(property: "end", type: "string", format: "date-time", example: "2024-12-25T11:00:00+00:00"),
                            new OA\Property(property: "color", type: "string", example: "#667eea"),
                            new OA\Property(property: "extendedProps", type: "object", properties: [
                                new OA\Property(property: "client_name", type: "string", example: "Іван Іванов"),
                                new OA\Property(property: "employee_name", type: "string", example: "Майстер"),
                                new OA\Property(property: "service_name", type: "string", example: "Стрижка"),
                                new OA\Property(property: "status", type: "string", example: "scheduled"),
                                new OA\Property(property: "price", type: "number", format: "float", example: 800.00),
                                new OA\Property(property: "notes", type: "string", nullable: true, example: "Примітки"),
                            ]),
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: "Не авторизовано"),
            new OA\Response(response: 403, description: "Доступ заборонено"),
        ]
    )]
    public function index(Request $request)
    {
        $query = Appointment::with(['client.user', 'employee.user', 'service']);

        // Фільтр по майстру
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Фільтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фільтр по діапазону дат (для FullCalendar)
        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('appointment_date', [
                $request->start,
                $request->end
            ]);
        }

        $appointments = $query->get();

        // Форматування для FullCalendar
        $events = $appointments->map(function ($appointment) {
            // appointment_time завжди у форматі H:i:s завдяки accessor
            $timeString = $appointment->appointment_time;
            // Нормалізувати дату - завжди тільки дата без часу
            $aptDate = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
            $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $aptDate . ' ' . $timeString);
            $end = $start->copy()->addMinutes($appointment->duration);

            // Визначення кольору залежно від статусу
            $color = match($appointment->status) {
                'scheduled' => '#667eea',
                'confirmed' => '#28a745',
                'completed' => '#17a2b8',
                'cancelled' => '#dc3545',
                default => '#6c757d',
            };

            return [
                'id' => $appointment->id,
                'title' => $appointment->client->user->name . ' - ' . $appointment->service->name,
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'color' => $color,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'client_name' => $appointment->client->user->name,
                    'employee_name' => $appointment->employee->user->name,
                    'service_name' => $appointment->service->name,
                    'status' => $appointment->status,
                    'price' => $appointment->price,
                    'notes' => $appointment->notes,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Update appointment time (for drag & drop).
     */
    #[OA\Post(
        path: "/api/appointments/{appointment}/update-time",
        summary: "Оновити час запису",
        description: "Оновлює час запису (використовується для drag & drop в календарі)",
        tags: ["Appointments"],
        security: [["session" => []]],
        parameters: [
            new OA\Parameter(
                name: "appointment",
                in: "path",
                required: true,
                description: "ID запису",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["start"],
                properties: [
                    new OA\Property(property: "start", type: "string", format: "date-time", example: "2024-12-25T10:00:00+00:00"),
                    new OA\Property(property: "end", type: "string", format: "date-time", nullable: true, example: "2024-12-25T11:00:00+00:00"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Успішна відповідь",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Час запису оновлено."),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Помилка валідації"),
            new OA\Response(response: 500, description: "Помилка сервера"),
        ]
    )]
    public function updateTime(Request $request, Appointment $appointment)
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['nullable', 'date'],
        ]);

        try {
            $start = \Carbon\Carbon::parse($request->start);
            $appointmentDate = $start->toDateString();
            $appointmentTime = $start->toTimeString();
            
            // Перевірка доступності нового часу
            $conflictingAppointment = Appointment::where('employee_id', $appointment->employee_id)
                ->where('appointment_date', $appointmentDate)
                ->where('appointment_time', $appointmentTime)
                ->where('id', '!=', $appointment->id)
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->first();

            if ($conflictingAppointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Цей час вже зайнятий іншим записом.',
                ], 422);
            }

            // Перевірка на перекриття часу
            $service = $appointment->service;
            $endTime = $start->copy()->addMinutes($service->duration);

            $overlappingAppointments = Appointment::where('employee_id', $appointment->employee_id)
                ->where('appointment_date', $appointmentDate)
                ->where('id', '!=', $appointment->id)
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->get()
                ->filter(function ($apt) use ($start, $endTime) {
                    // Безпечний парсинг часу - витягнути тільки час якщо це datetime
                    $timeString = $apt->appointment_time;
                    if (strlen($timeString) > 8) {
                        $timeString = \Carbon\Carbon::parse($timeString)->format('H:i:s');
                    }
                    // Нормалізувати дату - завжди тільки дата без часу
                    $aptDate = \Carbon\Carbon::parse($apt->appointment_date)->format('Y-m-d');
                    $appStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $aptDate . ' ' . $timeString);
                    $appEnd = $appStart->copy()->addMinutes($apt->duration);

                    return $start->lt($appEnd) && $endTime->gt($appStart);
                });

            if ($overlappingAppointments->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Обраний час перекривається з іншим записом.',
                ], 422);
            }
            
            $appointment->update([
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
            ]);

            // Якщо передано end, оновити duration
            if ($request->filled('end')) {
                $end = \Carbon\Carbon::parse($request->end);
                $duration = $start->diffInMinutes($end);
                $appointment->update(['duration' => $duration]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Час запису оновлено.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Помилка при оновленні: ' . $e->getMessage(),
            ], 500);
        }
    }
}
