<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use App\Support\StaffNotificationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarkStaffModuleNotificationsRead
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $module = match ($request->route()?->getName()) {
            'admin.reservations', 'employee.reservation' => 'reservations',
            'admin.rooms', 'employee.room-status', 'housekeeping.room-status-update', 'housekeeping.cleaning-history' => 'rooms',
            'admin.refunds', 'employee.refunds' => 'refunds',
            'admin.messages', 'employee.messages', 'housekeeping.messages' => 'messages',
            'admin.notifications' => 'notifications',
            'employee.guest-requests', 'employee.guest-requests.show',
            'housekeeping.guest-requests', 'housekeeping.guest-requests.show' => 'requests',
            'housekeeping.assigned-rooms' => 'tasks',
            default => null,
        };

        if ($user instanceof Staff && $request->isMethod('GET') && $module !== null) {
            StaffNotificationService::markModuleRead($user, $module);
        }

        return $next($request);
    }
}