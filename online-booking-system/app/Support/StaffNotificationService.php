<?php

namespace App\Support;

use App\Models\Staff;
use App\Notifications\StaffNotification;
use Illuminate\Support\Collection;

class StaffNotificationService
{
    private const SIDEBAR_MODULES = [
        'reservations',
        'refunds',
        'rooms',
        'requests',
        'messages',
        'notifications',
        'tasks',
    ];

    /**
     * @param  iterable<int, Staff>|Collection<int, Staff>|Staff|null  $users
     */
    public static function notifyUsers(iterable|Collection|Staff|null $users, string $title, string $message, array $payload = []): void
    {
        foreach (self::normalizeUsers($users) as $user) {
            $reference = $payload['reference'] ?? md5($title . $message . $user->id . microtime(true));
            $existing = $user->notifications()->where('type', StaffNotification::class)->get()->contains(
                fn ($notification) => ($notification->data['reference'] ?? null) === $reference
            );

            if ($existing) {
                continue;
            }

            $user->notify(new StaffNotification($title, $message, array_merge([
                'type' => $payload['type'] ?? 'general',
                'reference' => $reference,
                'url' => $payload['url'] ?? null,
                'related_id' => $payload['related_id'] ?? null,
                'related_type' => $payload['related_type'] ?? null,
                'module' => $payload['module'] ?? null,
                'icon' => $payload['icon'] ?? 'fas fa-bell',
                'action_label' => $payload['action_label'] ?? 'View',
            ], $payload)));
        }
    }

    public static function notifyEmployees(string $title, string $message, array $payload = []): void
    {
        self::notifyUsers(
            Staff::query()->where('role', 'employee')->where('is_active', true)->get(),
            $title,
            $message,
            $payload
        );
    }

    public static function notifyHousekeeping(string $title, string $message, array $payload = []): void
    {
        self::notifyUsers(
            Staff::query()->where('role', 'housekeeping')->where('is_active', true)->get(),
            $title,
            $message,
            $payload
        );
    }

    public static function notifyAdmins(string $title, string $message, array $payload = []): void
    {
        self::notifyUsers(
            Staff::query()->where('role', 'admin')->where('is_active', true)->get(),
            $title,
            $message,
            $payload
        );
    }

    public static function unreadSidebarCounts(Staff $user): array
    {
        $counts = array_fill_keys(self::SIDEBAR_MODULES, 0);

        foreach ($user->unreadNotifications()->get(['data']) as $notification) {
            $module = self::moduleForNotification($notification->data, $user);
            $counts[$module]++;
        }

        return $counts;
    }

    public static function markModuleRead(Staff $user, string $module): void
    {
        $notificationIds = $user->unreadNotifications()
            ->get(['id', 'data'])
            ->filter(fn ($notification) => self::moduleForNotification($notification->data, $user) === $module)
            ->pluck('id');

        if ($notificationIds->isNotEmpty()) {
            $user->unreadNotifications()
                ->whereIn('id', $notificationIds)
                ->update(['read_at' => now(), 'updated_at' => now()]);
        }
    }

    public static function moduleForNotification(array $data, Staff $user): string
    {
        $module = strtolower((string) ($data['module'] ?? ''));
        if (in_array($module, self::SIDEBAR_MODULES, true)) {
            if ($user->role === 'admin' && $module === 'requests') {
                return 'notifications';
            }

            return $module;
        }

        $type = strtolower((string) ($data['type'] ?? ''));
        $path = strtolower((string) parse_url((string) ($data['url'] ?? ''), PHP_URL_PATH));

        if (str_contains($path, 'guest-requests') || in_array($type, ['request', 'guest_request'], true)) {
            return $user->role === 'admin' ? 'notifications' : 'requests';
        }

        if (str_contains($path, 'messages') || in_array($type, ['message', 'chatbot', 'contact'], true)) {
            return 'messages';
        }

        if (str_contains($path, 'refund') || str_contains($path, 'payment')
            || in_array($type, ['refund', 'payment'], true)) {
            return 'refunds';
        }

        if (str_contains($path, 'reservation') || $type === 'reservation') {
            return 'reservations';
        }

        if (str_contains($path, 'room-status') || str_contains($path, 'assigned-rooms')
            || str_contains($path, 'cleaning-history') || in_array($type, ['housekeeping', 'room_status'], true)) {
            return 'rooms';
        }

        if ($type === 'task') {
            return 'tasks';
        }

        return 'notifications';
    }

    /**
     * @return array<int, Staff>
     */
    protected static function normalizeUsers(iterable|Collection|Staff|null $users): array
    {
        if ($users instanceof Staff) {
            return [$users];
        }

        if ($users === null) {
            return [];
        }

        if ($users instanceof Collection) {
            return $users->all();
        }

        return iterator_to_array($users);
    }
}
