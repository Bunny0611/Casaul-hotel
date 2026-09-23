<?php

namespace App\Http\Controllers;

use App\Mail\ReservationConfirmed;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\RoomReservation;
use App\Models\EventReservation;
use App\Models\FacilityReservation;
use App\Models\DiningReservation;
use App\Models\Message;
use App\Models\MaintenanceReport;
use App\Models\Staff;
use App\Models\InventoryItem;
use App\Models\Facility;
use App\Models\Event;
use App\Models\DiningTable;
use App\Models\DiningSchedule;
use App\Models\DiningMenu;
use App\Models\ReservationDiningItem;
use App\Models\GuestRequest;
use App\Models\Payment;
use App\Models\Refund;
use App\Support\ReservationPricing;

class AdminController extends Controller
{
    public function unifiedReservations(?string $from = null, ?string $to = null)
    {
        $sources = collect([
            ['category' => 'rooms', 'rows' => RoomReservation::with('room')->get()],
            ['category' => 'facilities', 'rows' => FacilityReservation::with('facility')->get()],
            ['category' => 'event', 'rows' => EventReservation::with('event')->get()],
            ['category' => 'dining', 'rows' => DiningReservation::with('diningItems.diningMenu')->get()],
            ['category' => null, 'rows' => Reservation::with(['room', 'facility', 'event', 'diningItems'])->get()],
        ]);

        return $sources->flatMap(function (array $source) use ($from, $to) {
            return $source['rows']->filter(function ($reservation) use ($from, $to) {
                if ($from && optional($reservation->created_at)->lt(Carbon::parse($from)->startOfDay())) {
                    return false;
                }
                if ($to && optional($reservation->created_at)->gt(Carbon::parse($to)->endOfDay())) {
                    return false;
                }

                return true;
            })->map(function ($reservation) use ($source) {
                $category = $source['category'] ?: ($reservation->category ?: ($reservation->room_id ? 'rooms' : null));
                if (!$category && $reservation->facility_id) {
                    $category = 'facilities';
                } elseif (!$category && $reservation->event_id) {
                    $category = 'event';
                } elseif (!$category && ($reservation->dining_id || $reservation->dining_area || $reservation->dining_schedule)) {
                    $category = 'dining';
                }

                $reservation->resource_category = $category;

                return $reservation;
            });
        })->filter(fn ($reservation) => $reservation->resource_category !== null)
            ->unique(function ($reservation) {
                return implode('|', [
                    $reservation->resource_category,
                    $reservation->guest_email,
                    optional($reservation->check_in)->toDateString(),
                    $reservation->room_id,
                    $reservation->facility_id,
                    $reservation->event_id,
                    $reservation->dining_area,
                    $reservation->total_amount,
                ]);
            })->values();
    }

    public function applyResourceReservationStatuses($rooms, $facilities, $events, $diningTables): array
    {
        $activeStatuses = ['pending', 'confirmed', 'checked-in'];
        $today = today();
        $reservations = $this->unifiedReservations();
        $active = $reservations->filter(function ($reservation) use ($activeStatuses, $today) {
            return in_array(strtolower((string) $reservation->status), $activeStatuses, true)
                && (!$reservation->check_out || Carbon::parse($reservation->check_out)->gte($today));
        });
        $statusFor = static fn ($reservation) => strtolower((string) $reservation->status) === 'checked-in' ? 'occupied' : 'reserved';

        $rooms->each(function ($room) use ($active, $statusFor) {
            $booking = $active->first(fn ($reservation) => $reservation->resource_category === 'rooms'
                && (int) $reservation->room_id === (int) $room->id);
            $baseStatus = strtolower((string) $room->status);
            if ($booking && !in_array($baseStatus, ['maintenance', 'out_of_order', 'blocked'], true)) {
                $room->status = $statusFor($booking);
                $room->detail_guest = $booking->guest_name ?: '—';
                $room->detail_checkin = optional($booking->check_in)->format('Y-m-d') ?: '—';
                $room->detail_checkout = optional($booking->check_out)->format('Y-m-d') ?: '—';
            }
        });

        $facilities->each(function ($facility) use ($active, $statusFor) {
            $booking = $active->first(fn ($reservation) => $reservation->resource_category === 'facilities'
                && (int) $reservation->facility_id === (int) $facility->id);
            $baseStatus = strtolower((string) $facility->status);
            if ($booking && !in_array($baseStatus, ['maintenance', 'unavailable'], true)) {
                $facility->status = $statusFor($booking);
            }
        });

        $events->each(function ($event) use ($active, $statusFor) {
            $booking = $active->first(fn ($reservation) => $reservation->resource_category === 'event'
                && (int) $reservation->event_id === (int) $event->id);
            $baseStatus = strtolower((string) $event->status);
            if ($booking && !in_array($baseStatus, ['maintenance', 'unavailable'], true)) {
                $event->status = $statusFor($booking);
            }
        });

        $diningTables->each(function ($table) use ($active, $statusFor) {
            $tableNumber = (string) $table->table_no;
            $booking = $active->first(function ($reservation) use ($tableNumber) {
                if ($reservation->resource_category !== 'dining') {
                    return false;
                }

                return collect(explode(',', (string) $reservation->dining_area))
                    ->map(fn ($value) => trim($value))
                    ->contains($tableNumber);
            });
            $baseStatus = strtolower((string) $table->status);
            if ($booking && !in_array($baseStatus, ['maintenance', 'unavailable'], true)) {
                $table->status = ucfirst($statusFor($booking));
            }
        });

        return compact('rooms', 'facilities', 'events', 'diningTables');
    }

    protected function dynamicRoomCounts($rooms): array
    {
        return [
            'total' => $rooms->count(),
            'available' => $rooms->filter(fn ($room) => strtolower((string) $room->status) === 'available'
                && in_array($room->cleaning_status, ['clean', 'ready'], true))->count(),
            'reserved' => $rooms->where('status', 'reserved')->count(),
            'occupied' => $rooms->where('status', 'occupied')->count(),
            'dirty' => $rooms->where('cleaning_status', 'dirty')->count(),
            'cleaning' => $rooms->where('cleaning_status', 'in_progress')->count(),
            'maintenance' => $rooms->filter(fn ($room) => in_array(strtolower((string) $room->status), ['maintenance', 'out_of_order'], true)
                || $room->cleaning_status === 'out_of_order')->count(),
        ];
    }

    public function dashboard()
    {
        $unifiedReservations = $this->unifiedReservations();
        $rooms = Room::orderBy('room_number')->get();
        $resources = $this->applyResourceReservationStatuses($rooms, Facility::get(), Event::get(), DiningTable::get());
        $rooms = $resources['rooms'];

        // === Core Stats ===
        $totalRevenue = $unifiedReservations->where('status', 'completed')->sum('total_amount') ?? 0;
        $availableRooms = $rooms->where('status', 'available')->count();
        $totalRooms = $rooms->count();
        $activeReservations = $unifiedReservations->where('status', 'confirmed')->count();
        $totalGuests = $unifiedReservations->pluck('guest_email')->filter()->unique()->count();
        $unreadMessages = Message::where('is_replied', false)->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round((($totalRooms - $availableRooms) / $totalRooms) * 100) : 0;

        // === Reservation Status Counts ===
        $pendingReservations = $unifiedReservations->where('status', 'pending')->count();
        $confirmedReservations = $unifiedReservations->where('status', 'confirmed')->count();
        $completedReservations = $unifiedReservations->where('status', 'completed')->count();
        $cancelledReservations = $unifiedReservations->where('status', 'cancelled')->count();

        // === Average Daily Revenue (Last 30 Days) ===
        // Use updated_at so recent completions/payments count toward the average.
        $thirtyDaysAgo = now()->subDays(30);
        $last30DaysRevenue = $unifiedReservations
            ->filter(fn ($reservation) => $reservation->status === 'completed' && $reservation->updated_at && $reservation->updated_at->gte($thirtyDaysAgo))
            ->sum('total_amount');
        $avgDailyRevenue = $last30DaysRevenue > 0 ? round($last30DaysRevenue / 30, 2) : 0;

        // === Room Type Distribution ===
        $roomTypes = Room::selectRaw('room_type, COUNT(*) as count')
            ->groupBy('room_type')
            ->pluck('count', 'room_type')
            ->toArray();

        // === Recent Reservations (Latest 5) ===
        $recentReservations = $unifiedReservations->sortByDesc('created_at')->take(5);

        return view('admin.dashboard', compact(
            'totalRevenue',
            'availableRooms',
            'totalRooms',
            'unreadMessages',
            'maintenanceRooms',
            'occupiedRooms',
            'occupancyRate',
            'pendingReservations',
            'confirmedReservations',
            'completedReservations',
            'cancelledReservations',
            'avgDailyRevenue',
            'roomTypes',
            'recentReservations',
            'activeReservations',
            'totalGuests'
        ));
    }

    public function employeeDashboard()
    {
        $rooms = Room::orderBy('room_number')->get();
        $resources = $this->applyResourceReservationStatuses($rooms, Facility::get(), Event::get(), DiningTable::get());
        $rooms = $resources['rooms'];
        $unifiedReservations = $this->unifiedReservations();
        $totalRooms = $rooms->count();
        $availableRooms = $rooms->where('status', 'available')->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();
        $todayArrivals = RoomReservation::whereDate('check_in', today())->count();
        $todayDepartures = RoomReservation::whereDate('check_out', today())->count();
        $pendingRequests = $unifiedReservations->where('status', 'pending')->count();
        $occupancyRate = $totalRooms > 0 ? min(100, round(($occupiedRooms / $totalRooms) * 100)) : 0;

        $recentActivity = $unifiedReservations->where('resource_category', 'rooms')
            ->sortByDesc('created_at')
            ->take(5)
            ->map(function ($reservation) {
                $roomNumber = $reservation->room?->room_number ?? 'N/A';

                return [
                    'icon' => 'fas fa-user-check',
                    'title' => $reservation->guest_name . ' booked room ' . $roomNumber,
                    'time' => $reservation->created_at?->diffForHumans() ?? 'Recently',
                ];
            });

        return view('employee.dashboard', compact(
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'maintenanceRooms',
            'occupancyRate',
            'todayArrivals',
            'todayDepartures',
            'pendingRequests',
            'recentActivity'
        ));
    }

    public function adminCalendar()
    {
        return $this->showCalendar('admin', 'admin.calendar');
    }

    public function employeeCalendar()
    {
        return $this->showCalendar('employee', 'employee.calendar');
    }

    protected function showCalendar(string $portal, string $view): \Illuminate\View\View
    {
        $baseDate = Carbon::parse(request('date', now()->format('Y-m-d')));
        $calendarStart = Carbon::parse(request('start_date', $baseDate->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d')));
        $calendarEnd = $calendarStart->copy()->addDays(30);

        $days = [];
        $cursor = $calendarStart->copy();
        while ($cursor->lessThanOrEqualTo($calendarEnd)) {
            $days[] = $cursor->copy();
            $cursor->addDay();
        }

        $rooms = Room::orderBy('room_number')->get();
        $facilities = Facility::orderBy('name')->get();
        $events = Event::orderBy('name')->get();
        $diningTables = DiningTable::orderBy('table_no')->get();

        $roomReservations = RoomReservation::with('room')
            ->whereDate('check_in', '<=', $calendarEnd->toDateString())
            ->whereDate('check_out', '>=', $calendarStart->toDateString())
            ->get();

        // Older bookings remain in reservations after the table split. Include them
        // only when the same booking is not already present in room_reservations.
        $legacyRoomReservations = Reservation::with('room')
            ->whereNotNull('room_id')
            ->whereDate('check_in', '<=', $calendarEnd->toDateString())
            ->whereDate('check_out', '>=', $calendarStart->toDateString())
            ->get();

        $reservations = $roomReservations
            ->concat($legacyRoomReservations)
            ->values();

        $roomTimeline = [];
        $roomTimelineHeights = [];
        foreach ($rooms as $room) {
            $segments = [];
            $roomBookings = $reservations->filter(fn ($reservation) => (int) $reservation->room_id === (int) $room->id);

            foreach ($roomBookings as $reservation) {
                $reservationStart = $reservation->check_in ? Carbon::parse($reservation->check_in) : $calendarStart->copy();
                $reservationEnd = $reservation->check_out ? Carbon::parse($reservation->check_out) : $calendarStart->copy();
                $effectiveStart = $reservationStart->copy()->max($calendarStart);
                $effectiveEnd = $reservationEnd->copy()->min($calendarEnd);

                if ($effectiveEnd->lt($effectiveStart)) {
                    continue;
                }

                $startIndex = $calendarStart->diffInDays($effectiveStart);
                $span = max(1, $effectiveStart->diffInDays($effectiveEnd) + 1);

                $segments[] = [
                    'start' => $startIndex,
                    'span' => $span,
                    'guest' => $reservation->guest_name ?? 'Guest',
                    'status' => strtolower((string) ($reservation->status ?? 'pending')),
                    'details' => [
                        'guest' => $reservation->guest_name ?? 'Guest',
                        'room' => $room->room_number,
                        'check_in' => $reservation->check_in ? Carbon::parse($reservation->check_in)->format('M d, Y') : 'N/A',
                        'check_out' => $reservation->check_out ? Carbon::parse($reservation->check_out)->format('M d, Y') : 'N/A',
                        'amount' => number_format((float) ($reservation->total_amount ?? 0), 2),
                        'status' => ucfirst(strtolower((string) ($reservation->status ?? 'pending'))),
                    ],
                    'style' => match (strtolower((string) ($reservation->status ?? 'pending'))) {
                        'booked' => 'background: rgba(239, 68, 68, 0.28); border: 1px solid rgba(239, 68, 68, 0.4); color: #1f2937;',
                        'confirmed' => 'background: rgba(16, 185, 129, 0.24); border: 1px solid rgba(16, 185, 129, 0.4); color: #0f172a;',
                        'checked-in' => 'background: rgba(234, 88, 12, 0.32); border: 1px solid rgba(234, 88, 12, 0.5); color: #7c2d12;',
                        'pending' => 'background: rgba(248, 232, 5, 0.28); border: 1px solid rgba(248, 232, 5, 0.55); color: #713f12;',
                        'completed' => 'background: rgba(59, 130, 246, 0.28); border: 1px solid rgba(59, 130, 246, 0.4); color: #1f2937;',
                        'cancelled' => 'background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.35); color: #1f2937;',
                        default => 'background: rgba(251, 191, 36, 0.28); border: 1px solid rgba(251, 191, 36, 0.35); color: #1f2937;',
                    },
                ];
            }

            usort($segments, fn ($left, $right) => $left['start'] <=> $right['start']);
            $laneEnds = [];
            foreach ($segments as &$segment) {
                $lane = 0;
                while (isset($laneEnds[$lane]) && $segment['start'] < $laneEnds[$lane]) {
                    $lane++;
                }

                $segment['lane'] = $lane;
                $laneEnds[$lane] = $segment['start'] + $segment['span'];
            }
            unset($segment);

            $roomTimeline[(int) $room->id] = $segments;
            $roomTimelineHeights[(int) $room->id] = max(60, count($laneEnds) * 46 + 14);
        }

        $facilityRows = [];
        $facilityTimeline = [];
        foreach ($facilities as $facility) {
            $facilityRows[] = [
                'id' => $facility->id,
                'name' => $facility->name,
                'type' => $facility->pricing_basis ?: 'Facility',
                'view' => 'facilities',
            ];

            $segments = [];
            $facilityReservations = FacilityReservation::where('facility_id', $facility->id)
                ->where(function ($query) use ($calendarStart, $calendarEnd) {
                    $query->whereBetween('check_in', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
                        ->orWhereBetween('check_out', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
                        ->orWhere(function ($q) use ($calendarStart, $calendarEnd) {
                            $q->where('check_in', '<=', $calendarStart->toDateString())
                                ->where('check_out', '>=', $calendarEnd->toDateString());
                        });
                })
                ->get();

            foreach ($facilityReservations as $reservation) {
                $reservationStart = $reservation->check_in ? Carbon::parse($reservation->check_in) : $calendarStart->copy();
                $reservationEnd = $reservation->check_out ? Carbon::parse($reservation->check_out) : $calendarStart->copy();
                $effectiveStart = $reservationStart->copy()->max($calendarStart);
                $effectiveEnd = $reservationEnd->copy()->min($calendarEnd);

                if ($effectiveEnd->lt($effectiveStart)) {
                    continue;
                }

                $startIndex = $calendarStart->diffInDays($effectiveStart);
                $span = max(1, $effectiveStart->diffInDays($effectiveEnd) + 1);

                $segments[] = [
                    'start' => $startIndex,
                    'span' => $span,
                    'guest' => $reservation->guest_name ?? $facility->name,
                    'status' => strtolower((string) ($reservation->status ?? 'pending')),
                    'details' => [
                        'guest' => $reservation->guest_name ?? 'Guest',
                        'type' => 'Facility',
                        'item' => $facility->name,
                        'check_in' => $reservation->check_in ? Carbon::parse($reservation->check_in)->format('M d, Y') : 'N/A',
                        'check_out' => $reservation->check_out ? Carbon::parse($reservation->check_out)->format('M d, Y') : 'N/A',
                        'time' => $reservation->facility_start_time ?: 'Time not set',
                        'amount' => number_format((float) ($reservation->total_amount ?? 0), 2),
                        'status' => ucfirst(strtolower((string) ($reservation->status ?? 'pending'))),
                    ],
                    'style' => match (strtolower((string) ($reservation->status ?? 'pending'))) {
                        'confirmed' => 'background: rgba(16, 185, 129, 0.24); border: 1px solid rgba(16, 185, 129, 0.4); color: #0f172a;',
                        'checked-in' => 'background: rgba(234, 88, 12, 0.32); border: 1px solid rgba(234, 88, 12, 0.5); color: #7c2d12;',
                        'pending' => 'background: rgba(248, 232, 5, 0.28); border: 1px solid rgba(248, 232, 5, 0.55); color: #713f12;',
                        'completed' => 'background: rgba(59, 130, 246, 0.28); border: 1px solid rgba(59, 130, 246, 0.4); color: #1f2937;',
                        'cancelled' => 'background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.35); color: #1f2937;',
                        default => 'background: rgba(99, 102, 241, 0.24); border: 1px solid rgba(99, 102, 241, 0.35); color: #1f2937;',
                    },
                ];
            }

            $facilityTimeline[(int) $facility->id] = $segments;
        }

        $eventRows = [];
        $eventTimeline = [];
        foreach ($events as $event) {
            $eventRows[] = [
                'id' => $event->id,
                'name' => $event->name,
                'type' => $event->event_type ?: 'Event',
                'view' => 'events',
            ];

            $segments = [];
            $eventReservations = EventReservation::where('event_id', $event->id)
                ->where(function ($query) use ($calendarStart, $calendarEnd) {
                    $query->whereBetween('check_in', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
                        ->orWhereBetween('check_out', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
                        ->orWhere(function ($q) use ($calendarStart, $calendarEnd) {
                            $q->where('check_in', '<=', $calendarStart->toDateString())
                                ->where('check_out', '>=', $calendarEnd->toDateString());
                        });
                })
                ->get();

            foreach ($eventReservations as $reservation) {
                $reservationStart = $reservation->check_in ? Carbon::parse($reservation->check_in) : $calendarStart->copy();
                $reservationEnd = $reservation->check_out ? Carbon::parse($reservation->check_out) : $calendarStart->copy();
                $effectiveStart = $reservationStart->copy()->max($calendarStart);
                $effectiveEnd = $reservationEnd->copy()->min($calendarEnd);

                if ($effectiveEnd->lt($effectiveStart)) {
                    continue;
                }

                $startIndex = $calendarStart->diffInDays($effectiveStart);
                $span = max(1, $effectiveStart->diffInDays($effectiveEnd) + 1);

                $segments[] = [
                    'start' => $startIndex,
                    'span' => $span,
                    'guest' => $reservation->guest_name ?? $event->name,
                    'status' => strtolower((string) ($reservation->status ?? 'pending')),
                    'details' => [
                        'guest' => $reservation->guest_name ?? 'Guest',
                        'type' => 'Event',
                        'item' => $event->name,
                        'event_type' => $reservation->event_type ?: $event->event_type ?: 'Event',
                        'check_in' => $reservation->check_in ? Carbon::parse($reservation->check_in)->format('M d, Y') : 'N/A',
                        'check_out' => $reservation->check_out ? Carbon::parse($reservation->check_out)->format('M d, Y') : 'N/A',
                        'start_time' => $reservation->event_start_time ?: 'Time not set',
                        'end_time' => $reservation->event_end_time ?: 'Time not set',
                        'amount' => number_format((float) ($reservation->total_amount ?? 0), 2),
                        'status' => ucfirst(strtolower((string) ($reservation->status ?? 'pending'))),
                    ],
                    'style' => match (strtolower((string) ($reservation->status ?? 'pending'))) {
                        'confirmed' => 'background: rgba(16, 185, 129, 0.24); border: 1px solid rgba(16, 185, 129, 0.4); color: #0f172a;',
                        'checked-in' => 'background: rgba(234, 88, 12, 0.32); border: 1px solid rgba(234, 88, 12, 0.5); color: #7c2d12;',
                        'pending' => 'background: rgba(248, 232, 5, 0.28); border: 1px solid rgba(248, 232, 5, 0.55); color: #713f12;',
                        'completed' => 'background: rgba(59, 130, 246, 0.28); border: 1px solid rgba(59, 130, 246, 0.4); color: #1f2937;',
                        'cancelled' => 'background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.35); color: #1f2937;',
                        default => 'background: rgba(236, 72, 153, 0.22); border: 1px solid rgba(236, 72, 153, 0.35); color: #1f2937;',
                    },
                ];
            }

            $eventTimeline[(int) $event->id] = $segments;
        }

        $diningRows = [];
        $diningTimeline = [];
        foreach ($diningTables as $table) {
            $diningRows[] = [
                'id' => $table->id,
                'name' => 'Table ' . $table->table_no,
                'type' => $table->type ?: 'Dining',
                'view' => 'dining',
            ];

            $segments = [];
            $tableNumber = (string) $table->table_no;
            $diningReservations = DiningReservation::whereNotIn('status', ['cancelled', 'completed'])
                ->where(function ($query) use ($calendarStart, $calendarEnd) {
                    $query->whereBetween('check_in', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
                        ->orWhereBetween('check_out', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
                        ->orWhere(function ($q) use ($calendarStart, $calendarEnd) {
                            $q->where('check_in', '<=', $calendarStart->toDateString())
                                ->where('check_out', '>=', $calendarEnd->toDateString());
                        });
                })
                ->get();

            foreach ($diningReservations as $reservation) {
                $tableNumbers = array_map('trim', explode(',', (string) ($reservation->dining_area ?? '')));
                if (!in_array($tableNumber, $tableNumbers, true)) {
                    continue;
                }

                $reservationStart = $reservation->check_in ? Carbon::parse($reservation->check_in) : $calendarStart->copy();
                $reservationEnd = $reservation->check_out ? Carbon::parse($reservation->check_out) : $calendarStart->copy();
                $effectiveStart = $reservationStart->copy()->max($calendarStart);
                $effectiveEnd = $reservationEnd->copy()->min($calendarEnd);

                if ($effectiveEnd->lt($effectiveStart)) {
                    continue;
                }

                $startIndex = $calendarStart->diffInDays($effectiveStart);
                $span = max(1, $effectiveStart->diffInDays($effectiveEnd) + 1);

                $segments[] = [
                    'start' => $startIndex,
                    'span' => $span,
                    'guest' => $reservation->guest_name ?? 'Dining',
                    'status' => strtolower((string) ($reservation->status ?? 'pending')),
                    'details' => [
                        'guest' => $reservation->guest_name ?? 'Guest',
                        'type' => 'Dining',
                        'item' => 'Table ' . $table->table_no,
                        'dining_area' => $reservation->dining_area ?: 'Table not set',
                        'check_in' => $reservation->check_in ? Carbon::parse($reservation->check_in)->format('M d, Y') : 'N/A',
                        'check_out' => $reservation->check_out ? Carbon::parse($reservation->check_out)->format('M d, Y') : 'N/A',
                        'time' => $reservation->dining_schedule ?: 'Time not set',
                        'number_of_guests' => $reservation->quantity ?? $reservation->number_of_guests ?? 'N/A',
                        'amount' => number_format((float) ($reservation->total_amount ?? 0), 2),
                        'status' => ucfirst(strtolower((string) ($reservation->status ?? 'pending'))),
                    ],
                    'style' => match (strtolower((string) ($reservation->status ?? 'pending'))) {
                        'confirmed' => 'background: rgba(16, 185, 129, 0.24); border: 1px solid rgba(16, 185, 129, 0.4); color: #0f172a;',
                        'checked-in' => 'background: rgba(234, 88, 12, 0.32); border: 1px solid rgba(234, 88, 12, 0.5); color: #7c2d12;',
                        'pending' => 'background: rgba(248, 232, 5, 0.28); border: 1px solid rgba(248, 232, 5, 0.55); color: #713f12;',
                        'completed' => 'background: rgba(59, 130, 246, 0.28); border: 1px solid rgba(59, 130, 246, 0.4); color: #1f2937;',
                        'cancelled' => 'background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.35); color: #1f2937;',
                        default => 'background: rgba(16, 185, 129, 0.22); border: 1px solid rgba(16, 185, 129, 0.35); color: #1f2937;',
                    },
                ];
            }

            $diningTimeline[(int) $table->id] = $segments;
        }

        $calendarTypeOptions = collect([$rooms, $facilities, $events, $diningTables])
            ->flatten(1)
            ->map(function ($item) {
                $type = null;

                if (isset($item->room_type)) {
                    $type = $item->room_type;
                } elseif (isset($item->pricing_basis)) {
                    $type = $item->pricing_basis ?: 'Facility';
                } elseif (isset($item->event_type)) {
                    $type = $item->event_type ?: 'Event';
                } elseif (isset($item->type)) {
                    $type = $item->type ?: 'Dining';
                }

                return trim((string) $type);
            })
            ->filter(fn ($type) => $type !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();

        return view($view, compact(
            'portal',
            'rooms',
            'facilities',
            'events',
            'diningTables',
            'facilityRows',
            'facilityTimeline',
            'eventRows',
            'eventTimeline',
            'diningRows',
            'diningTimeline',
            'calendarTypeOptions',
            'days',
            'baseDate',
            'calendarStart',
            'calendarEnd',
            'roomTimeline',
            'roomTimelineHeights'
        ));
    }

    public function rooms()
    {
        $rooms = Room::orderBy('room_number')->paginate(5);
        $facilities = Facility::orderBy('name')->paginate(5, ['*'], 'facilities_page')->appends(['tab' => 'facilities']);
        $events = Event::orderBy('name')->paginate(5, ['*'], 'events_page')->appends(['tab' => 'events']);
        $dining = DiningMenu::orderBy('name')->paginate(5, ['*'], 'dining_page')->appends(['tab' => 'dining']);
        $diningTables = DiningTable::orderBy('table_no')->get();
        $diningSchedules = DiningSchedule::orderBy('available_from')->get();
        $activeTab = request()->query('tab', 'rooms');

        if (!in_array($activeTab, ['rooms', 'facilities', 'events', 'dining'], true)) {
            $activeTab = 'rooms';
        }

        $resources = $this->applyResourceReservationStatuses(
            $rooms->getCollection(),
            $facilities->getCollection(),
            $events->getCollection(),
            $diningTables
        );
        $rooms->setCollection($resources['rooms']);
        $facilities->setCollection($resources['facilities']);
        $events->setCollection($resources['events']);
        $diningTables = $resources['diningTables'];

        return view('admin.rooms', compact('rooms', 'facilities', 'events', 'dining', 'diningTables', 'diningSchedules', 'activeTab'));
    }

    public function diningOverview()
    {
        return redirect()->route('admin.rooms', ['tab' => 'dining']);
    }

    public function diningTables()
    {
        $tables = DiningTable::orderBy('table_no')->get();

        return view('admin.dining.tables', compact('tables'));
    }

    public function diningMenu()
    {
        $menus = DiningMenu::orderBy('name')->get()->map(function ($menu) {
            return [
                'name' => $menu->name,
            'category' => $menu->category ?: 'Menu / Meal',
                'price' => '₱' . number_format((float) $menu->price, 2),
                'available_time' => $menu->available_from && $menu->available_to
                    ? Carbon::parse($menu->available_from)->format('g:i A') . ' - ' . Carbon::parse($menu->available_to)->format('g:i A')
                    : 'Any time',
                'status' => ucfirst($menu->status),
            ];
        });

        return view('admin.dining.menu', compact('menus'));
    }

    public function diningSchedule()
    {
        $schedules = DiningSchedule::orderBy('available_from')->get();

        return view('admin.dining.schedule', compact('schedules'));
    }

    protected function handleCatalogImageUpload(Request $request, ?string $currentImage = null): ?string
    {
        if (!$request->hasFile('image')) {
            return $currentImage;
        }

        if ($currentImage && Storage::disk('public')->exists($currentImage)) {
            Storage::disk('public')->delete($currentImage);
        }

        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('catalog', $filename, 'public');

        return $path;
    }

    protected function handleRoomImageUpload(Request $request, ?Room $room = null): ?string
    {
        if (!$request->hasFile('image')) {
            return $room?->image;
        }

        if ($room && $room->image && Storage::disk('public')->exists($room->image)) {
            Storage::disk('public')->delete($room->image);
        }

        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs('rooms', $filename, 'public');
    }

    public function storeInventoryItem(Request $request)
    {
        $eventTimeOptions = collect(range(8, 22))->map(fn ($hour) => sprintf('%02d:00', $hour))->all();
        $validated = $request->validate([
            'category' => ['required', 'in:facilities,event,dining'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'pricing_basis' => ['required_if:category,facilities,event', 'nullable', 'string', 'in:Per Stay,Per Person,Per Vehicle,Per Stay + Per Vehicle,Per Hour,Per Day,Fixed Price,Per Event'],
            'scheduling_requirement' => ['required_if:category,facilities', 'nullable', 'string', 'in:No Additional Schedule,Date Required,Date & Time Required'],
            'event_type' => ['nullable', 'string', 'in:Birthday,Wedding'],
            'status' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'available_from' => ['required_if:category,event', 'nullable', 'date_format:H:i', Rule::in($eventTimeOptions)],
            'available_to' => ['required_if:category,event', 'nullable', 'date_format:H:i', 'after:available_from', Rule::in($eventTimeOptions)],
            'duration_hours' => ['nullable', 'integer', 'min:1', 'max:24'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);
        if ($validated['category'] === 'event' && in_array($validated['pricing_basis'], ['Per Person', 'Per Hour'], true)) {
            $request->validate(['duration_hours' => ['required', 'integer', 'min:1', 'max:24']]);
            $availableHours = Carbon::parse($validated['available_from'])->diffInHours(Carbon::parse($validated['available_to']));
            if ((int) $validated['duration_hours'] > $availableHours) {
                return back()->withErrors(['duration_hours' => 'Duration cannot exceed the selected availability window.'])->withInput();
            }
        }
        $validated['status'] = strtolower($validated['status']);

        $catalogModel = match ($validated['category']) {
            'facilities' => Facility::class,
            'event' => Event::class,
            default => DiningMenu::class,
        };
        if ($catalogModel::where('name', $validated['name'])->exists()) {
            return back()->withErrors(['name' => 'An item with this name already exists.'])->withInput();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleCatalogImageUpload($request);
        }

        match ($validated['category']) {
            'facilities' => Facility::create([
                'name' => $validated['name'], 'description' => $validated['description'] ?? null,
                'price' => $validated['price'], 'status' => $validated['status'], 'image' => $validated['image'] ?? null,
                'pricing_basis' => $validated['pricing_basis'] ?? 'Per Stay', 'capacity' => $validated['capacity'] ?? null,
                'scheduling_requirement' => $validated['scheduling_requirement'] ?? 'No Additional Schedule',
            ]),
            'event' => Event::create([
                'event_type' => $validated['event_type'] ?? 'Birthday', 'name' => $validated['name'], 'description' => $validated['description'] ?? null,
                'price' => $validated['price'], 'capacity' => $validated['capacity'] ?? null,
                'pricing_basis' => $validated['pricing_basis'] ?? 'Per Event', 'location' => $validated['location'] ?? null,
                'available_from' => $validated['available_from'] ?? null, 'available_to' => $validated['available_to'] ?? null,
                'duration_hours' => $validated['duration_hours'] ?? 4,
                'status' => $validated['status'], 'image' => $validated['image'] ?? null,
            ]),
            default => DiningMenu::create([
                'name' => $validated['name'], 'category' => $request->input('menu_category', $validated['type'] ?: 'Menu / Meal'),
                'description' => $validated['description'] ?? null, 'price' => $validated['price'], 'status' => $validated['status'],
                'available_from' => $validated['available_from'] ?? null, 'available_to' => $validated['available_to'] ?? null,
                'quantity' => $validated['quantity'] ?? null, 'image' => $validated['image'] ?? null,
            ]),
        };

        return redirect()->route('admin.rooms')->with('success', 'Item added successfully.');
    }

    public function storeDiningItem(Request $request)
    {
        $validated = $request->validate([
            'dining_type' => ['required', Rule::in(['tables', 'menus', 'schedules'])],
            'name' => ['required', 'string', 'max:255'],
            'menu_category' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_to' => ['nullable', 'date_format:H:i'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'max_guests' => ['nullable', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $image = $request->hasFile('image') ? $this->handleCatalogImageUpload($request) : null;

        if ($validated['dining_type'] === 'tables') {
            DiningTable::create([
                'table_no' => $validated['name'],
                'type' => $validated['type'] ?: 'Standard',
                'capacity' => $validated['capacity'],
                'location' => $validated['location'] ?? null,
                'status' => strtolower($validated['status']),
            ]);
        } elseif ($validated['dining_type'] === 'schedules') {
            DiningSchedule::create([
                'period' => $validated['name'],
                'available_from' => $validated['available_from'],
                'available_to' => $validated['available_to'],
                'max_guests' => $validated['max_guests'] ?? null,
                'status' => strtolower($validated['status']),
            ]);
        } else {
            DiningMenu::create([
                'name' => $validated['name'],
                'category' => $validated['menu_category'] ?? null,
                'price' => $validated['price'] ?? 0,
                'status' => strtolower($validated['status']),
                'available_from' => $validated['available_from'] ?? null,
                'available_to' => $validated['available_to'] ?? null,
                'image' => $image,
            ]);
        }

        return redirect()->route('admin.rooms', ['tab' => 'dining'])->with('success', 'Dining item added successfully.');
    }

    public function updateInventoryItem(Request $request, $id)
    {
        $category = $request->input('category');
        $item = $category === 'facilities' ? Facility::findOrFail($id) : ($category === 'event' ? Event::findOrFail($id) : DiningMenu::findOrFail($id));
        $eventTimeOptions = collect(range(8, 22))->map(fn ($hour) => sprintf('%02d:00', $hour))->all();
        $validated = $request->validate([
            'category' => ['required', 'in:facilities,event,dining'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'pricing_basis' => ['required_if:category,facilities,event', 'nullable', 'string', 'in:Per Stay,Per Person,Per Vehicle,Per Stay + Per Vehicle,Per Hour,Per Day,Fixed Price,Per Event'],
            'scheduling_requirement' => ['nullable', 'string', 'in:No Additional Schedule,Date Required,Date & Time Required'],
            'event_type' => ['nullable', 'string', 'in:Birthday,Wedding'],
            'status' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'available_from' => ['required_if:category,event', 'nullable', 'date_format:H:i', Rule::in($eventTimeOptions)],
            'available_to' => ['required_if:category,event', 'nullable', 'date_format:H:i', 'after:available_from', Rule::in($eventTimeOptions)],
            'duration_hours' => ['nullable', 'integer', 'min:1', 'max:24'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);
        if ($validated['category'] === 'event' && in_array($validated['pricing_basis'], ['Per Person', 'Per Hour'], true)) {
            $request->validate(['duration_hours' => ['required', 'integer', 'min:1', 'max:24']]);
            $availableHours = Carbon::parse($validated['available_from'])->diffInHours(Carbon::parse($validated['available_to']));
            if ((int) $validated['duration_hours'] > $availableHours) {
                return back()->withErrors(['duration_hours' => 'Duration cannot exceed the selected availability window.'])->withInput();
            }
        }
        $validated['status'] = strtolower($validated['status']);

        $model = $category === 'facilities' ? Facility::class : ($category === 'event' ? Event::class : DiningMenu::class);
        if ($model::where('name', $validated['name'])->where('id', '!=', $item->id)->exists()) {
            return back()->withErrors(['name' => 'An item with this name already exists.'])->withInput();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleCatalogImageUpload($request, $item->image);
        }

        $item->update($category === 'facilities'
            ? ['name' => $validated['name'], 'description' => $validated['description'] ?? null, 'price' => $validated['price'], 'pricing_basis' => $validated['pricing_basis'] ?? 'Per Stay', 'capacity' => $validated['capacity'] ?? null, 'scheduling_requirement' => $validated['scheduling_requirement'] ?? $item->scheduling_requirement ?? 'No Additional Schedule', 'status' => $validated['status'], 'image' => $validated['image'] ?? $item->image]
            : ($category === 'event'
                ? ['event_type' => $validated['event_type'] ?? $item->event_type ?? 'Birthday', 'name' => $validated['name'], 'description' => $validated['description'] ?? null, 'price' => $validated['price'], 'pricing_basis' => $validated['pricing_basis'] ?? 'Per Event', 'capacity' => $validated['capacity'] ?? null, 'location' => $validated['location'] ?? null, 'available_from' => $validated['available_from'] ?? null, 'available_to' => $validated['available_to'] ?? null, 'duration_hours' => $validated['duration_hours'] ?? $item->duration_hours ?? 4, 'status' => $validated['status'], 'image' => $validated['image'] ?? $item->image]
                : $validated));

        return redirect()->route('admin.rooms')->with('success', 'Inventory item updated successfully.');
    }

    public function updateInventoryStatus(Request $request, $id)
    {
        $category = $request->input('category', 'dining');
        $item = $category === 'facilities' ? Facility::findOrFail($id) : ($category === 'event' ? Event::findOrFail($id) : DiningMenu::findOrFail($id));
        $validated = $request->validate(['status' => ['required', 'string', 'max:50']]);
        $item->update(['status' => strtolower($validated['status'])]);

        return redirect()->route('admin.rooms')->with('success', 'Inventory status updated successfully.');
    }

    public function updateDiningStatus(Request $request, $type, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
        ]);

        $model = match ($type) {
            'tables' => DiningTable::class,
            'menus' => DiningMenu::class,
            'schedules' => DiningSchedule::class,
            default => abort(404),
        };

        $model::findOrFail($id)->update(['status' => strtolower($validated['status'])]);

        return response()->json(['status' => strtolower($validated['status'])]);
    }

    public function destroyInventoryItem($id)
    {
        $category = request()->input('category', 'dining');
        $item = $category === 'facilities' ? Facility::findOrFail($id) : ($category === 'event' ? Event::findOrFail($id) : DiningMenu::findOrFail($id));
        $item->delete();

        return redirect()->route('admin.rooms')->with('success', 'Item deleted successfully.');
    }

    public function bulkDestroyInventoryItems(Request $request)
    {
        $ids = $request->input('inventory_ids', []);
        $category = $request->input('category');

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $inventoryIds = collect($ids)
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (!count($inventoryIds) || !in_array($category, ['facilities', 'event', 'dining'], true)) {
            return redirect()->route('admin.rooms')->with('success', 'No inventory items selected for deletion.');
        }

        $model = $category === 'facilities' ? Facility::class : ($category === 'event' ? Event::class : DiningMenu::class);
        $deleted = $model::whereIn('id', $inventoryIds)->delete();

        return redirect()->route('admin.rooms')->with('success', $deleted . ' inventory item(s) deleted successfully.');
    }

    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:255|unique:rooms,room_number',
            'room_type' => 'required|string',
            'bed_type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'adult_guest_price' => 'required|numeric|min:0',
            'kid_guest_price' => 'required|numeric|min:0',
            'floor' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);
        $validated['status'] = 'available';

        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleRoomImageUpload($request);
        }

        Room::create($validated);
        return redirect()->route('admin.rooms')->with('success', 'Room created successfully!');
    }

    public function updateRoom(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $id,
            'room_type' => 'required|string',
            'bed_type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'adult_guest_price' => 'required|numeric|min:0',
            'kid_guest_price' => 'required|numeric|min:0',
            'floor' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'status' => 'required|in:OC,OD,VR,VC,VD,HSD,HSUC,OOO,BLO,NS,SO,HU,DND,available,occupied,reserved,maintenance,blocked,out_of_order',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleRoomImageUpload($request, $room);
        }

        $room->update($validated);
        return redirect()->route('admin.rooms')->with('success', 'Room updated successfully!');
    }

    public function updateRoomStatus(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $room->update(['status' => $request->status]);
        return redirect()->route('admin.rooms')->with('success', 'Room status updated successfully!');
    }

    public function destroyRoom($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return redirect()->route('admin.rooms')->with('success', 'Room deleted successfully!');
    }

    public function bulkDestroyRooms(Request $request)
    {
        $ids = $request->input('room_ids', []);

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $roomIds = collect($ids)
            ->filter(fn($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (!count($roomIds)) {
            return redirect()->route('admin.rooms')->with('success', 'No rooms selected for deletion.');
        }

        $deleted = Room::whereIn('id', $roomIds)->delete();

        return redirect()->route('admin.rooms')->with('success', $deleted . ' room(s) deleted successfully!');
    }

    public function bulkDestroyDining(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['tables', 'menus', 'schedules'])],
            'dining_ids' => ['required'],
        ]);

        $ids = is_string($validated['dining_ids']) ? explode(',', $validated['dining_ids']) : $validated['dining_ids'];
        $ids = collect($ids)
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()->route('admin.rooms')->with('success', 'No dining items selected for deletion.');
        }

        $model = match ($validated['type']) {
            'tables' => DiningTable::query(),
            'menus' => DiningMenu::query(),
            'schedules' => DiningSchedule::query(),
        };
        $deleted = $model->whereIn('id', $ids)->delete();

        return redirect()->route('admin.rooms')->with('success', $deleted . ' dining item(s) deleted successfully.');
    }

    public function reservations()
    {
        $this->completeFinishedReservations();

        $roomReservations = RoomReservation::with(['room', 'refunds'])->latest()->get();
        $facilitiesReservations = FacilityReservation::with(['facility', 'refunds'])->latest()->get();
        $eventsReservations = EventReservation::with(['event', 'diningItems.diningMenu', 'refunds'])->latest()->get();
        $diningReservations = DiningReservation::with(['diningItems.diningMenu', 'refunds'])->latest()->get();

        $legacyReservations = Reservation::with(['room', 'facility', 'event', 'diningItems', 'refunds'])->latest()->get();
        $legacyReservations->each(function ($reservation) use (&$roomReservations, &$facilitiesReservations, &$eventsReservations, &$diningReservations) {
            $category = $reservation->category;
            if ($category === 'rooms' || $reservation->room_id) {
                $roomReservations->push($reservation);
            }
            if ($category === 'facilities' || $reservation->facility_id) {
                $facilitiesReservations->push($reservation);
            }
            if ($category === 'event' || $reservation->event_id) {
                $eventsReservations->push($reservation);
            }
            if ($category === 'dining' || $reservation->dining_id || $reservation->dining_area || $reservation->dining_schedule) {
                $diningReservations->push($reservation);
            }
        });

        $allReservationRows = collect([
            ...$roomReservations,
            ...$facilitiesReservations,
            ...$eventsReservations,
            ...$diningReservations,
            ...$legacyReservations,
        ])->unique(fn ($reservation) => get_class($reservation) . ':' . $reservation->id)->values();

        $allReservationRows->each(function ($reservation) use ($allReservationRows) {
            $relatedRefunds = $allReservationRows
                ->filter(fn ($related) => $related->guest_email === $reservation->guest_email
                    && optional($related->check_in)->toDateString() === optional($reservation->check_in)->toDateString())
                ->flatMap(function ($related) {
                    $category = match (true) {
                        $related instanceof RoomReservation => 'Room',
                        $related instanceof FacilityReservation => 'Facilities',
                        $related instanceof EventReservation => 'Event',
                        $related instanceof DiningReservation => 'Dining',
                        default => 'Reservation',
                    };

                    return $related->refunds->map(fn ($refund) => [
                        'id' => $refund->id,
                        'category' => $category,
                        'amount' => (float) $refund->refund_amount,
                        'reason' => $refund->reason,
                        'status' => $refund->status,
                        'date' => $refund->refund_date?->format('F j, Y') ?? 'N/A',
                    ]);
                })
                ->unique('id')
                ->sortByDesc('id')
                ->values()
                ->all();

            $reservation->setAttribute('related_refunds', $relatedRefunds);
        });

        $roomReservations = $this->paginateReservations($roomReservations->sortByDesc('created_at')->values(), 'rooms_page');
        $facilitiesReservations = $this->paginateReservations($facilitiesReservations->sortByDesc('created_at')->values(), 'facilities_page');
        $eventsReservations = $this->paginateReservations($eventsReservations->sortByDesc('created_at')->values(), 'events_page');
        $diningReservations = $this->paginateReservations($diningReservations->sortByDesc('created_at')->values(), 'dining_page');
        
        $rooms = Room::orderBy('room_number')->get();
        $inventoryItems = InventoryItem::orderBy('name')->get();
        $facilities = Facility::orderBy('name')->get();
        $events = Event::orderBy('name')->get();
        $diningMenus = DiningMenu::orderBy('name')->get();
        $diningSchedules = DiningSchedule::orderBy('available_from')->get();
        $diningTables = DiningTable::orderBy('table_no')->get();

        // For backward compatibility, keep the old variable as a collection.
        $reservations = $roomReservations->getCollection();

        return request()->routeIs('employee.reservation')
            ? view('employee.reservation', compact('reservations', 'roomReservations', 'facilitiesReservations', 'eventsReservations', 'diningReservations', 'rooms', 'inventoryItems', 'facilities', 'events', 'diningTables', 'diningMenus', 'diningSchedules'))
            : view('admin.reservations', compact('reservations', 'roomReservations', 'facilitiesReservations', 'eventsReservations', 'diningReservations', 'rooms', 'inventoryItems', 'facilities', 'events', 'diningMenus', 'diningSchedules'));
    }

    private function paginateReservations($reservations, string $pageName): LengthAwarePaginator
    {
        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);

        return new LengthAwarePaginator(
            $reservations->forPage($currentPage, $perPage)->values(),
            $reservations->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => $pageName,
            ]
        );
    }

    public function bulkDestroyReservations(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', Rule::in(['rooms', 'facilities', 'event', 'dining'])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['string', 'distinct', 'regex:/^(?:[1-9][0-9]*|(?:reservations|room_reservations|facility_reservations|event_reservations|dining_reservations):[1-9][0-9]*)$/'],
        ]);

        $deleted = 0;
        foreach ($validated['ids'] as $reference) {
            if (str_contains($reference, ':')) {
                [$source, $id] = explode(':', $reference, 2);
            } else {
                $source = null;
                $id = $reference;
            }

            $reservation = match ($source) {
                'room_reservations' => RoomReservation::find($id),
                'event_reservations' => EventReservation::find($id),
                'facility_reservations' => FacilityReservation::find($id),
                'dining_reservations' => DiningReservation::find($id),
                'reservations' => Reservation::find($id),
                default => match ($validated['category']) {
                    'rooms' => RoomReservation::find($id) ?? Reservation::find($id),
                    'event' => EventReservation::find($id) ?? Reservation::find($id),
                    'facilities' => FacilityReservation::find($id) ?? Reservation::find($id),
                    'dining' => DiningReservation::find($id) ?? Reservation::find($id),
                },
            };

            // Older reservation pages may submit only the numeric ID. For room
            // records, prefer the legacy reservation when that is where the row
            // actually came from, without touching Darlene's separate booking.
            if (!$reservation && $validated['category'] === 'rooms') {
                $reservation = Reservation::whereKey($id)
                    ->whereNotNull('room_id')
                    ->first();
            }

            if ($reservation instanceof Reservation) {
                $legacyCategory = match (true) {
                    $reservation->category === 'event' || $reservation->event_id => 'event',
                    $reservation->category === 'facilities' || $reservation->facility_id => 'facilities',
                    $reservation->category === 'dining' || $reservation->dining_id || $reservation->dining_area || $reservation->dining_schedule => 'dining',
                    default => 'rooms',
                };

                if ($legacyCategory !== $validated['category']) {
                    $reservation = null;
                }
            }

            if ($reservation) {
                $reservation->delete();
                $deleted++;
            }
        }

        $route = request()->routeIs('employee.reservations.bulk-destroy')
            ? 'employee.reservation'
            : 'admin.reservations';

        return redirect()->route($route)->with('success', $deleted . ' reservation(s) deleted successfully.');
    }

    private function completeFinishedReservations(): void
    {
        $now = Carbon::now();

        RoomReservation::whereIn('status', ['confirmed', 'checked-in'])
            ->get()
            ->each(function ($reservation) use ($now) {
                $end = Carbon::parse(Carbon::parse($reservation->check_out)->format('Y-m-d') . ' ' . ($reservation->room_check_out_time ?: '23:59:59'));
                if ($end->lte($now)) {
                    $reservation->update(['status' => 'completed']);
                    $reservation->room?->update(['status' => 'available', 'cleaning_status' => 'dirty']);
                }
            });

        EventReservation::whereIn('status', ['confirmed', 'checked-in'])
            ->get()
            ->each(function ($reservation) use ($now) {
                $end = Carbon::parse(Carbon::parse($reservation->check_out)->format('Y-m-d') . ' ' . ($reservation->event_end_time ?: '23:59:59'));
                if ($end->lte($now)) {
                    $reservation->update(['status' => 'completed']);
                    $reservation->event?->update(['status' => 'available']);
                }
            });

        FacilityReservation::whereIn('status', ['confirmed', 'checked-in'])
            ->get()
            ->each(function ($reservation) use ($now) {
                $end = Carbon::parse(Carbon::parse($reservation->check_out)->format('Y-m-d') . ' ' . ($reservation->facility_end_time ?: '23:59:59'));
                if ($end->lte($now)) {
                    $reservation->update(['status' => 'completed']);
                }
            });

        DiningReservation::whereIn('status', ['confirmed', 'checked-in'])
            ->get()
            ->each(function ($reservation) use ($now) {
                $scheduleEnd = DiningSchedule::whereIn('period', collect(explode(',', (string) $reservation->dining_schedule))->map(fn ($period) => trim($period))->filter()->all())
                    ->max('available_to');
                $end = Carbon::parse(Carbon::parse($reservation->check_in)->format('Y-m-d') . ' ' . ($scheduleEnd ?: '23:59:59'));
                if ($end->lte($now)) {
                    $reservation->update(['status' => 'completed']);
                    $tableNumbers = collect(explode(',', (string) $reservation->dining_area))->map(fn ($table) => trim($table))->filter();
                    DiningTable::whereIn('table_no', $tableNumbers)->update(['status' => 'available']);
                }
            });
    }

    protected function normalizeDiningSelections(Request $request): array
    {
        $rawDiningItems = $request->input('dining_items');
        if (is_string($rawDiningItems)) {
            $decoded = json_decode($rawDiningItems, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rawDiningItems = $decoded;
            }
        }

        if (is_array($rawDiningItems) && !empty($rawDiningItems)) {
            return collect($rawDiningItems)
                ->filter(fn ($item) => is_array($item) && !empty($item['dining_id']))
                ->map(function ($item) {
                    return [
                        'dining_id' => (int) $item['dining_id'],
                        'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                        'dining_area' => !empty($item['dining_area']) ? (string) $item['dining_area'] : null,
                        'dining_schedule' => !empty($item['dining_schedule']) ? (string) $item['dining_schedule'] : null,
                        'dining_date' => !empty($item['dining_date']) ? (string) $item['dining_date'] : null,
                    ];
                })
                ->values()
                ->all();
        }

        $diningIds = collect(explode(',', (string) $request->input('dining_id', '')))
            ->map(fn ($id) => trim((string) $id))
            ->filter(fn ($id) => $id !== '' && $id !== 'null' && $id !== 'upon_arriving')
            ->values()
            ->all();

        if (empty($diningIds)) {
            return [];
        }

        $areas = collect(explode(',', (string) $request->input('dining_area', '')))
            ->map(fn ($value) => trim((string) $value))
            ->values()
            ->all();

        $schedules = collect(explode(',', (string) $request->input('dining_schedule', '')))
            ->map(fn ($value) => trim((string) $value))
            ->values()
            ->all();

        $quantities = collect(explode(',', (string) $request->input('quantity', '')))
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '')
            ->values()
            ->all();

        $items = [];
        foreach ($diningIds as $index => $diningId) {
            $items[] = [
                'dining_id' => (int) $diningId,
                'quantity' => max(1, (int) ($quantities[$index] ?? $quantities[0] ?? 1)),
                'dining_area' => $areas[$index] ?? $areas[0] ?? null,
                'dining_schedule' => $schedules[$index] ?? $schedules[0] ?? null,
                'dining_date' => null,
            ];
        }

        return $items;
    }

    public function storeReservation(Request $request)
    {
        $diningSelections = $this->normalizeDiningSelections($request);

        $diningIds = $request->input('dining_id');
        if (is_string($diningIds)) {
            $diningIds = collect(explode(',', $diningIds))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '' && $id !== 'upon_arriving' && $id !== 'null')
                ->values()
                ->all();

            if ($diningIds === []) {
                $request->merge(['dining_id' => null]);
            } else {
                $request->merge(['dining_id' => implode(',', $diningIds)]);
            }
        }

        if ($request->input('dining_id') === 'upon_arriving') {
            $request->merge(['dining_id' => null]);
        }

        if (!empty($diningSelections)) {
            $request->merge(['dining_id' => null]);
        }

        $validated = $request->validate([
            'category' => ['required', 'in:rooms,facilities,event,dining'],
            'room_id' => ['nullable', 'required_if:category,rooms', 'exists:rooms,id'],
            'facility_id' => ['nullable', 'required_if:category,facilities', 'exists:facilities,id'],
            'event_id' => ['nullable', 'required_if:category,event', 'exists:events,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'event_type' => ['nullable', 'required_if:category,event', 'string', 'max:100'],
            'number_of_guests' => ['nullable', 'required_if:category,rooms|required_if:category,event', 'integer', 'min:1'],
            'adult_guests' => ['nullable', 'integer', 'min:0'],
            'kid_guests' => ['nullable', 'integer', 'min:0'],
            'dining_area' => ['nullable', 'required_if:category,dining', 'string', 'max:100'],
            'dining_schedule' => ['nullable', 'required_if:category,dining', 'in:Breakfast,Lunch,Afternoon Snacks,Dinner'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'facility_quantity' => ['nullable', 'integer', 'min:1'],
            'check_in' => ['required', 'date'],
            'check_in_time' => ['nullable', 'required_if:category,facilities', 'date_format:H:i'],
            'event_start_time' => ['exclude_unless:category,event', 'required', 'date_format:H:i'],
            'check_out' => ['required', 'date', 'after_or_equal:check_in'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'event_end_time' => ['exclude_unless:category,event', 'required', 'date_format:H:i'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:Cash / Pay at Hotel,GCash,Maya,Credit / Debit Card,Bank Transfer'],
            'payment_details' => ['nullable', 'string', 'max:2000'],
            'amount_paid' => ['nullable', 'numeric', 'min:0', 'lte:total_amount'],
            'dining_id' => ['nullable', 'string'],
            'duration_hours' => ['nullable', 'required_if:category,facilities', 'integer', 'min:1', 'max:24'],
            'special_requests' => ['nullable', 'string'],
            'submission_token' => ['nullable', 'string', 'max:100'],
        ]);

        if (!empty($validated['dining_id'])) {
            $diningIdList = collect(explode(',', $validated['dining_id']))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '')
                ->all();

            $invalidDiningIds = collect($diningIdList)->filter(fn ($id) => !DiningMenu::whereKey($id)->exists())->values()->all();
            abort_if(!empty($invalidDiningIds), 422, 'One or more selected dining items are invalid.');
            $validated['dining_id'] = implode(',', $diningIdList);
        }

        $submissionToken = $validated['submission_token'] ?? null;
        $isEmployeeReservation = $request->routeIs('employee.reservations.store') || $request->user()?->role === 'employee';

        if ($submissionToken && $request->session()->has('reservation_submission_' . $submissionToken)) {
            return $isEmployeeReservation
                ? redirect()->route('employee.reservation')
                : redirect()->route('admin.reservations');
        }

        if ($submissionToken) {
            $request->session()->put('reservation_submission_' . $submissionToken, true);
        }

        unset($validated['submission_token']);
        $validated['status'] = 'pending';

        // Process based on category
        $category = $validated['category'];
        $room = !empty($validated['room_id']) ? Room::findOrFail($validated['room_id']) : null;
        $facility = !empty($validated['facility_id']) ? Facility::findOrFail($validated['facility_id']) : null;
        $event = !empty($validated['event_id']) ? Event::findOrFail($validated['event_id']) : null;
        $roomTotal = $room
            ? ReservationPricing::room(
                $room,
                $validated['check_in'],
                $validated['check_out'],
                (int) ($validated['number_of_guests'] ?? 1),
                isset($validated['adult_guests']) ? (int) $validated['adult_guests'] : null,
                isset($validated['kid_guests']) ? (int) $validated['kid_guests'] : null
            )
            : 0;
        $facilityTotal = $facility
            ? ReservationPricing::facilities(
                collect([$facility]),
                (int) ($validated['facility_quantity'] ?? $validated['quantity'] ?? 1),
                $validated['check_in'],
                $validated['check_out']
            )
            : 0;
        $eventDurationHours = 1;
        if (!empty($validated['event_start_time']) && !empty($validated['event_end_time'])) {
            $eventDurationHours = max(1, Carbon::parse($validated['event_start_time'])->diffInHours(Carbon::parse($validated['event_end_time'])));
        }
        $eventTotal = $event
            ? ReservationPricing::events(collect([$event]), (int) ($validated['number_of_guests'] ?? 1), $eventDurationHours)
            : 0;
        $diningTotal = ReservationPricing::dining($diningSelections);
        $validated['total_amount'] = match ($category) {
            'rooms' => $roomTotal,
            'facilities' => $facilityTotal,
            'event' => $eventTotal,
            'dining' => $diningTotal,
        };

        $amountPaid = (float) ($validated['amount_paid'] ?? 0);
        if ($amountPaid > (float) $validated['total_amount']) {
            throw ValidationException::withMessages([
                'amount_paid' => 'The amount paid cannot exceed the calculated reservation total.',
            ]);
        }
        $validated['amount_paid'] = round($amountPaid, 2);

        if ($category === 'rooms') {
            $validated['room_check_in_time'] = $validated['check_in_time'] ?? null;
            $validated['room_check_out_time'] = $validated['check_out_time'] ?? null;
            $reservation = RoomReservation::create($validated);
        } elseif ($category === 'event') {
            $validated['event_start_time'] = $validated['event_start_time'] ?? $validated['check_in_time'] ?? null;
            $validated['event_end_time'] = $validated['event_end_time'] ?? $validated['check_out_time'] ?? null;
            $reservation = EventReservation::create($validated);
        } elseif ($category === 'facilities') {
            $endTime = Carbon::createFromFormat('Y-m-d H:i', $validated['check_in'] . ' ' . $validated['check_in_time'])
                ->addHours((int) $validated['duration_hours']);
            $validated['check_out'] = $endTime->toDateString();
            $validated['facility_end_time'] = $endTime->format('H:i');
            $validated['facility_start_time'] = $validated['check_in_time'] ?? null;
            $validated['facility_quantity'] = $validated['facility_quantity'] ?? $validated['quantity'] ?? 1;
            unset($validated['duration_hours']);
            $reservation = FacilityReservation::create($validated);
        } elseif ($category === 'dining') {
            $hasDiningConflict = DiningReservation::query()
                ->activeForTableAndSchedule(
                    (string) $validated['dining_area'],
                    $validated['check_in'],
                    $validated['dining_schedule']
                )
                ->exists();

            if ($hasDiningConflict) {
                throw ValidationException::withMessages([
                    'dining_area' => 'This table is already reserved for the selected dining schedule. Please choose another table or schedule.',
                ]);
            }

            $reservation = DiningReservation::create($validated);
            if (!empty($diningSelections) && method_exists($reservation, 'diningItems')) {
                $reservation->diningItems()->createMany($diningSelections);
            }
        }

        return $isEmployeeReservation
            ? redirect()->route('employee.reservation')->with('success', 'Reservation created successfully!')
            : redirect()->route('admin.reservations')->with('success', 'Reservation created successfully!');
    }

    public function updateReservationStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,checked-in,cancelled,completed'],
            'category' => ['nullable', 'in:rooms,facilities,event,dining'],
        ]);

        $refundMessage = null;
        $confirmedReservation = null;

        DB::transaction(function () use ($id, $validated, &$refundMessage, &$confirmedReservation) {
            $reservationType = match ($validated['category'] ?? null) {
                'rooms' => 'room',
                'event' => 'event',
                'facilities' => 'facility',
                'dining' => 'dining',
                default => null,
            };
            $reservation = match ($validated['category'] ?? null) {
                'rooms' => RoomReservation::with('room')->find($id),
                'event' => EventReservation::find($id),
                'facilities' => FacilityReservation::find($id),
                'dining' => DiningReservation::find($id),
                default => RoomReservation::with('room')->find($id)
                    ?? EventReservation::find($id)
                    ?? FacilityReservation::find($id)
                    ?? DiningReservation::find($id)
                    ?? Reservation::with('room')->find($id),
            };

            if (!$reservationType) {
                $reservationType = $reservation instanceof EventReservation ? 'event'
                    : ($reservation instanceof FacilityReservation ? 'facility'
                    : ($reservation instanceof DiningReservation ? 'dining' : 'room'));
            }

            abort_if(!$reservation, 404, 'Reservation not found');

            $wasPendingConfirmation = $reservation->status !== 'confirmed'
                && $validated['status'] === 'confirmed';

            if ($validated['status'] === 'completed') {
                $relatedRows = $reservation instanceof RoomReservation
                    ? collect([
                        ...RoomReservation::with('payments')->get(),
                        ...FacilityReservation::with('payments')->get(),
                        ...EventReservation::with('payments')->get(),
                        ...DiningReservation::with('payments')->get(),
                    ])->filter(function ($row) use ($reservation) {
                        return $row->guest_email === $reservation->guest_email
                            && optional($row->check_in)->toDateString() === optional($reservation->check_in)->toDateString();
                    })
                    : collect([$reservation]);
                $total = (float) $relatedRows->sum(fn ($row) => (float) ($row->total_amount ?? 0));
                if ($reservation instanceof RoomReservation) {
                    $total += (float) GuestRequest::with('reservation')
                        ->where('is_billable', true)
                        ->whereIn('status', ['Delivered', 'Completed'])
                        ->get()
                        ->filter(function (GuestRequest $guestRequest) use ($reservation) {
                            return ($guestRequest->reservation_type === RoomReservation::class
                                    && (int) $guestRequest->reservation_key === (int) $reservation->id)
                                || ($guestRequest->reservation
                                    && $guestRequest->reservation->guest_email === $reservation->guest_email
                                    && optional($guestRequest->reservation->check_in)->toDateString() === optional($reservation->check_in)->toDateString());
                        })
                        ->sum(fn (GuestRequest $guestRequest) => (float) $guestRequest->unit_price * max((int) ($guestRequest->quantity ?? 1), 1));
                }
                $paid = max(
                    (float) $relatedRows->max(fn ($row) => (float) ($row->amount_paid ?? 0)),
                    (float) $relatedRows->sum(fn ($row) => (float) $row->payments->sum('amount'))
                );
                if (round($total - $paid, 2) > 0) {
                    throw ValidationException::withMessages([
                        'status' => 'The reservation must be paid in full before checkout.',
                    ]);
                }
            }

            if ($validated['status'] === 'cancelled' && $reservation->status !== 'cancelled') {
                $reservation->loadMissing('payments');
                $originalTotal = round((float) ($reservation->total_amount ?? 0), 2);
                $totalPaid = $this->overallReservationTotals($reservation)['paid'];
                $refundReason = $reservation->status === 'checked-in' ? 'Early Check-out' : 'Cancellation';
                $refundAmount = $this->calculateRefundAmount($originalTotal, 0, $totalPaid);
                $this->createRefundIfDue(
                    $reservation,
                    $originalTotal,
                    0,
                    $totalPaid,
                    $refundReason
                );
                $refundMessage = $refundAmount > 0
                    ? ' Refund Due: ₱' . number_format($refundAmount, 2) . ' (Pending).'
                    : '';
            }

            $reservation->update(['status' => $validated['status']]);

            if ($wasPendingConfirmation && $reservation->guest_email) {
                $confirmedReservation = $reservation->fresh();
            }

            if ($reservation instanceof RoomReservation && in_array($validated['status'], ['checked-in', 'completed'], true)) {
                $legacyReservation = Reservation::query()
                    ->where('guest_email', $reservation->guest_email)
                    ->where('room_id', $reservation->room_id)
                    ->whereDate('check_in', $reservation->check_in)
                    ->whereDate('check_out', $reservation->check_out)
                    ->where('total_amount', $reservation->total_amount)
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->latest('id')
                    ->first();

                $legacyReservation?->update(['status' => $validated['status']]);
            }

            if ($reservation instanceof RoomReservation) {
                if ($validated['status'] === 'confirmed') {

                    FacilityReservation::query()
                        ->where('guest_email', $reservation->guest_email)
                        ->whereDate('check_in', $reservation->check_in)
                        ->whereNotIn('status', ['cancelled', 'completed'])
                        ->update(['status' => 'confirmed']);

                    EventReservation::query()
                        ->where('guest_email', $reservation->guest_email)
                        ->whereDate('check_in', $reservation->check_in)
                        ->whereNotIn('status', ['cancelled', 'completed'])
                        ->with('event')
                        ->get()
                        ->each(function (EventReservation $linkedReservation) {
                            $linkedReservation->update(['status' => 'confirmed']);

                            if (!$linkedReservation->event) {
                                return;
                            }

                            $linkedReservation->event->update(['status' => 'reserved']);
                        });

                    DiningReservation::query()
                        ->where('guest_email', $reservation->guest_email)
                        ->whereDate('check_in', $reservation->check_in)
                        ->whereNotIn('status', ['cancelled', 'completed'])
                        ->get()
                        ->each(function (DiningReservation $linkedReservation) {
                            $linkedReservation->update(['status' => 'confirmed']);

                            $tableNumbers = collect(explode(',', (string) $linkedReservation->dining_area))
                                ->map(fn ($tableNumber) => trim($tableNumber))
                                ->filter()
                                ->values();

                            if ($tableNumbers->isNotEmpty()) {
                                DiningTable::whereIn('table_no', $tableNumbers)->update(['status' => 'reserved']);
                            }
                        });
                }
            }

            // Only update room status for room reservations
            if ($reservationType === 'room' && $reservation->room) {
                if ($validated['status'] === 'confirmed') {
                    $reservation->room->update([
                        'status' => 'reserved',
                    ]);
                } elseif ($validated['status'] === 'checked-in') {
                    $reservation->room->update([
                        'status' => 'occupied',
                        'cleaning_status' => 'clean',
                    ]);
                } elseif (in_array($validated['status'], ['cancelled', 'completed'], true)) {
                    $reservation->room->update([
                        'status' => 'available',
                        'cleaning_status' => 'dirty',
                    ]);
                }
            }

            if ($reservation instanceof DiningReservation && $validated['status'] === 'confirmed') {
                $tableNumbers = collect(explode(',', (string) $reservation->dining_area))
                    ->map(fn ($tableNumber) => trim($tableNumber))
                    ->filter()
                    ->values();

                if ($tableNumbers->isNotEmpty()) {
                    DiningTable::whereIn('table_no', $tableNumbers)->update(['status' => 'reserved']);
                }
            }

            if ($reservation instanceof EventReservation && $reservation->event) {
                if ($validated['status'] === 'confirmed') {
                    $reservation->event->update(['status' => 'reserved']);
                } elseif (in_array($validated['status'], ['cancelled', 'completed'], true)) {
                    $reservation->event->update(['status' => 'available']);
                }
            }
        });

        if ($confirmedReservation) {
            Mail::to($confirmedReservation->guest_email)
                ->send(new ReservationConfirmed($confirmedReservation));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Reservation status updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Reservation status updated successfully!' . ($refundMessage ?? ''));
    }

    public function storePayment(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', Rule::in(['Cash', 'GCash', 'Bank Transfer', 'Credit/Debit Card'])],
            'payment_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:255', 'required_unless:payment_method,Cash'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $result = DB::transaction(function () use ($request, $id, $validated) {
            // Find reservation in all tables
            $reservation = RoomReservation::find($id) ?? EventReservation::find($id) ?? 
                          FacilityReservation::find($id) ?? DiningReservation::find($id);
            
            abort_if(!$reservation, 404, 'Reservation not found');
            
            $isRoomBooking = $reservation instanceof RoomReservation;
            $relatedRows = $isRoomBooking
                ? collect([
                    ...RoomReservation::with('payments')->get(),
                    ...FacilityReservation::with('payments')->get(),
                    ...EventReservation::with('payments')->get(),
                    ...DiningReservation::with('payments')->get(),
                ])->filter(function ($row) use ($reservation) {
                    return $row->guest_email === $reservation->guest_email
                        && optional($row->check_in)->toDateString() === optional($reservation->check_in)->toDateString();
                })
                : collect([$reservation]);
            $chargeableAddOns = $isRoomBooking
                ? GuestRequest::with('reservation')
                    ->where('is_billable', true)
                    ->whereIn('status', ['Delivered', 'Completed'])
                        ->where('billing_status', 'pending')
                    ->lockForUpdate()
                    ->get()
                    ->filter(function (GuestRequest $guestRequest) use ($reservation) {
                        $matchesReservationKey = $guestRequest->reservation_type === RoomReservation::class
                            && (int) $guestRequest->reservation_key === (int) $reservation->id;
                        $matchesLegacyReservation = $guestRequest->reservation
                            && $guestRequest->reservation->guest_email === $reservation->guest_email
                            && optional($guestRequest->reservation->check_in)->toDateString() === optional($reservation->check_in)->toDateString();

                        return $matchesReservationKey || $matchesLegacyReservation;
                    })
                : collect();
            $total = $isRoomBooking
                ? round(
                    (float) $relatedRows->sum(fn ($row) => (float) ($row->total_amount ?? 0))
                    + (float) $chargeableAddOns->sum(function (GuestRequest $guestRequest) {
                            return round(
                                (float) ($guestRequest->unit_price ?? 0) * max((int) ($guestRequest->quantity ?? 1), 1),
                                2
                            );
                        }),
                    2
                )
                : (float) $reservation->total_amount;
            $paid = max(
                (float) $relatedRows->max(fn ($row) => (float) ($row->amount_paid ?? 0)),
                (float) $relatedRows->sum(fn ($row) => (float) $row->payments->sum('amount'))
            );
            $paid = min($paid, $total);
            $balance = round($total - $paid, 2);
            if ((float) $validated['amount'] > $balance) {
                abort(422, 'Payment amount cannot exceed the balance due.');
            }

            $payment = new Payment([
                ...$validated,
                'recorded_by' => $request->user()->id,
            ]);
            $reservation->payments()->save($payment);
            
            $newPaid = round($paid + (float) $payment->amount, 2);
            $newBalance = max(round($total - $newPaid, 2), 0);
            $reservation->update([
                'amount_paid' => $newPaid,
            ]);

            if ($newBalance === 0.0 && $chargeableAddOns->isNotEmpty()) {
                $chargeableAddOns->each(function (GuestRequest $guestRequest) {
                    $guestRequest->update([
                        'billing_status' => 'posted',
                        'billing_posted_at' => now(),
                    ]);
                });
            }

            return [
                'total' => $total,
                'paid' => $newPaid,
                'balance' => $newBalance,
                'status' => $newBalance === 0.0 ? 'Paid' : ($newPaid > 0 ? 'Partially Paid' : 'Unpaid'),
            ];
        });

        return response()->json($result);
    }

    public function updateReservation(Request $request, $id)
    {
        $diningSelections = $this->normalizeDiningSelections($request);

        $diningIds = $request->input('dining_id');
        if (is_string($diningIds)) {
            $diningIds = collect(explode(',', $diningIds))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '' && $id !== 'upon_arriving' && $id !== 'null')
                ->values()
                ->all();

            $request->merge(['dining_id' => $diningIds ? implode(',', $diningIds) : null]);
        }

        if ($request->input('dining_id') === 'upon_arriving') {
            $request->merge(['dining_id' => null]);
        }

        if (!empty($diningSelections)) {
            $request->merge(['dining_id' => null]);
        }

        $validated = $request->validate([
            'category' => ['required', 'in:rooms,facilities,event,dining'],
            'status' => ['required', Rule::in(['pending', 'confirmed', 'checked-in', 'completed', 'cancelled'])],
            'room_id' => ['nullable', 'required_if:category,rooms', 'exists:rooms,id'],
            'facility_id' => ['nullable', 'required_if:category,facilities', 'exists:facilities,id'],
            'event_id' => ['nullable', 'required_if:category,event', 'exists:events,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'event_type' => ['nullable', 'required_if:category,event', 'string', 'max:100'],
            'number_of_guests' => ['nullable', 'required_if:category,event', 'integer', 'min:1'],
            'adult_guests' => ['nullable', 'integer', 'min:0'],
            'kid_guests' => ['nullable', 'integer', 'min:0'],
            'dining_area' => ['nullable', 'required_if:category,dining', 'string', 'max:100'],
            'dining_schedule' => ['nullable', 'required_if:category,dining', 'in:Breakfast,Lunch,Afternoon Snacks,Dinner'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'facility_quantity' => ['nullable', 'integer', 'min:1'],
            'check_in' => ['required', 'date'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'event_start_time' => ['exclude_unless:category,event', 'required', 'date_format:H:i'],
            'check_out' => ['required', 'date', 'after_or_equal:check_in'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'event_end_time' => ['exclude_unless:category,event', 'required', 'date_format:H:i'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:Cash / Pay at Hotel,GCash,Maya,Credit / Debit Card,Bank Transfer'],
            'payment_details' => ['nullable', 'string', 'max:2000'],
            'special_requests' => ['nullable', 'string'],
            'dining_id' => ['nullable', 'string'],
            'dining_items' => ['nullable', 'json'],
        ]);

        $reservation = match ($validated['category']) {
            'rooms' => RoomReservation::findOrFail($id),
            'event' => EventReservation::findOrFail($id),
            'facilities' => FacilityReservation::findOrFail($id),
            'dining' => DiningReservation::findOrFail($id),
        };

        $originalTotal = round((float) ($reservation->total_amount ?? 0), 2);
        $paymentTotals = ['paid' => $this->overallReservationTotals($reservation)['paid']];

        $calculatedTotal = match ($validated['category']) {
            'rooms' => ReservationPricing::room(
                Room::findOrFail($validated['room_id'] ?? $reservation->room_id),
                $validated['check_in'],
                $validated['check_out'],
                (int) ($validated['number_of_guests'] ?? $reservation->number_of_guests ?? 1),
                array_key_exists('adult_guests', $validated) ? (int) $validated['adult_guests'] : $reservation->adult_guests,
                array_key_exists('kid_guests', $validated) ? (int) $validated['kid_guests'] : $reservation->kid_guests
            ),
            'facilities' => ReservationPricing::facilities(
                collect([Facility::findOrFail($validated['facility_id'] ?? $reservation->facility_id)]),
                (int) ($validated['facility_quantity'] ?? $reservation->facility_quantity ?? 1),
                $validated['check_in'],
                $validated['check_out']
            ),
            'event' => ReservationPricing::events(
                collect([Event::findOrFail($validated['event_id'] ?? $reservation->event_id)]),
                (int) ($validated['number_of_guests'] ?? 1),
                !empty($validated['event_start_time']) && !empty($validated['event_end_time'])
                    ? max(1, Carbon::parse($validated['event_start_time'])->diffInHours(Carbon::parse($validated['event_end_time'])))
                    : 1
            ),
            'dining' => ReservationPricing::dining($diningSelections),
        };

        $validated['total_amount'] = $calculatedTotal;

        if (!empty($validated['dining_id'])) {
            $diningIdList = collect(explode(',', $validated['dining_id']))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '')
                ->all();

            $invalidDiningIds = collect($diningIdList)->filter(fn ($id) => !DiningMenu::whereKey($id)->exists())->values()->all();
            abort_if(!empty($invalidDiningIds), 422, 'One or more selected dining items are invalid.');
            $validated['dining_id'] = implode(',', $diningIdList);
        }

        $attributes = collect($validated)->except(['category', 'amount_paid'])->all();
        if ($validated['category'] === 'rooms') {
            $attributes['room_check_in_time'] = $validated['check_in_time'] ?? null;
            $attributes['room_check_out_time'] = $validated['check_out_time'] ?? null;
        } elseif ($validated['category'] === 'event') {
            $attributes['event_start_time'] = $validated['event_start_time'] ?? $validated['check_in_time'] ?? null;
            $attributes['event_end_time'] = $validated['event_end_time'] ?? $validated['check_out_time'] ?? null;
        } elseif ($validated['category'] === 'facilities') {
            $attributes['facility_start_time'] = $validated['check_in_time'] ?? null;
            $attributes['facility_end_time'] = $validated['check_out_time'] ?? null;
        }

        $reservation->update(array_intersect_key($attributes, array_flip($reservation->getFillable())));

        $finalTotal = round((float) ($validated['total_amount'] ?? 0), 2);

        $this->createRefundIfDue(
            $reservation,
            $originalTotal,
            $finalTotal,
            $paymentTotals['paid'],
            $finalTotal < $originalTotal ? 'Reservation Change' : null
        );

        $refundDue = $this->calculateRefundAmount($originalTotal, $finalTotal, $paymentTotals['paid']);
        $balanceDue = round(max($finalTotal - $paymentTotals['paid'], 0), 2);
        $updateMessage = $refundDue > 0
            ? 'Reservation updated successfully! Refund Due: ₱' . number_format($refundDue, 2) . ' (Pending).'
            : ($balanceDue > 0
                ? 'Reservation updated successfully! Balance Due: ₱' . number_format($balanceDue, 2) . '.'
                : 'Reservation updated successfully!');

        if (!empty($diningSelections) && method_exists($reservation, 'diningItems')) {
            $reservation->diningItems()->delete();
            $reservation->diningItems()->createMany($diningSelections);
        }

        return $request->routeIs('employee.reservations.update')
            ? redirect()->route('employee.reservation')->with('success', $updateMessage)
            : redirect()->route('admin.reservations')->with('success', $updateMessage);
    }

    public function destroyReservation(Request $request, $id)
    {
        $category = $request->validate([
            'category' => ['nullable', 'in:rooms,facilities,event,dining'],
        ])['category'] ?? null;
        $reservationType = match ($category) {
            'rooms' => 'room',
            'event' => 'event',
            'facilities' => 'facility',
            'dining' => 'dining',
            default => null,
        };
        $reservation = match ($category) {
            'rooms' => RoomReservation::with('room')->find($id) ?? Reservation::with('room')->find($id),
            'event' => EventReservation::find($id) ?? Reservation::with('event')->find($id),
            'facilities' => FacilityReservation::find($id) ?? Reservation::with('facility')->find($id),
            'dining' => DiningReservation::find($id) ?? Reservation::find($id),
            default => RoomReservation::with('room')->find($id)
                ?? EventReservation::find($id)
                ?? FacilityReservation::find($id)
                ?? DiningReservation::find($id)
                ?? Reservation::find($id),
        };

        if (!$reservationType && $reservation instanceof Reservation) {
            $reservationType = match ($reservation->category) {
                'event' => 'event',
                'facilities' => 'facility',
                'dining' => 'dining',
                default => 'room',
            };
        } elseif (!$reservationType) {
            $reservationType = $reservation instanceof EventReservation ? 'event'
                : ($reservation instanceof FacilityReservation ? 'facility'
                : ($reservation instanceof DiningReservation ? 'dining' : 'room'));
        }

        abort_if(!$reservation, 404, 'Reservation not found');

        // If this is a room reservation that's occupied, reset the room status to available
        if ($reservationType === 'room' && $reservation->room_id && $reservation->room && $reservation->room->status === 'occupied') {
            $reservation->room->update([
                'status' => 'available',
                'cleaning_status' => 'dirty',
            ]);
        }

        $reservation->delete();

        return $request->routeIs('employee.reservations.destroy')
            ? redirect()->route('employee.reservation')->with('success', 'Reservation deleted successfully!')
            : redirect()->route('admin.reservations')->with('success', 'Reservation deleted successfully!');
    }

    public function refundHistory(Request $request)
    {
        $refunds = Refund::with(['reservationable', 'processedBy'])->latest('refund_date')->latest('id')->get();
        $routePrefix = $request->routeIs('employee.*') ? 'employee.' : 'admin.';

        return view($routePrefix . 'refund', compact('refunds', 'routePrefix'));
    }

    public function markRefunded(Request $request, Refund $refund)
    {
        abort_if($refund->status === 'Refunded', 422, 'This refund has already been marked as refunded.');

        $validated = $request->validate([
            'refund_payment_method' => ['nullable', Rule::in(['Cash', 'GCash', 'Maya', 'Bank Transfer', 'Credit/Debit Card'])],
            'refund_reference_number' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn () => filled($request->input('refund_payment_method')) && $request->input('refund_payment_method') !== 'Cash')],
            'refund_receipt' => ['nullable', 'image', 'max:5120', Rule::requiredIf(fn () => filled($request->input('refund_payment_method')) && $request->input('refund_payment_method') !== 'Cash')],
        ]);

        $paymentMethod = $validated['refund_payment_method'] ?? 'Cash';

        if ($request->hasFile('refund_receipt')) {
            $validated['refund_receipt'] = $request->file('refund_receipt')->store('refund-receipts', 'public');
        }

        $refund->update([
            'status' => 'Refunded',
            'processed_by' => $request->user()->id,
            'refund_date' => now()->toDateString(),
            'refund_payment_method' => $paymentMethod,
            'refund_reference_number' => $validated['refund_reference_number'] ?? null,
            'refund_receipt' => $validated['refund_receipt'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Refund marked as refunded successfully.');
    }

    public function destroyRefund(Request $request, Refund $refund)
    {
        $refund->delete();

        $routePrefix = $request->routeIs('employee.*') ? 'employee.' : 'admin.';
        return redirect()->route($routePrefix . 'refunds')->with('success', 'Refund deleted successfully.');
    }

    private function overallReservationTotals($reservation): array
    {
        $rows = collect([
            ...RoomReservation::with('payments')->get(),
            ...FacilityReservation::with('payments')->get(),
            ...EventReservation::with('payments')->get(),
            ...DiningReservation::with('payments')->get(),
        ])->filter(function ($row) use ($reservation) {
            if (!isset($row->guest_email) || !isset($row->check_in)) {
                return false;
            }

            return $row->guest_email === $reservation->guest_email
                && optional($row->check_in)->toDateString() === optional($reservation->check_in)->toDateString()
                && !in_array($row->status, ['cancelled', 'completed'], true);
        })->values();

        if ($rows->isEmpty()) {
            $rows = collect([$reservation]);
        }

        $total = (float) $rows->sum(fn ($row) => (float) ($row->total_amount ?? 0));
        $paid = (float) $rows->sum(function ($row) {
            $row->loadMissing('payments');
            $rowPaid = (float) ($row->amount_paid ?? 0);
            $paymentPaid = (float) ($row->payments?->sum('amount') ?? 0);

            return max($rowPaid, $paymentPaid);
        });

        return [
            'total' => round($total, 2),
            'paid' => round(min(max($paid, 0), max($total, 0)), 2),
        ];
    }

    private function calculateRefundAmount(float $originalTotal, float $finalTotal, float $totalPaid): float
    {
        $originalTotal = max($originalTotal, 0);
        $finalTotal = max($finalTotal, 0);
        $totalPaid = max($totalPaid, 0);

        if ($finalTotal >= $originalTotal || $totalPaid < $finalTotal) {
            return 0.0;
        }

        $reduction = $originalTotal - $finalTotal;

        return round(min(max($totalPaid, 0), $reduction), 2);
    }

    private function createRefundIfDue($reservation, float $originalTotal, float $finalTotal, float $totalPaid, ?string $reason): void
    {
        if (!$reason) {
            return;
        }

        $refundAmount = $this->calculateRefundAmount($originalTotal, $finalTotal, $totalPaid);
        if ($refundAmount <= 0) {
            return;
        }

        $originalTotal = round(max($originalTotal, 0), 2);
        $finalTotal = round(max($finalTotal, 0), 2);
        $totalPaid = round(min(max($totalPaid, 0), $originalTotal), 2);

        $reservation->refunds()->create([
            'guest_name' => $reservation->guest_name,
            'original_total' => $originalTotal,
            'final_total' => $finalTotal,
            'total_paid' => $totalPaid,
            'refund_amount' => $refundAmount,
            'reason' => $reason,
            'refund_date' => now()->toDateString(),
            'status' => 'Pending',
            'processed_by' => auth()->id(),
        ]);
    }

    public function guests()
    {
        $guests = Reservation::select('guest_name', 'guest_email', 'guest_phone')
            ->distinct()
            ->get();
        return view('admin.guests', compact('guests'));
    }

    public function messages()
    {
        $messages = Message::latest()->get();
        return view('admin.messages', compact('messages'));
    }

    public function employeeMessages()
    {
        $messages = Message::latest()->get();
        return view('employee.messages', compact('messages'));
    }

    public function employeeGuestRequests()
    {
        $baseQuery = GuestRequest::with(['guest', 'reservation.room', 'room', 'assignedEmployee'])
            ->where('department', 'Employee')
            ->latest('submitted_at');
        $allRequests = (clone $baseQuery)->get();
        $requestQuery = clone $baseQuery;

        if (request('search')) {
            $search = request('search');
            $requestId = preg_match('/^REQ-?(\d+)$/i', $search, $matches) ? (int) $matches[1] : null;
            $requestQuery->where(function ($query) use ($search, $requestId) {
                $query->where('request_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('guest', fn ($guestQuery) => $guestQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('room', fn ($roomQuery) => $roomQuery->where('room_number', 'like', "%{$search}%"))
                    ->orWhereHas('reservation', fn ($reservationQuery) => $reservationQuery->where('guest_name', 'like', "%{$search}%"));

                if ($requestId !== null) {
                    $query->orWhere('id', $requestId);
                }
            });
        }

        if (request('status') && request('status') !== 'All Status') {
            $requestQuery->where('status', request('status'));
        }

        $requests = $requestQuery->paginate(5)->withQueryString();
        $employees = Staff::where('role', 'employee')->orderBy('name')->get();

        return view('employee.guest-requests', compact('requests', 'allRequests', 'employees'));
    }

    public function employeeGuestRequest($id)
    {
        $guestRequest = GuestRequest::with(['guest', 'reservation.room', 'room', 'assignedEmployee'])
            ->where('department', 'Employee')
            ->findOrFail($id);
        $employees = Staff::where('role', 'employee')->orderBy('name')->get();

        return view('employee.guest-request-detail', compact('guestRequest', 'employees'));
    }

    public function updateEmployeeGuestRequest(Request $request, $id)
    {
        $guestRequest = GuestRequest::where('department', 'Employee')->findOrFail($id);
        $validated = $request->validate([
            'status' => ['required', 'in:New,In Progress,Completed'],
            'assigned_employee_id' => ['nullable', 'exists:staff_users,id'],
            'employee_notes' => ['nullable', 'string'],
        ]);

        if (!empty($validated['assigned_employee_id'])) {
            abort_unless(Staff::whereKey($validated['assigned_employee_id'])->where('role', 'employee')->exists(), 422);
        }

        $guestRequest->fill($validated);
        $guestRequest->completed_at = $validated['status'] === 'Completed' ? ($guestRequest->completed_at ?: now()) : null;
        $guestRequest->save();

        return redirect()->route('employee.guest-requests.show', $guestRequest->id)
            ->with('success', 'Guest request updated successfully.');
    }

    public function replyMessage(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        $validated = $request->validate([
            'admin_reply' => 'required|string',
        ]);

        $message->update([
            'admin_reply' => $validated['admin_reply'],
            'is_replied' => true,
            'replied_at' => now(),
        ]);

        return redirect()->route('admin.messages')->with('success', 'Reply sent successfully!');
    }

    public function storeEmployeeMessage(Request $request)
    {
        $validated = $request->validate([
            'recipient' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        Message::create([
            'customer_name' => $request->user()?->name ?? 'Employee',
            'customer_email' => $request->user()?->email ?? 'employee@casaul.com',
            'message' => $validated['message'],
        ]);

        return redirect()->route('employee.messages')->with('success', 'Message sent successfully.');
    }

    public function resolveGuestRequest(Request $request, $id)
    {
        $requests = $request->session()->get('employee_guest_requests', []);

        if (!empty($requests)) {
            foreach ($requests as &$item) {
                if (($item['id'] ?? null) == $id) {
                    $item['status'] = 'Resolved';
                    break;
                }
            }
            unset($item);

            $request->session()->put('employee_guest_requests', $requests);
        }

        return redirect()->route('employee.guest-requests')->with('success', 'Guest request resolved.');
    }

    public function updateMaintenanceReportStatus(Request $request, MaintenanceReport $maintenanceReport)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:In Progress,Completed'],
        ]);

        $maintenanceReport->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Maintenance report status updated successfully.');
    }

    public function reports(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $maintenanceReportsQuery = MaintenanceReport::query()->latest('date_reported');
        if ($from) {
            $maintenanceReportsQuery->whereDate('date_reported', '>=', $from);
        }
        if ($to) {
            $maintenanceReportsQuery->whereDate('date_reported', '<=', $to);
        }

        $maintenanceReports = $maintenanceReportsQuery->get();
        $maintenanceStatusLabels = ['Pending', 'Repairing', 'In Progress', 'Completed'];
        $maintenanceStatusData = collect($maintenanceStatusLabels)
            ->map(fn($status) => $maintenanceReports->where('status', $status)->count())
            ->all();
        $maintenancePriorityLabels = ['Low', 'Medium', 'High', 'Urgent'];
        $maintenancePriorityData = collect($maintenancePriorityLabels)
            ->map(fn($priority) => $maintenanceReports->where('priority', $priority)->count())
            ->all();
        $maintenanceCategoryData = $maintenanceReports->groupBy('category')
            ->map(fn($group) => $group->count());
        $maintenanceCategoryLabels = $maintenanceCategoryData->keys()->values()->all();
        $maintenanceCategoryCounts = $maintenanceCategoryData->values()->map(fn($count) => (int) $count)->all();
        $maintenancePending = $maintenanceReports->where('status', 'Pending')->count();
        $maintenanceRepairing = $maintenanceReports->whereIn('status', ['Repairing', 'In Progress'])->count();
        $maintenanceCompleted = $maintenanceReports->where('status', 'Completed')->count();

        $reservations = $this->unifiedReservations($from, $to)->sortByDesc('created_at')->values();
        $completedReservations = $reservations->where('status', 'completed');
        $confirmedReservations = $reservations->where('status', 'confirmed');
        $pendingReservations = $reservations->where('status', 'pending');
        $cancelledReservations = $reservations->where('status', 'cancelled');

        $totalRevenue = (float) $completedReservations->sum('total_amount');
        $totalPaymentsReceived = $totalRevenue;
        $revenueThisMonth = (float) $completedReservations->filter(function ($reservation) {
            return $reservation->updated_at && $reservation->updated_at->isSameMonth(now()) && $reservation->updated_at->isSameYear(now());
        })->sum('total_amount');
        $averageRevenuePerReservation = $completedReservations->count() > 0 ? $totalRevenue / $completedReservations->count() : 0;

        $recentPayments = $completedReservations->sortByDesc(function ($reservation) {
            return $reservation->updated_at ?? $reservation->created_at;
        })->take(8);

        $completedRevenueByMonth = $completedReservations
            ->filter(function ($reservation) use ($from, $to) {
                if (!$reservation->updated_at) {
                    return false;
                }
                if ($from && $reservation->updated_at->lt(Carbon::parse($from)->startOfDay())) {
                    return false;
                }
                if ($to && $reservation->updated_at->gt(Carbon::parse($to)->endOfDay())) {
                    return false;
                }

                return true;
            })
            ->groupBy(function ($reservation) {
                return $reservation->updated_at->format('Y-m');
            })
            ->map(function ($group) {
                return $group->sum('total_amount');
            });

        $completedRevenueByMonth = $completedRevenueByMonth->slice(max(0, $completedRevenueByMonth->count() - 6));

        $monthlyLabels = $completedRevenueByMonth->keys()
            ->map(fn($month) => Carbon::createFromFormat('Y-m', $month)->format('M Y'))
            ->all();

        $monthlyRevenue = $completedRevenueByMonth->values()
            ->map(fn($revenue) => (float) $revenue)
            ->all();

        $roomTypeRevenueCollection = $completedReservations->filter(function ($reservation) {
            return $reservation->room;
        })->groupBy(function ($reservation) {
            return $reservation->room->room_type ?? 'Unknown';
        })->map(function ($group) {
            return $group->sum('total_amount');
        });

        $roomTypeRevenueLabels = $roomTypeRevenueCollection->keys()->all();
        $roomTypeRevenueData = $roomTypeRevenueCollection->values()->map(fn($value) => (float) $value)->all();

        $paymentMethodLabels = [];
        $paymentMethodData = [];
        if (Schema::hasColumn('reservations', 'payment_method')) {
            $paymentMethodCollection = $reservations->filter(function ($reservation) {
                return !empty($reservation->payment_method);
            })->groupBy(function ($reservation) {
                return $reservation->payment_method;
            })->map(function ($group) {
                return $group->count();
            });

            $paymentMethodLabels = $paymentMethodCollection->keys()->all();
            $paymentMethodData = $paymentMethodCollection->values()->map(fn($count) => (int) $count)->all();
        }

        if (empty($paymentMethodLabels)) {
            $paymentMethodLabels = ['Recorded Payments'];
            $paymentMethodData = [$completedReservations->count() > 0 ? $completedReservations->count() : 0];
        }

        $reservationTrendLabels = [];
        $reservationTrendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = (clone $monthStart)->endOfMonth();
            $count = $this->unifiedReservations()
                ->filter(fn ($reservation) => $reservation->created_at
                    && $reservation->created_at->between($monthStart->startOfDay(), $monthEnd->endOfDay()))
                ->count();

            $reservationTrendLabels[] = $monthStart->format('M Y');
            $reservationTrendData[] = $count;
        }

        $reservationStatusLabels = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
        $reservationStatusData = [
            count($pendingReservations),
            count($confirmedReservations),
            count($completedReservations),
            count($cancelledReservations),
        ];

        $mostBookedRoomTypes = $reservations->filter(function ($reservation) {
            return $reservation->room;
        })->groupBy(function ($reservation) {
            return $reservation->room->room_type ?? 'Unknown';
        })->map(function ($group) {
            return $group->count();
        })->sortDesc();

        $mostBookedRoomTypeLabels = $mostBookedRoomTypes->keys()->all();
        $mostBookedRoomTypeData = $mostBookedRoomTypes->values()->map(fn($count) => (int) $count)->all();

        $rooms = Room::orderBy('room_number')->get();
        $resources = $this->applyResourceReservationStatuses($rooms, Facility::get(), Event::get(), DiningTable::get());
        $rooms = $resources['rooms'];
        $availableRooms = $rooms->where('status', 'available')->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();
        $reservedRooms = $rooms->where('status', 'reserved')->count();
        $totalRooms = $rooms->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        $roomStatusLabels = ['Available', 'Occupied', 'Reserved', 'Maintenance'];
        $roomStatusData = [
            $availableRooms,
            $occupiedRooms,
            $reservedRooms,
            $maintenanceRooms,
        ];

        $occupancyTrendLabels = [];
        $occupancyTrendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = (clone $monthStart)->endOfMonth();
            $count = $this->unifiedReservations()
                ->filter(fn ($reservation) => $reservation->created_at
                    && $reservation->created_at->between($monthStart->startOfDay(), $monthEnd->endOfDay())
                    && in_array($reservation->status, ['confirmed', 'checked-in', 'completed'], true))
                ->count();

            $occupancyTrendLabels[] = $monthStart->format('M Y');
            $occupancyTrendData[] = $count;
        }

        $guestEmails = $reservations->pluck('guest_email')->filter()->unique();
        $totalGuests = $guestEmails->count();
        $returningGuests = $guestEmails->filter(function ($email) use ($reservations) {
            return $reservations->where('guest_email', $email)->count() > 1;
        })->count();
        $newGuests = max(0, $totalGuests - $returningGuests);

        $stayDurations = $reservations->filter(function ($reservation) {
            return $reservation->check_in && $reservation->check_out;
        })->map(function ($reservation) {
            return $reservation->check_in->diffInDays($reservation->check_out);
        });
        $averageStayDuration = $stayDurations->count() > 0 ? round($stayDurations->avg(), 1) : 0;

        $recentGuestActivity = $reservations->sortByDesc(function ($reservation) {
            return $reservation->created_at;
        })->take(8);

        return view('admin.reports', compact(
            'reservations',
            'recentPayments',
            'totalRevenue',
            'totalPaymentsReceived',
            'revenueThisMonth',
            'averageRevenuePerReservation',
            'availableRooms',
            'occupiedRooms',
            'maintenanceRooms',
            'pendingReservations',
            'confirmedReservations',
            'cancelledReservations',
            'monthlyLabels',
            'monthlyRevenue',
            'roomTypeRevenueLabels',
            'roomTypeRevenueData',
            'paymentMethodLabels',
            'paymentMethodData',
            'reservationTrendLabels',
            'reservationTrendData',
            'reservationStatusLabels',
            'reservationStatusData',
            'mostBookedRoomTypeLabels',
            'mostBookedRoomTypeData',
            'rooms',
            'totalRooms',
            'occupancyRate',
            'roomStatusLabels',
            'roomStatusData',
            'occupancyTrendLabels',
            'occupancyTrendData',
            'totalGuests',
            'newGuests',
            'returningGuests',
            'averageStayDuration',
            'recentGuestActivity',
            'maintenanceReports',
            'maintenanceStatusLabels',
            'maintenanceStatusData',
            'maintenancePriorityLabels',
            'maintenancePriorityData',
            'maintenanceCategoryLabels',
            'maintenanceCategoryCounts',
            'maintenancePending',
            'maintenanceRepairing',
            'maintenanceCompleted'
        ));
    }

    public function exportReportsCsv(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $reservations = $this->unifiedReservations($from, $to)->sortByDesc('created_at')->values();
        $completedReservations = $reservations->where('status', 'completed');
        $confirmedReservations = $reservations->where('status', 'confirmed');
        $pendingReservations = $reservations->where('status', 'pending');
        $cancelledReservations = $reservations->where('status', 'cancelled');

        $totalRevenue = (float) $completedReservations->sum('total_amount');
        $totalPaymentsReceived = $totalRevenue;
        $revenueThisMonth = (float) $completedReservations->filter(function ($reservation) {
            return $reservation->updated_at && $reservation->updated_at->isSameMonth(now()) && $reservation->updated_at->isSameYear(now());
        })->sum('total_amount');
        $averageRevenuePerReservation = $completedReservations->count() > 0 ? $totalRevenue / $completedReservations->count() : 0;

        $roomTypeRevenueCollection = $completedReservations->filter(fn ($reservation) => $reservation->room)
            ->groupBy(fn ($reservation) => $reservation->room->room_type ?? 'Unknown')
            ->map(fn ($group) => $group->sum('total_amount'));

        $paymentMethodCollection = $reservations->filter(fn ($reservation) => ! empty($reservation->payment_method))
            ->groupBy(fn ($reservation) => $reservation->payment_method)
            ->map(fn ($group) => $group->count());

        $rooms = Room::orderBy('room_number')->get();
        $availableRooms = $rooms->where('status', 'available')->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();
        $reservedRooms = $rooms->where('status', 'reserved')->count();
        $totalRooms = $rooms->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        $guestEmails = $reservations->pluck('guest_email')->filter()->unique();
        $totalGuests = $guestEmails->count();
        $returningGuests = $guestEmails->filter(function ($email) use ($reservations) {
            return $reservations->where('guest_email', $email)->count() > 1;
        })->count();
        $newGuests = max(0, $totalGuests - $returningGuests);

        $monthlyRevenue = $completedReservations
            ->filter(fn ($reservation) => $reservation->updated_at
                && (!$from || $reservation->updated_at->gte(Carbon::parse($from)->startOfDay()))
                && (!$to || $reservation->updated_at->lte(Carbon::parse($to)->endOfDay())))
            ->groupBy(fn ($reservation) => $reservation->updated_at->format('Y-m'))
            ->map(fn ($group) => $group->sum('total_amount'))
            ->slice(max(0, $reservations->count() > 0 ? 0 : 0));

        $monthlyLabels = $monthlyRevenue->keys()->map(fn ($month) => Carbon::createFromFormat('Y-m', $month)->format('M Y'))->all();
        $monthlyRevenueValues = $monthlyRevenue->values()->map(fn ($value) => (float) $value)->all();

        $filename = 'hotel_reports_' . now()->format('Ymd_His') . '.xls';

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="UTF-8" /><style>body{font-family:Arial,sans-serif;} table{border-collapse:collapse;width:100%;} td,th{border:1px solid #bdbdbd;padding:8px 10px;font-size:12px;vertical-align:top;} .title{font-size:18px;font-weight:bold;background:#ffffff;} .section{font-weight:bold;background:#f2f2f2;text-transform:uppercase;} .header{font-weight:bold;background:#eaeaea;} .metric{font-weight:bold;background:#fafafa;} .amount{text-align:right;} .label{font-weight:bold;} </style></head><body><table>';

        $html .= '<tr><td colspan="2" class="title"><strong>CASAUL Hotel Reports</strong></td></tr>';
        $html .= '<tr><td class="label">Generated At</td><td>' . e(now()->format('m/d/Y g:i A')) . '</td></tr>';
        $html .= '<tr><td class="label">Date From</td><td>' . e($from ?: 'All') . '</td></tr>';
        $html .= '<tr><td class="label">Date To</td><td>' . e($to ?: 'All') . '</td></tr>';
        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';

        $html .= '<tr><td colspan="2" class="section">Financial Summary</td></tr>';
        $html .= '<tr class="header"><th>Metric</th><th>Value</th></tr>';
        $html .= '<tr><td class="metric">Total Revenue</td><td class="amount">₱' . number_format($totalRevenue, 2) . '</td></tr>';
        $html .= '<tr><td class="metric">Total Payments Received</td><td class="amount">₱' . number_format($totalPaymentsReceived, 2) . '</td></tr>';
        $html .= '<tr><td class="metric">Revenue This Month</td><td class="amount">₱' . number_format($revenueThisMonth, 2) . '</td></tr>';
        $html .= '<tr><td class="metric">Average Revenue Per Reservation</td><td class="amount">₱' . number_format($averageRevenuePerReservation, 2) . '</td></tr>';
        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';

        $html .= '<tr><td colspan="8" class="section">Payment Transactions</td></tr>';
        $html .= '<tr class="header"><th>Guest</th><th>Email</th><th>Phone</th><th>Room</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Amount</th></tr>';

        foreach ($completedReservations as $reservation) {
            $html .= '<tr>';
            $html .= '<td>' . e($reservation->guest_name) . '</td>';
            $html .= '<td>' . e($reservation->guest_email) . '</td>';
            $html .= '<td>' . e($reservation->guest_phone ?? 'N/A') . '</td>';
            $html .= '<td>' . e($reservation->room ? $reservation->room->room_number : 'N/A') . '</td>';
            $html .= '<td>' . e($reservation->check_in ? $reservation->check_in->format('Y-m-d') : 'N/A') . '</td>';
            $html .= '<td>' . e($reservation->check_out ? $reservation->check_out->format('Y-m-d') : 'N/A') . '</td>';
            $html .= '<td>' . e($reservation->status) . '</td>';
            $html .= '<td class="amount">₱' . number_format((float) $reservation->total_amount, 2) . '</td>';
            $html .= '</tr>';
        }

        if ($completedReservations->isEmpty()) {
            $html .= '<tr><td colspan="8">No completed payments found.</td></tr>';
        }

        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';

        $html .= '<tr><td colspan="2" class="section">Payment Method Breakdown</td></tr>';
        $html .= '<tr class="header"><th>Method</th><th>Count</th></tr>';
        if ($paymentMethodCollection->isNotEmpty()) {
            foreach ($paymentMethodCollection as $method => $count) {
                $html .= '<tr><td>' . e($method) . '</td><td>' . e($count) . '</td></tr>';
            }
        } else {
            $html .= '<tr><td>Recorded Payments</td><td>' . e($completedReservations->count()) . '</td></tr>';
        }

        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';

        $html .= '<tr><td colspan="2" class="section">Revenue by Room Type</td></tr>';
        $html .= '<tr class="header"><th>Room Type</th><th>Revenue</th></tr>';
        foreach ($roomTypeRevenueCollection as $roomType => $amount) {
            $html .= '<tr><td>' . e($roomType) . '</td><td class="amount">₱' . number_format((float) $amount, 2) . '</td></tr>';
        }

        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';

        $html .= '<tr><td colspan="2" class="section">Monthly Revenue</td></tr>';
        $html .= '<tr class="header"><th>Month</th><th>Revenue</th></tr>';
        foreach ($monthlyLabels as $index => $monthLabel) {
            $html .= '<tr><td>' . e($monthLabel) . '</td><td class="amount">₱' . number_format((float) ($monthlyRevenueValues[$index] ?? 0), 2) . '</td></tr>';
        }

        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';

        $html .= '<tr><td colspan="8" class="section">Maintenance Reports</td></tr>';
        $html .= '<tr class="header"><th>Room</th><th>Issue</th><th>Category</th><th>Priority</th><th>Reported By</th><th>Date &amp; Time</th><th>Status</th><th>Description</th></tr>';
        foreach ($maintenanceReports as $report) {
            $html .= '<tr>';
            $html .= '<td>' . e($report->room_number) . '</td>';
            $html .= '<td>' . e($report->problem ?: $report->category) . '</td>';
            $html .= '<td>' . e($report->category) . '</td>';
            $html .= '<td>' . e($report->priority) . '</td>';
            $html .= '<td>' . e($report->reported_by) . '</td>';
            $html .= '<td>' . e(optional($report->date_reported)->format('d/m/Y h:i A')) . '</td>';
            $html .= '<td>' . e($report->status) . '</td>';
            $html .= '<td>' . e($report->description) . '</td>';
            $html .= '</tr>';
        }

        if ($maintenanceReports->isEmpty()) {
            $html .= '<tr><td colspan="8">No maintenance reports found.</td></tr>';
        }

        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';

        $html .= '<tr><td colspan="2" class="section">Reservation Summary</td></tr>';
        $html .= '<tr class="header"><th>Metric</th><th>Value</th></tr>';
        $html .= '<tr><td class="metric">Total Reservations</td><td>' . e($reservations->count()) . '</td></tr>';
        $html .= '<tr><td class="metric">Pending</td><td>' . e($pendingReservations->count()) . '</td></tr>';
        $html .= '<tr><td class="metric">Confirmed</td><td>' . e($confirmedReservations->count()) . '</td></tr>';
        $html .= '<tr><td class="metric">Completed</td><td>' . e($completedReservations->count()) . '</td></tr>';
        $html .= '<tr><td class="metric">Cancelled</td><td>' . e($cancelledReservations->count()) . '</td></tr>';

        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';

        $html .= '<tr><td colspan="2" class="section">Occupancy Summary</td></tr>';
        $html .= '<tr class="header"><th>Metric</th><th>Value</th></tr>';
        $html .= '<tr><td class="metric">Occupancy Rate</td><td>' . e($occupancyRate) . '%</td></tr>';
        $html .= '<tr><td class="metric">Available Rooms</td><td>' . e($availableRooms) . '</td></tr>';
        $html .= '<tr><td class="metric">Occupied Rooms</td><td>' . e($occupiedRooms) . '</td></tr>';
        $html .= '<tr><td class="metric">Reserved Rooms</td><td>' . e($reservedRooms) . '</td></tr>';
        $html .= '<tr><td class="metric">Maintenance Rooms</td><td>' . e($maintenanceRooms) . '</td></tr>';
        $html .= '<tr><td class="metric">Total Rooms</td><td>' . e($totalRooms) . '</td></tr>';

        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';

        $html .= '<tr><td colspan="2" class="section">Guest Summary</td></tr>';
        $html .= '<tr class="header"><th>Metric</th><th>Value</th></tr>';
        $html .= '<tr><td class="metric">Total Guests</td><td>' . e($totalGuests) . '</td></tr>';
        $html .= '<tr><td class="metric">New Guests</td><td>' . e($newGuests) . '</td></tr>';
        $html .= '<tr><td class="metric">Returning Guests</td><td>' . e($returningGuests) . '</td></tr>';

        $html .= '</table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function printReports(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $reservations = $this->unifiedReservations($from, $to)->sortByDesc('created_at')->values();

        $completedRevenueByMonth = $reservations->where('status', 'completed')
            ->filter(fn ($reservation) => $reservation->updated_at)
            ->groupBy(function ($reservation) {
                return $reservation->updated_at->format('Y-m');
            })
            ->map(function ($group) {
                return $group->sum('total_amount');
            });

        $completedRevenueByMonth = $completedRevenueByMonth->slice(
            max(0, $completedRevenueByMonth->count() - 6)
        );

        $monthlyLabels = $completedRevenueByMonth->keys()
            ->map(fn($month) => \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('M Y'))
            ->all();

        $monthlyRevenue = $completedRevenueByMonth->values()
            ->map(fn($revenue) => (float) $revenue)
            ->all();

        return view('admin.reports_print', compact('reservations', 'monthlyLabels', 'monthlyRevenue', 'from', 'to'));
    }

    public function notifications()
    {
        $messages = Message::latest()->get();
        return view('admin.notifications', compact('messages'));
    }

    public function manageAccount()
    {
        $this->ensureAdmin();

        $users = Staff::with('creator')->latest()->paginate(5, ['*'], 'accounts_page');

        return view('admin.manage-account', [
            'users' => $users,
            'totalUsers' => Staff::count(),
            'totalAdmins' => Staff::where('role', 'admin')->count(),
            'totalEmployees' => Staff::where('role', 'employee')->count(),
            'totalHousekeeping' => Staff::where('role', 'housekeeping')->count(),
            'activeUsers' => Staff::where('is_active', true)->count(),
        ]);
    }

    public function storeAccount(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:3'],
            'email' => ['required', 'email', 'max:255', 'unique:staff_users,email', 'unique:guest_users,email'],
            'contact_no' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'in:admin,employee,housekeeping'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $middleInitial = $validated['middle_initial'] ?? null;

        Staff::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_initial' => $middleInitial,
            'name' => trim($validated['first_name'] . ' ' . ($middleInitial ? $middleInitial . '. ' : '') . $validated['last_name']),
            'email' => $validated['email'],
            'contact_no' => $validated['contact_no'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.manage-account')->with('success', 'Account created successfully! The user can now log in with their email and password.');
    }

    public function updateAccountUser(Request $request, $id)
    {
        $this->ensureAdmin();

        $user = Staff::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:3'],
            'email' => ['required', 'email', 'max:255', 'unique:staff_users,email,' . $user->id, 'unique:guest_users,email'],
            'contact_no' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'in:admin,employee,housekeeping'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $middleInitial = $validated['middle_initial'] ?? null;

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->middle_initial = $middleInitial;
        $user->name = trim($validated['first_name'] . ' ' . ($middleInitial ? $middleInitial . '. ' : '') . $validated['last_name']);
        $user->email = $validated['email'];
        $user->contact_no = $validated['contact_no'] ?? null;
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.manage-account')->with('success', 'Account updated successfully!');
    }

    public function updateUserStatus(Request $request, $id)
    {
        $this->ensureAdmin();

        $user = Staff::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.manage-account')->withErrors(['You cannot deactivate your own account.']);
        }

        $validated = $request->validate([
            'is_active' => ['required', 'in:0,1'],
        ]);

        $user->update(['is_active' => (bool) $validated['is_active']]);

        return redirect()->route('admin.manage-account')->with('success', 'Account status updated successfully!');
    }

    public function destroyUser($id)
    {
        $this->ensureAdmin();

        $user = Staff::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.manage-account')->withErrors(['You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()->route('admin.manage-account')->with('success', 'Account deleted successfully!');
    }

    public function bulkDestroyUsers(Request $request)
    {
        $this->ensureAdmin();

        $ids = $request->input('user_ids', []);
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $userIds = collect($ids)
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === (int) auth()->id())
            ->unique()
            ->values()
            ->all();

        if (!count($userIds)) {
            return redirect()->route('admin.manage-account')->withErrors(['No valid accounts selected for deletion.']);
        }

        $deleted = Staff::whereIn('id', $userIds)->delete();

        return redirect()->route('admin.manage-account')->with('success', $deleted . ' account(s) deleted successfully!');
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403, 'Only administrators can manage accounts.');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function updateAccount(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:staff_users,email,' . $user->id, 'unique:guest_users,email'],
            'current_password' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Verify the current password
        if (!\Illuminate\Support\Facades\Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password you entered is incorrect.',
            ])->withInput();
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Only update the password if a new one was provided
        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Account settings updated successfully!');
    }
}
