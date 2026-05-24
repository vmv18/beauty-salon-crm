<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use OpenApi\Attributes as OA;

class ClientAppointmentController extends Controller
{
    /**
     * Get appointments for a client.
     */
    #[OA\Get(
        path: "/api/clients/{client}/appointments",
        summary: "Отримати записи клієнта",
        description: "Повертає список записів для вказаного клієнта",
        tags: ["Clients"],
        parameters: [
            new OA\Parameter(
                name: "client",
                in: "path",
                required: true,
                description: "ID клієнта",
                schema: new OA\Schema(type: "integer", example: 1)
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
                            new OA\Property(property: "service_name", type: "string", example: "Стрижка"),
                            new OA\Property(property: "date", type: "string", example: "25.12.2024"),
                        ]
                    )
                )
            ),
            new OA\Response(response: 404, description: "Клієнт не знайдений"),
        ]
    )]
    public function __invoke(Client $client)
    {
        $appointments = $client->appointments()
            ->whereIn('status', ['scheduled', 'confirmed', 'completed'])
            ->with('service')
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'service_name' => $appointment->service->name,
                    'date' => $appointment->appointment_date->format('d.m.Y'),
                ];
            });
        
        return response()->json($appointments);
    }
}

