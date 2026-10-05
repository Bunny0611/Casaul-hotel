<?php

namespace App\Http\Controllers;

use App\Models\DiningReservation;
use App\Models\EventReservation;
use App\Models\FacilityReservation;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\Staff;
use App\Support\StaffNotificationService;
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
            ->map(function (DatabaseNotification $notification) use ($user) {
                $data = is_array($notification->data) ? $notification->data : [];
                $url = $data['url'] ?? null;
                $relatedId = (int) ($data['related_id'] ?? 0);
                $path = $url ? parse_url($url, PHP_URL_PATH) : null;
                $query = $url ? parse_url($url, PHP_URL_QUERY) : null;
                $roomStatusPaths = ['/employee/room-status', '/housekeeping/room-status-update'];
                $reservationTargetPaths = ['/employee/reservation'];

                $resolveReservationType = function (?string $relatedType) {
                    if ($relatedType === null || $relatedType === '') {
                        return null;
                    }

                    $tableMappings = [
                        Reservation::class => 'reservations',
                        RoomReservation::class => 'room_reservations',
                        FacilityReservation::class => 'facility_reservations',
                        EventReservation::class => 'event_reservations',
                        DiningReservation::class => 'dining_reservations',
                    ];

                    if (isset($tableMappings[$relatedType])) {
                        return $tableMappings[$relatedType];
                    }

                    foreach ($tableMappings as $class => $tableName) {
                        if (strcasecmp($relatedType, $class) === 0 || strcasecmp($relatedType, $tableName) === 0) {
                            return $tableName;
                        }
                    }

                    return null;
                };

                $resolveReservationTab = function (?string $reservationType) {
                    return match ($reservationType) {
                        'facility_reservations' => 'facilities',
                        'event_reservations' => 'event',
                        'dining_reservations' => 'dining',
                        'room_reservations', 'reservations' => 'rooms',
                        default => 'rooms',
                    };
                };

                if ($url && in_array($path, $reservationTargetPaths, true)) {
                    $existingQuery = [];
                    if ($query) {
                        parse_str($query, $existingQuery);
                    }

                    $reference = (string) ($data['reference'] ?? '');
                    $referenceType = null;
                    if ($relatedId === 0 && preg_match('/^reservation(?:-[^:]+)?: (?:(room_reservations|facility_reservations|event_reservations|dining_reservations|reservations):)?([0-9]+)$/x', $reference, $matches)) {
                        $referenceType = $matches[1] ?? null;
                        $relatedId = (int) $matches[2];
                    }

                    $reservationType = $resolveReservationType((string) ($data['reservation_type'] ?? $data['related_type'] ?? $referenceType ?? ''));
                    $reservationType ??= $resolveReservationType((string) ($existingQuery['reservation_type'] ?? ''));

                    if ($relatedId > 0 && $reservationType === null) {
                        $reservationModels = [
                            'reservations' => Reservation::class,
                            'room_reservations' => RoomReservation::class,
                            'facility_reservations' => FacilityReservation::class,
                            'event_reservations' => EventReservation::class,
                            'dining_reservations' => DiningReservation::class,
                        ];
                        $message = (string) ($data['message'] ?? '');
                        $matches = [];

                        foreach ($reservationModels as $tableName => $modelClass) {
                            $candidate = $modelClass::query()->find($relatedId);
                            if ($candidate && $candidate->guest_name && stripos($message, $candidate->guest_name) !== false) {
                                $matches[] = $tableName;
                            }
                        }

                        if (count($matches) === 1) {
                            $reservationType = $matches[0];
                        }
                    }

                    if ($relatedId > 0 && $reservationType !== null) {
                        $existingQuery['tab'] = $data['tab'] ?? $existingQuery['tab'] ?? $resolveReservationTab($reservationType);
                        $existingQuery['reservation_id'] = (string) $relatedId;
                        $existingQuery['reservation_type'] = $reservationType;
                        $url = $path . '?' . http_build_query($existingQuery);
                    }
                }

                if ($url && in_array($path, $roomStatusPaths, true) && $relatedId === 0) {
                    $roomNumbers = [];
                    preg_match_all('/\bRoom\s*(?:#)?\s*([0-9]{1,4})\b/i', (string) ($data['message'] ?? ''), $matches);
                    if (!empty($matches[1])) {
                        $roomNumbers = array_values(array_filter(array_map('trim', $matches[1])));
                    }

                    if (!empty($roomNumbers)) {
                        $candidateRoomNumber = end($roomNumbers);
                        $relatedId = (int) Room::query()
                            ->where('room_number', (string) $candidateRoomNumber)
                            ->orderByDesc('id')
                            ->value('id');
                    }
                }

                if ($url && in_array($path, $roomStatusPaths, true) && $relatedId > 0
                    && in_array($data['related_type'] ?? Room::class, [Room::class, ''], true)) {
                    $url = $path . '?room_id=' . $relatedId;
                } elseif ($path && !in_array($path, $reservationTargetPaths, true)
                    && (str_starts_with($path, '/employee/') || str_starts_with($path, '/housekeeping/'))) {
                    $url = $path . ($query ? '?' . $query : '');
                }

                if ($user->role === 'admin' && in_array($path, $roomStatusPaths, true)) {
                    $existingQuery = [];
                    if ($query) {
                        parse_str($query, $existingQuery);
                    }

                    $roomId = $relatedId ?: (int) ($existingQuery['room_id'] ?? 0);
                    $url = route('admin.rooms', $roomId > 0 ? ['room_id' => $roomId] : []);
                } elseif ($user->role === 'admin' && $path === '/employee/reservation') {
                    $existingQuery = [];
                    if ($query) {
                        parse_str($query, $existingQuery);
                    }

                    $url = route('admin.reservations', $existingQuery);
                }

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'type' => $data['type'] ?? 'general',
                    'url' => $url,
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
            'module_counts' => StaffNotificationService::unreadSidebarCounts($user),
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
