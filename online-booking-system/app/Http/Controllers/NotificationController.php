<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user instanceof Staff, 403);

        $items = $user->notifications()
            ->latest('created_at')
            ->limit(20)
            ->get()
            ->map(function (DatabaseNotification $notification) {
                $data = is_array($notification->data) ? $notification->data : [];

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'type' => $data['type'] ?? 'general',
                    'url' => $data['url'] ?? null,
                    'icon' => $data['icon'] ?? 'fas fa-bell',
                    'action_label' => $data['action_label'] ?? 'View',
                    'is_read' => ! is_null($notification->read_at),
                    'read_at' => $notification->read_at?->toISOString(),
                    'created_at' => $notification->created_at?->toISOString(),
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'data' => $items,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $user = $request->user();
        abort_unless($user instanceof Staff, 403);

        $notification = $user->notifications()->whereKey($id)->firstOrFail();
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        abort_unless($user instanceof Staff, 403);

        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    public function stream(Request $request)
    {
        $user = $request->user();
        abort_unless($user instanceof Staff, 403);

        $lastId = (int) $request->query('last_id', 0);

        return response()->stream(function () use ($user, $lastId) {
            while (true) {
                $newNotifications = $user->notifications()
                    ->where('id', '>', $lastId)
                    ->latest('id')
                    ->get();

                foreach ($newNotifications as $notification) {
                    echo "event: notification\n";
                    echo 'data: ' . json_encode([
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? 'Notification',
                        'message' => $notification->data['message'] ?? '',
                        'url' => $notification->data['url'] ?? null,
                    ]) . "\n\n";
                    flush();
                    $lastId = max($lastId, (int) $notification->id);
                }

                if (connection_aborted()) {
                    break;
                }

                sleep(5);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
