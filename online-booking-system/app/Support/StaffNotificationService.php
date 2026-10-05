<?php

namespace App\Support;

use App\Models\Staff;
use App\Notifications\StaffNotification;
use Illuminate\Support\Collection;

class StaffNotificationService
{
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
