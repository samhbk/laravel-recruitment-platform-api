<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DatabaseNotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Notifications\DatabaseNotification;
use Symfony\Component\HttpFoundation\Response;

final class NotificationController extends Controller
{
    private const PER_PAGE = 30;

    public function index(Request $request): AnonymousResourceCollection
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(self::PER_PAGE);

        return DatabaseNotificationResource::collection($notifications);
    }

    public function markRead(DatabaseNotification $notification): Response
    {
        $notification->markAsRead();

        return response()->noContent();
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked read']);
    }
}
