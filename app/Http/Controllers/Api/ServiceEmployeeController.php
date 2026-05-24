<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use OpenApi\Attributes as OA;

class ServiceEmployeeController extends Controller
{
    /**
     * Get employees for a service.
     */
    #[OA\Get(
        path: "/api/services/{service}/employees",
        summary: "Отримати майстрів за послугою",
        description: "Повертає список активних майстрів, які надають вказану послугу",
        tags: ["Services"],
        parameters: [
            new OA\Parameter(
                name: "service",
                in: "path",
                required: true,
                description: "ID послуги",
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
                            new OA\Property(property: "name", type: "string", example: "Майстер"),
                            new OA\Property(property: "specialization", type: "string", nullable: true, example: "Стрижка"),
                            new OA\Property(property: "rating", type: "string", example: "4.8"),
                        ]
                    )
                )
            ),
            new OA\Response(response: 404, description: "Послуга не знайдена"),
        ]
    )]
    public function __invoke(Service $service)
    {
        $employees = $service->employees()
            ->where('employees.status', 'active')
            ->with('user')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->user->name,
                    'specialization' => $employee->specialization,
                    'rating' => number_format($employee->rating, 1),
                ];
            });
        
        return response()->json($employees);
    }
}

