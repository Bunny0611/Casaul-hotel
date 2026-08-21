<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Message;
use App\Models\User;
use App\Models\InventoryItem;

class AdminController extends Controller
{
    public function dashboard()
    {
        // === Core Stats ===
        $totalRevenue = Reservation::where('status', 'completed')->sum('total_amount') ?? 0;
        $availableRooms = Room::where('status', 'available')->count();
        $totalRooms = Room::count();
        $activeReservations = Reservation::where('status', 'confirmed')->count();
        $totalGuests = Reservation::distinct('guest_email')->count('guest_email');
        $unreadMessages = Message::where('is_replied', false)->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round((($totalRooms - $availableRooms) / $totalRooms) * 100) : 0;

        // === Reservation Status Counts ===
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $confirmedReservations = Reservation::where('status', 'confirmed')->count();
        $completedReservations = Reservation::where('status', 'completed')->count();
        $cancelledReservations = Reservation::where('status', 'cancelled')->count();

        // === Average Daily Revenue (Last 30 Days) ===
        // Use updated_at so recent completions/payments count toward the average.
        $thirtyDaysAgo = now()->subDays(30);
        $last30DaysRevenue = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $thirtyDaysAgo)
            ->sum('total_amount');
        $avgDailyRevenue = $last30DaysRevenue > 0 ? round($last30DaysRevenue / 30, 2) : 0;

        // === Room Type Distribution ===
        $roomTypes = Room::selectRaw('room_type, COUNT(*) as count')
            ->groupBy('room_type')
            ->pluck('count', 'room_type')
            ->toArray();

        // === Recent Reservations (Latest 5) ===
        $recentReservations = Reservation::with('room')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'availableRooms',
            'totalRooms',
            'activeReservations',
            'totalGuests',
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
            'recentReservations'
        ));
    }

    public function rooms()
    {
        $rooms = Room::orderBy('room_number')->paginate(5);
        $amenities = InventoryItem::where('category', 'amenities')->orderBy('name')->paginate(5, ['*'], 'amenities_page')->appends(['tab' => 'amenities']);
        $eventPlaces = InventoryItem::where('category', 'event_place')->orderBy('name')->paginate(5, ['*'], 'event_places_page')->appends(['tab' => 'event-place']);
        $dining = InventoryItem::where('category', 'dining')->orderBy('name')->paginate(5, ['*'], 'dining_page')->appends(['tab' => 'dining']);
        $activeTab = request()->query('tab', 'rooms');

        if (!in_array($activeTab, ['rooms', 'amenities', 'event-place', 'dining'], true)) {
            $activeTab = 'rooms';
        }

        return view('admin.rooms', compact('rooms', 'amenities', 'eventPlaces', 'dining', 'activeTab'));
    }

    public function storeInventoryItem(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'in:amenities,event_place,dining'],
            'name' => ['required', 'string', 'max:255', Rule::unique('inventory_items', 'name')->where(fn ($query) => $query->where('category', $request->category))],
            'type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_to' => ['nullable', 'date_format:H:i'],
            'quantity' => ['nullable', 'integer', 'min:0'],
        ]);
        $validated['status'] = strtolower($validated['status']);

        InventoryItem::create($validated);

        return redirect()->route('admin.rooms')->with('success', 'Inventory item added successfully.');
    }

    public function updateInventoryItem(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);
        $validated = $request->validate([
            'category' => ['required', 'in:amenities,event_place,dining'],
            'name' => ['required', 'string', 'max:255', Rule::unique('inventory_items', 'name')->where(fn ($query) => $query->where('category', $request->category))->ignore($item->id)],
            'type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_to' => ['nullable', 'date_format:H:i'],
            'quantity' => ['nullable', 'integer', 'min:0'],
        ]);
        $validated['status'] = strtolower($validated['status']);

        $item->update($validated);

        return redirect()->route('admin.rooms')->with('success', 'Inventory item updated successfully.');
    }

    public function updateInventoryStatus(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);
        $validated = $request->validate(['status' => ['required', 'string', 'max:50']]);
        $item->update(['status' => strtolower($validated['status'])]);

        return redirect()->route('admin.rooms')->with('success', 'Inventory status updated successfully.');
    }

    public function destroyInventoryItem($id)
    {
        $item = InventoryItem::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.rooms')->with('success', 'Inventory item deleted successfully.');
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

        if (!count($inventoryIds) || !in_array($category, ['amenities', 'event_place', 'dining'], true)) {
            return redirect()->route('admin.rooms')->with('success', 'No inventory items selected for deletion.');
        }

        $deleted = InventoryItem::where('category', $category)->whereIn('id', $inventoryIds)->delete();

        return redirect()->route('admin.rooms')->with('success', $deleted . ' inventory item(s) deleted successfully.');
    }

    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:255|unique:rooms,room_number',
            'room_type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'floor' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);
        $validated['status'] = 'available';

        Room::create($validated);
        return redirect()->route('admin.rooms')->with('success', 'Room created successfully!');
    }

    public function updateRoom(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $id,
            'room_type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'floor' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

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

    public function reservations()
    {
        $reservations = Reservation::with('room')->latest()->get();
        $rooms = Room::orderBy('room_number')->get();
        return view('admin.reservations', compact('reservations', 'rooms'));
    }

    public function storeReservation(Request $request)
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after_or_equal:check_in'],
            'status' => ['required', 'in:pending,confirmed,cancelled,completed'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'special_requests' => ['nullable', 'string'],
        ]);

        Reservation::create($validated);

        return redirect()->route('admin.reservations')->with('success', 'Reservation created successfully!');
    }

    public function updateReservationStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => $request->status]);
        return redirect()->route('admin.reservations')->with('success', 'Reservation status updated successfully!');
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
        $requests = session()->get('employee_guest_requests', []);
        return view('employee.guest-requests', compact('requests'));
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

    public function reports(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $reservationsQuery = Reservation::with('room')->latest();
        if ($from) {
            $reservationsQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $reservationsQuery->whereDate('created_at', '<=', $to);
        }

        $reservations = $reservationsQuery->get();
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

        $monthlyRevenueQuery = Reservation::where('status', 'completed');
        if ($from) {
            $monthlyRevenueQuery->whereDate('updated_at', '>=', $from);
        }
        if ($to) {
            $monthlyRevenueQuery->whereDate('updated_at', '<=', $to);
        }

        $completedRevenueByMonth = $monthlyRevenueQuery->orderBy('updated_at')
            ->get(['updated_at', 'total_amount'])
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
            $count = Reservation::whereBetween('created_at', [$monthStart->startOfDay(), $monthEnd->endOfDay()])
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

        $hasReservations = $reservations->count() > 0;

        if (! $hasReservations) {
            $totalRevenue = 180000.00;
            $totalPaymentsReceived = 180000.00;
            $revenueThisMonth = 34000.00;
            $averageRevenuePerReservation = 4250.00;

            $paymentMethodLabels = ['Cash', 'Credit Card', 'Bank Transfer'];
            $paymentMethodData = [45, 30, 25];

            $roomTypeRevenueLabels = ['Deluxe', 'Executive', 'Standard'];
            $roomTypeRevenueData = [62000.00, 52000.00, 66000.00];

            $monthlyLabels = ['Mar 2026', 'Apr 2026', 'May 2026', 'Jun 2026', 'Jul 2026', 'Aug 2026'];
            $monthlyRevenue = [22000.00, 25000.00, 28000.00, 30000.00, 33000.00, 35000.00];

            $reservationTrendLabels = $monthlyLabels;
            $reservationTrendData = [18, 22, 20, 24, 26, 28];
            $reservationStatusData = [10, 18, 26, 6];
            $mostBookedRoomTypeLabels = ['Deluxe', 'Executive', 'Standard'];
            $mostBookedRoomTypeData = [20, 15, 12];

            $totalGuests = 245;
            $newGuests = 58;
            $returningGuests = 187;
            $averageStayDuration = 3.7;

            $dummyRooms = [
                Room::make(['room_number' => '102']),
                Room::make(['room_number' => '205']),
                Room::make(['room_number' => '310']),
            ];

            $recentPayments = collect([
                Reservation::make([
                    'guest_name' => 'John Doe',
                    'total_amount' => 6200.00,
                    'status' => 'completed',
                ])->setRelation('room', $dummyRooms[0]),
                Reservation::make([
                    'guest_name' => 'Maria Santos',
                    'total_amount' => 4300.00,
                    'status' => 'completed',
                ])->setRelation('room', $dummyRooms[1]),
                Reservation::make([
                    'guest_name' => 'Alex Cruz',
                    'total_amount' => 5300.00,
                    'status' => 'completed',
                ])->setRelation('room', $dummyRooms[2]),
            ]);

            $recentGuestActivity = $recentPayments;
            $reservations = $recentPayments;
            $confirmedReservations = $reservations->where('status', 'confirmed');
            $pendingReservations = $reservations->where('status', 'pending');
            $cancelledReservations = $reservations->where('status', 'cancelled');
        }

        $rooms = Room::orderBy('room_number')->get();
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
            $count = Reservation::whereBetween('created_at', [$monthStart->startOfDay(), $monthEnd->endOfDay()])
                ->whereIn('status', ['confirmed', 'completed'])
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
            'recentGuestActivity'
        ));
    }

    public function exportReportsCsv(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $query = Reservation::with('room')->latest();
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);

        $reservations = $query->get();

        $filename = 'reports_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['Guest', 'Email', 'Phone', 'Room', 'Check In', 'Check Out', 'Status', 'Amount'];

        $callback = function () use ($reservations, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($reservations as $r) {
                fputcsv($file, [
                    $r->guest_name,
                    $r->guest_email,
                    $r->guest_phone,
                    $r->room ? $r->room->room_number : 'N/A',
                    $r->check_in,
                    $r->check_out,
                    $r->status,
                    number_format($r->total_amount, 2),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printReports(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $reservations = Reservation::with('room')->latest();
        if ($from) $reservations->whereDate('created_at', '>=', $from);
        if ($to) $reservations->whereDate('created_at', '<=', $to);
        $reservations = $reservations->get();

        $completedRevenueByMonth = Reservation::where('status', 'completed')
            ->orderBy('updated_at')
            ->get(['updated_at', 'total_amount'])
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

        $users = User::with('creator')->latest()->paginate(5, ['*'], 'accounts_page');

        return view('admin.manage-account', [
            'users' => $users,
            'totalUsers' => User::count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'totalEmployees' => User::where('role', 'employee')->count(),
            'totalHousekeeping' => User::where('role', 'housekeeping')->count(),
            'activeUsers' => User::where('is_active', true)->count(),
        ]);
    }

    public function storeAccount(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:3'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'contact_no' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'in:admin,employee,housekeeping'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $middleInitial = $validated['middle_initial'] ?? null;

        User::create([
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

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:3'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
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

        $user = User::findOrFail($id);

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

        $user = User::findOrFail($id);

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

        $deleted = User::whereIn('id', $userIds)->delete();

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
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
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
