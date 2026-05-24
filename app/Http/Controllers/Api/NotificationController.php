<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class NotificationController extends Controller
{
    /**
     * Get unread notifications count.
     */
    #[OA\Get(
        path: "/api/notifications/unread-count",
        summary: "Отримати кількість непрочитаних сповіщень",
        description: "Повертає кількість непрочитаних сповіщень для поточного користувача",
        tags: ["Notifications"],
        security: [["session" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успішна відповідь",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "count", type: "integer", example: 5),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Не авторизовано"),
        ]
    )]
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Get notifications.
     */
    #[OA\Get(
        path: "/api/notifications",
        summary: "Отримати список сповіщень",
        description: "Повертає список сповіщень для поточного користувача",
        tags: ["Notifications"],
        security: [["session" => []]],
        parameters: [
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                description: "Максимальна кількість сповіщень",
                schema: new OA\Schema(type: "integer", default: 10, example: 10)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успішна відповідь",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "notifications", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "string", example: "uuid"),
                                new OA\Property(property: "type", type: "string", example: "new_appointment"),
                                new OA\Property(property: "message", type: "string", example: "Новий запис"),
                                new OA\Property(property: "url", type: "string", example: "/appointments/1"),
                                new OA\Property(property: "read_at", type: "string", nullable: true, example: "2024-12-25 10:00:00"),
                                new OA\Property(property: "created_at", type: "string", example: "25.12.2024 10:00"),
                            ]
                        )),
                        new OA\Property(property: "unread_count", type: "integer", example: 5),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Не авторизовано"),
        ]
    )]
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $user = Auth::user();
        $limit = $request->get('limit', 10);
        
        $notifications = $user->notifications()
            ->take($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'unknown',
                    'message' => $notification->data['message'] ?? '',
                    'url' => $notification->data['url'] ?? '#',
                    'read_at' => $notification->read_at?->format('Y-m-d H:i:s'),
                    'created_at' => $notification->created_at->format('d.m.Y H:i'),
                    'data' => $notification->data,
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    #[OA\Post(
        path: "/api/notifications/{id}/read",
        summary: "Позначити сповіщення як прочитане",
        description: "Позначає конкретне сповіщення як прочитане",
        tags: ["Notifications"],
        security: [["session" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID сповіщення",
                schema: new OA\Schema(type: "string", example: "uuid")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успішна відповідь",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "unread_count", type: "integer", example: 4),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Сповіщення не знайдено"),
        ]
    )]
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'unread_count' => Auth::user()->unreadNotifications()->count(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification not found',
        ], 404);
    }

    /**
     * Mark all notifications as read.
     */
    #[OA\Post(
        path: "/api/notifications/mark-all-read",
        summary: "Позначити всі сповіщення як прочитані",
        description: "Позначає всі сповіщення користувача як прочитані",
        tags: ["Notifications"],
        security: [["session" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успішна відповідь",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "unread_count", type: "integer", example: 0),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Не авторизовано"),
        ]
    )]
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
