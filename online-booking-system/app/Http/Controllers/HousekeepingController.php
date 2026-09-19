<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuestRequest;
use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Staff;
use App\Models\MaintenanceReport;
use App\Models\Message;

class HousekeepingController extends Controller
{
    public function dashboard()
    {
        $rooms = Room::orderBy('room_number')->get();

        // Stats
        $totalRooms = $rooms->count();
        $cleanRooms = $rooms->where('cleaning_status', 'clean')->count();
        $dirtyRooms = $rooms->where('cleaning_status', 'dirty')->count();
        $inProgress = $rooms->where('cleaning_status', 'in_progress')->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $pendingTasks = HousekeepingTask::with(['room', 'assignedStaff'])
            ->whereIn('status', ['pending', 'in_progress'])->get();
        $priorityTasks = $pendingTasks->sortBy(function (HousekeepingTask $task) {
            return ['urgent' => 1, 'high' => 2, 'medium' => 3, 'low' => 4][$task->priority] ?? 5;
        })->take(4);
        $cleaningPercentage = $totalRooms > 0 ? (int) round(($cleanRooms / $totalRooms) * 100) : 0;

        return view('housekeeping.dashboard', compact(
            'rooms',
            'totalRooms',
            'cleanRooms',
            'dirtyRooms',
            'inProgress',
            'occupiedRooms',
            'pendingTasks',
            'priorityTasks',
            'cleaningPercentage'
        ));
    }


    public function assignedRooms()
    {
        $tasks = HousekeepingTask::with(['room', 'assignedStaff', 'reservation'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get();
        $rooms = Room::orderBy('room_number')->get();
        $reservations = Reservation::whereIn('status', ['pending', 'confirmed', 'checked-in'])
            ->with('room')->latest()->get();
        $staff = Staff::where('role', 'housekeeping')->where('is_active', true)
            ->orderBy('name')->get();

        return view('housekeeping.assigned-rooms', compact('tasks', 'rooms', 'reservations', 'staff'));
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'reservation_id' => ['nullable', 'exists:reservations,id'],
            'assigned_staff_id' => ['nullable', 'exists:staff_users,id'],
            'task' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['nullable', 'date_format:H:i'],
            'estimated_duration' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if (!empty($validated['assigned_staff_id'])) {
            abort_unless(Staff::whereKey($validated['assigned_staff_id'])
                ->where('role', 'housekeeping')->exists(), 422, 'Invalid housekeeping staff.');
        }

        HousekeepingTask::create($validated);

        return redirect()->route('housekeeping.assigned-rooms')->with('success', 'Cleaning task assigned.');
    }

    public function startTask(HousekeepingTask $housekeepingTask)
    {
        abort_unless($housekeepingTask->status === 'pending', 422, 'Only pending tasks can be started.');
        $housekeepingTask->update(['status' => 'in_progress', 'started_at' => now()]);
        $housekeepingTask->room()->update(['cleaning_status' => 'in_progress']);

        return back()->with('success', 'Cleaning started.');
    }

    public function completeTask(HousekeepingTask $housekeepingTask)
    {
        abort_unless($housekeepingTask->status === 'in_progress', 422, 'Only active tasks can be completed.');
        $housekeepingTask->update(['status' => 'completed', 'finished_at' => now()]);
        $housekeepingTask->room()->update(['cleaning_status' => 'clean']);

        return back()->with('success', 'Cleaning completed.');
    }

    public function destroyTask(HousekeepingTask $housekeepingTask)
    {
        abort_unless($housekeepingTask->status === 'pending', 422, 'Only pending tasks can be deleted.');
        $housekeepingTask->delete();

        return back()->with('success', 'Pending task deleted.');
    }


    public function roomStatusUpdate(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $roomType = trim((string) $request->query('room_type', ''));
        $roomStatus = trim((string) $request->query('room_status', ''));
        $cleaningStatus = trim((string) $request->query('cleaning_status', ''));

        $baseRooms = Room::orderBy('room_number')->get();

        $roomsQuery = Room::query();

        if ($search !== '') {
            $roomsQuery->where('room_number', 'like', "%{$search}%");
        }

        if ($roomType !== '' && $roomType !== 'All') {
            $roomsQuery->where('room_type', $roomType);
        }

        if ($cleaningStatus !== '' && $cleaningStatus !== 'All') {
            $roomsQuery->where('cleaning_status', $cleaningStatus);
        }

        $rooms = $roomsQuery->orderBy('room_number')->get();

        if ($roomStatus !== '' && $roomStatus !== 'All') {
            $rooms = $rooms->filter(function ($room) use ($roomStatus) {
                return $this->resolveRoomStatusLabel($room) === $roomStatus;
            })->values();
        }

        $roomTypes = $baseRooms
            ->pluck('room_type')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $roomStatusOptions = [
            'All',
            'Vacant Ready',
            'Vacant Clean',
            'Vacant Dirty',
            'Occupied Clean',
            'Occupied Dirty',
            'House Use Dirty',
            'House Use Clean',
            'Out of Order',
            'Blocked',
            'No Show',
            'Slept Out',
            'House Use',
            'Do Not Disturb',
        ];

        $cleaningOptions = [
            'All',
            'clean',
            'dirty',
            'in_progress',
            'ready',
            'blocked',
            'out_of_order',
        ];

        return view('housekeeping.room-status-update', compact(
            'rooms',
            'roomTypes',
            'roomStatusOptions',
            'cleaningOptions',
            'search',
            'roomType',
            'roomStatus',
            'cleaningStatus'
        ));
    }

    private function resolveRoomStatusLabel($room): string
    {
        $status = strtolower((string) ($room->status ?? ''));
        $cleaningStatus = strtolower((string) ($room->cleaning_status ?? ''));

        if (in_array($status, ['vr', 'vacant_ready', 'available', 'vacant'], true)
            && in_array($cleaningStatus, ['clean', 'ready', '', null], true)) {
            return 'Vacant Ready';
        }

        if (in_array($status, ['vc', 'vacant_clean'], true)) {
            return 'Vacant Clean';
        }

        if (in_array($status, ['vd', 'vacant_dirty', 'available', 'vacant'], true)
            && $cleaningStatus === 'dirty') {
            return 'Vacant Dirty';
        }

        if (in_array($status, ['oc', 'occupied_clean', 'occupied'], true)
            && in_array($cleaningStatus, ['clean', 'ready'], true)) {
            return 'Occupied Clean';
        }

        if (in_array($status, ['od', 'occupied_dirty', 'occupied'], true)
            && $cleaningStatus === 'dirty') {
            return 'Occupied Dirty';
        }

        if (in_array($status, ['hsd', 'house_use_dirty'], true)) {
            return 'House Use Dirty';
        }

        if (in_array($status, ['hsuc', 'house_use_clean'], true)) {
            return 'House Use Clean';
        }

        if (in_array($status, ['ooo', 'out_of_order', 'maintenance', 'out of order'], true)
            || in_array($cleaningStatus, ['out_of_order', 'out of order'], true)) {
            return 'Out of Order';
        }

        if (in_array($status, ['blo', 'blocked', 'unavailable'], true)
            || $cleaningStatus === 'blocked') {
            return 'Blocked';
        }

        if (in_array($status, ['ns', 'no_show'], true)) {
            return 'No Show';
        }

        if (in_array($status, ['so', 'slept_out'], true)) {
            return 'Slept Out';
        }

        if (in_array($status, ['hu', 'house_use'], true)) {
            return 'House Use';
        }

        if (in_array($status, ['dnd', 'do_not_disturb'], true)) {
            return 'Do Not Disturb';
        }

        return 'Vacant Ready';
    }

    public function guestRequests()
    {
        $requests = GuestRequest::with(['guest', 'room', 'reservation'])
            ->where('department', 'Housekeeping')
            ->latest('submitted_at')
            ->get();

        $groupedRequests = $requests->groupBy(function ($request) {
            $signature = [
                $request->guest_id ?? 'guest',
                $request->reservation_id ?? 'reservation',
                $request->room_id ?? 'room',
                trim((string) ($request->description ?? '')),
                trim((string) ($request->preferred_time ?? '')),
                trim((string) ($request->priority ?? '')),
                trim((string) ($request->status ?? '')),
                $request->submitted_at ? $request->submitted_at->toDateTimeString() : now()->toDateTimeString(),
            ];

            return md5(implode('|', $signature));
        })->map(function ($group) {
            $first = $group->first();
            $reservation = $first->reservation ?: $this->resolveReservationForGuestRequest($first);

            return (object) [
                'id' => $first->id,
                'guest_id' => $first->guest_id,
                'reservation_id' => $first->reservation_id,
                'reservation_key' => $first->reservation_key,
                'room_id' => $first->room_id,
                'guest' => $first->guest,
                'room' => $first->room,
                'reservation' => $reservation,
                'unit_price' => $first->unit_price,
                'subtotal' => $group->sum(fn ($item) => (float) ($item->subtotal ?? ((float) ($item->unit_price ?? 0) * (int) ($item->quantity ?? 1)))),
                'request_type' => $group->pluck('request_type')->unique()->implode(', '),
                'description' => $first->description,
                'department' => $first->department,
                'priority' => $first->priority,
                'preferred_time' => $first->preferred_time,
                'status' => $first->status,
                'quantity' => $group->sum(fn ($item) => (int) ($item->quantity ?? 1)),
                'submitted_at' => $first->submitted_at,
                'items' => $group->map(fn ($item) => [
                    'request_type' => $item->request_type,
                    'quantity' => (int) ($item->quantity ?? 1),
                    'unit_price' => (float) ($item->unit_price ?? 0),
                    'unit_price_formatted' => '₱' . number_format((float) ($item->unit_price ?? 0), 2),
                    'subtotal' => (float) ($item->subtotal ?? ((float) ($item->unit_price ?? 0) * (int) ($item->quantity ?? 1))),
                    'subtotal_formatted' => '₱' . number_format((float) ($item->subtotal ?? ((float) ($item->unit_price ?? 0) * (int) ($item->quantity ?? 1))), 2),
                    'status' => $item->status,
                    'guest_note' => $item->description ?: 'No note provided',
                ])->values()->all(),
            ];
        })->values();

        $requests = $groupedRequests;

        $requestData = $groupedRequests->map(function ($group) {
            $first = $group;
            $quantity = (int) ($group->quantity ?? 1);
            $unitPrice = (float) ($first->unit_price ?? 0.0);
            $subtotal = (float) ($first->subtotal ?? ($unitPrice * $quantity));
            $itemPrices = collect($first->items ?? [])
                ->pluck('unit_price')
                ->map(fn ($price) => (float) $price)
                ->unique()
                ->values();
            $unitPriceLabel = $itemPrices->count() > 1
                ? 'Varies'
                : '₱' . number_format($unitPrice, 2);
            $reservationKey = $first->reservation_key ?? ($first->reservation?->id ?? null);
            $statusLabel = match (strtolower((string) ($first->status ?? 'New'))) {
                'new' => 'Pending',
                'in progress' => 'In Progress',
                'delivered' => 'Delivered',
                'completed' => 'Completed',
                default => ucfirst((string) ($first->status ?? 'New')),
            };

            return [
                'id' => $first->id,
                'requestId' => 'REQ-' . str_pad($first->id, 4, '0', STR_PAD_LEFT),
                'guest' => $first->guest?->name ?? $first->reservation?->guest_name ?? 'Guest',
                'room' => $first->room?->room_number ?? '—',
                'reservation' => $first->reservation ? 'RES-' . str_pad($first->reservation->id, 4, '0', STR_PAD_LEFT) : ($reservationKey ? 'RES-' . str_pad((int) $reservationKey, 4, '0', STR_PAD_LEFT) : 'N/A'),
                'requestCategory' => $first->department ?? 'Housekeeping',
                'requestType' => $first->request_type,
                'description' => $first->description,
                'guestNote' => $first->description ?: 'No note provided',
                'specialRequest' => $first->description ?: 'No special request.',
                'quantity' => $quantity,
                'unitPrice' => $unitPrice,
                'unitPriceFormatted' => $unitPriceLabel,
                'subtotal' => $subtotal,
                'subtotalFormatted' => '₱' . number_format($subtotal, 2),
                'status' => $first->status ?? 'New',
                'statusLabel' => $statusLabel,
                'checkIn' => $first->reservation?->check_in ? $first->reservation->check_in->format('M d, Y') : '—',
                'checkOut' => $first->reservation?->check_out ? $first->reservation->check_out->format('M d, Y') : '—',
                'nights' => $first->reservation ? (($first->reservation->nights ?? '1') . ' Nights') : '—',
                'preferredTime' => $first->preferred_time ? date('g:i A', strtotime($first->preferred_time)) : 'Not specified',
                'estimatedArrivalTime' => $first->preferred_time ? date('g:i A', strtotime($first->preferred_time)) : '—',
                'submitted' => $first->submitted_at ? $first->submitted_at->format('M d, Y \a\t g:i A') : '—',
                'submittedShort' => $first->submitted_at ? $first->submitted_at->format('M d, Y') : '—',
                'priority' => $first->priority,
                'items' => $group->items ?? [[
                    'request_type' => $first->request_type,
                    'quantity' => $quantity,
                    'status' => $first->status ?? 'New',
                    'guest_note' => $first->description ?: 'No note provided',
                ]],
            ];
        })->values();

        $stats = [
            'pending' => $groupedRequests->whereIn('status', ['New', 'In Progress'])->count(),
            'resolved' => $groupedRequests->where('status', 'Completed')->count(),
            'total' => $groupedRequests->count(),
        ];

        return view('housekeeping.guest-requests', compact('requests', 'groupedRequests', 'requestData', 'stats'));
    }

    public function guestRequestDetails($id)
    {
        $request = GuestRequest::with(['guest', 'room', 'reservation'])
            ->where('department', 'Housekeeping')
            ->findOrFail($id);

        $statusLabel = match (strtolower((string) $request->status)) {
            'new' => 'Pending',
            'in progress' => 'In Progress',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            default => ucfirst((string) $request->status),
        };

        $unitPrice = (float) ($request->unit_price ?? 0);
        $quantity = (int) ($request->quantity ?? 1);
        $subtotal = (float) ($request->subtotal ?? ($unitPrice * $quantity));

        $requestData = [
            'id' => $request->id,
            'requestId' => 'REQ-' . str_pad($request->id, 4, '0', STR_PAD_LEFT),
            'reservation' => $request->reservation ? 'RES-' . str_pad($request->reservation->id, 4, '0', STR_PAD_LEFT) : ($request->reservation_key ? 'RES-' . str_pad((int) $request->reservation_key, 4, '0', STR_PAD_LEFT) : 'N/A'),
            'guest' => $request->guest?->name ?? $request->reservation?->guest_name ?? 'Guest',
            'room' => $request->room ? ($request->room->room_number ?: ($request->room->room_type ? $request->room->room_type : 'Room')) : ($request->room_id ? 'Room ' . $request->room_id : 'Room info unavailable'),
            'checkIn' => $request->reservation?->check_in ? $request->reservation->check_in->format('M d, Y') : '—',
            'checkOut' => $request->reservation?->check_out ? $request->reservation->check_out->format('M d, Y') : '—',
            'nights' => $request->reservation ? (($request->reservation->nights ?? '1') . ' Nights') : '—',
            'status' => $request->status,
            'statusLabel' => $statusLabel,
            'requestType' => $request->request_type,
            'requestCategory' => $request->department ?? 'Housekeeping',
            'description' => $request->description,
            'preferredTime' => $request->preferred_time ? date('g:i A', strtotime($request->preferred_time)) : 'Not specified',
            'priority' => $request->priority,
            'submitted' => $request->submitted_at ? $request->submitted_at->format('M d, Y \a\t g:i A') : '—',
            'submittedShort' => $request->submitted_at ? $request->submitted_at->format('M d, Y') : '—',
            'quantity' => $quantity,
            'unitPrice' => $unitPrice,
            'unitPriceFormatted' => '₱' . number_format($unitPrice, 2),
            'subtotal' => $subtotal,
            'subtotalFormatted' => '₱' . number_format($subtotal, 2),
            'guestNote' => $request->description ?: 'No note provided',
            'specialRequest' => $request->description ?: 'No special request.',
            'estimatedArrivalTime' => $request->preferred_time ? date('g:i A', strtotime($request->preferred_time)) : '—',
            'items' => [[
                'request_type' => $request->request_type,
                'quantity' => $quantity,
                'status' => $request->status,
                'guest_note' => $request->description ?: 'No note provided',
            ]],
        ];

        return view('housekeeping.guest-request-details', compact('request', 'requestData'));
    }

    public function updateGuestRequest(Request $request, $id)
    {
        $guestRequest = GuestRequest::findOrFail($id);

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:New,In Progress,Delivered,Completed',
        ]);

        if (isset($validated['notes'])) {
            $guestRequest->employee_notes = $validated['notes'];
        }

        if (isset($validated['status'])) {
            $guestRequest->status = $validated['status'];

            if ($validated['status'] === 'Completed' && $guestRequest->is_billable && $guestRequest->billing_status !== 'posted') {
                $reservation = $this->resolveReservationForGuestRequest($guestRequest);
                if ($reservation) {
                    $currentTotal = (float) ($reservation->total_amount ?? 0);
                    $reservation->update([
                        'total_amount' => $currentTotal + (float) $guestRequest->subtotal,
                    ]);
                    $guestRequest->billing_status = 'posted';
                    $guestRequest->billing_posted_at = now();
                }
            }
        }

        $guestRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Guest request updated successfully',
            'data' => $guestRequest,
        ]);
    }

    protected function resolveReservationForGuestRequest(GuestRequest $guestRequest)
    {
        if ($guestRequest->reservation_type === 'App\\Models\\RoomReservation' && $guestRequest->reservation_key) {
            return \App\Models\RoomReservation::find($guestRequest->reservation_key);
        }

        return \App\Models\Reservation::find($guestRequest->reservation_id)
            ?? \App\Models\RoomReservation::find($guestRequest->reservation_key);
    }

    public function markGuestRequestDelivered(Request $request, $id)
    {
        $guestRequest = GuestRequest::findOrFail($id);
        $guestRequest->status = 'Delivered';
        $guestRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Guest request marked as delivered',
            'data' => $guestRequest,
        ]);
    }

    public function maintenanceReport()
    {
        $reports = MaintenanceReport::latest('date_reported')->get();
        $rooms = Room::orderBy('room_number')->get();
        $reportCounts = [
            'total' => $reports->count(),
            'pending' => $reports->where('status', 'Pending')->count(),
            'repairing' => $reports->whereIn('status', ['Repairing', 'In Progress'])->count(),
            'completed' => $reports->where('status', 'Completed')->count(),
        ];

        return view('housekeeping.maintenance-report', compact('reports', 'rooms', 'reportCounts'));
    }

    public function storeMaintenanceReport(Request $request)
    {
        $validated = $request->validate([
            'room_number' => ['required', 'exists:rooms,room_number'],
            'category' => ['required', 'in:Electrical,Plumbing,Furniture,Air Conditioning,Bathroom,Other'],
            'priority' => ['required', 'in:Low,Medium,High,Urgent'],
            'description' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        $room = Room::where('room_number', $validated['room_number'])->firstOrFail();
        $validated['room_type'] = $room->room_type;
        $validated['problem'] = $validated['category'];
        $validated['reported_by'] = $request->user('web')->name;
        $validated['date_reported'] = now();
        $validated['technician'] = 'Unassigned';
        $validated['status'] = 'Pending';
        $validated['photo_path'] = $request->hasFile('photo')
            ? $request->file('photo')->store('maintenance-reports', 'public')
            : null;
        unset($validated['photo']);

        MaintenanceReport::create($validated);

        return redirect()->route('housekeeping.maintenance-report')
            ->with('success', 'Maintenance report submitted successfully.');
    }

    public function updateMaintenanceReport(Request $request, MaintenanceReport $maintenanceReport)
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:255'],
            'room_type' => ['required', 'string', 'max:255'],
            'reported_by' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'max:50'],
            'problem' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date_reported' => ['required', 'date'],
            'expected_date' => ['nullable', 'date', 'after_or_equal:date_reported'],
            'technician' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $validated['reported_by'] = $request->user('web')->name;

        $maintenanceReport->update($validated);

        return redirect()->route('housekeeping.maintenance-report')
            ->with('success', 'Maintenance report updated successfully.');
    }

    public function destroyMaintenanceReport(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->delete();

        return redirect()->route('housekeeping.maintenance-report')
            ->with('success', 'Maintenance report deleted successfully.');
    }

    public function cleaningHistory()
    {
        $tasks = HousekeepingTask::with(['room', 'assignedStaff', 'reservation'])
            ->where('status', 'completed')->latest('finished_at')->get();

        return view('housekeeping.cleaning-history', compact('tasks'));
    }

    public function messages()
    {
        $messages = Message::latest()->get();
        
        $stats = [
            'unread' => $messages->where('is_replied', false)->count(),
            'replied' => $messages->where('is_replied', true)->count(),
            'total' => $messages->count(),
        ];
        
        $dateFilter = 'today';

        return view('housekeeping.messages', compact('messages', 'stats', 'dateFilter'));
    }

    public function updateStatus(Request $request, $id)
    {
        $room = Room::find($id) ??
            Room::where('room_number', $id)->firstOrFail();

        $validated = $request->validate([
            'room_status' => ['nullable', 'in:VC,VD,OC,OD,OOO,BLO,NS,SO,HU,DND,VR,HSUC,HSD'],
            'cleaning_status' => ['nullable', 'in:clean,dirty,in_progress,ready,blocked,out_of_order'],
        ]);

        if (!empty($validated['room_status'])) {
            $room->update(self::roomStatusFields($validated['room_status']));
        } else {
            $room->update(['cleaning_status' => $validated['cleaning_status'] ?? 'clean']);
        }

        return back()->with(
            'success',
            "Room {$room->room_number} status updated."
        );
    }

    public static function roomStatusFields(string $status): array
    {
        $fields = [
            'VR' => ['status' => 'vr', 'cleaning_status' => 'ready'],
            'VC' => ['status' => 'vc', 'cleaning_status' => 'clean'],
            'VD' => ['status' => 'vd', 'cleaning_status' => 'dirty'],
            'OC' => ['status' => 'oc', 'cleaning_status' => 'clean'],
            'OD' => ['status' => 'od', 'cleaning_status' => 'dirty'],
            'OOO' => ['status' => 'ooo', 'cleaning_status' => 'out_of_order'],
            'BLO' => ['status' => 'blo', 'cleaning_status' => 'blocked'],
            'NS' => ['status' => 'ns', 'cleaning_status' => 'clean'],
            'SO' => ['status' => 'so', 'cleaning_status' => 'clean'],
            'HU' => ['status' => 'hu', 'cleaning_status' => 'clean'],
            'DND' => ['status' => 'dnd', 'cleaning_status' => 'clean'],
            'HSUC' => ['status' => 'hsuc', 'cleaning_status' => 'clean'],
            'HSD' => ['status' => 'hsd', 'cleaning_status' => 'dirty'],
        ];

        return $fields[$status] ?? $fields['VR'];
    }

    public static function roomStatusCode($room): string
    {
        $status = strtoupper(trim((string) ($room->status ?? '')));
        $cleaningStatus = strtolower(trim((string) ($room->cleaning_status ?? '')));

        if (in_array($status, ['VC', 'VD', 'OC', 'OD', 'OOO', 'BLO', 'NS', 'SO', 'HU', 'DND', 'VR', 'HSUC', 'HSD'], true)) {
            return $status;
        }

        if (in_array($status, ['MAINTENANCE', 'OUT_OF_ORDER'], true) || $cleaningStatus === 'out_of_order') {
            return 'OOO';
        }
        if (in_array($status, ['BLOCKED', 'UNAVAILABLE'], true) || $cleaningStatus === 'blocked') {
            return 'BLO';
        }
        if ($status === 'OCCUPIED') {
            return $cleaningStatus === 'dirty' ? 'OD' : 'OC';
        }
        if (in_array($status, ['AVAILABLE', 'VACANT', 'RESERVED'], true)) {
            return $cleaningStatus === 'dirty' ? 'VD' : 'VC';
        }

        return 'VR';
    }

    public static function employeeStatusLabel(string $status): string
    {
        return [
            'VC' => 'Available',
            'VD' => 'Dirty',
            'OC' => 'Occupied',
            'OD' => 'Occupied / Dirty',
            'OOO' => 'Maintenance',
            'BLO' => 'Unavailable',
            'NS' => 'No Show',
            'SO' => 'Occupied',
            'HU' => 'House Use',
            'DND' => 'Occupied',
            'VR' => 'Available',
            'HSUC' => 'House Use',
            'HSD' => 'House Use',
        ][$status] ?? 'Available';
    }

    public static function housekeepingStatusLabel(string $status): string
    {
        return [
            'VC' => 'Vacant Clean',
            'VD' => 'Vacant Dirty',
            'OC' => 'Occupied Clean',
            'OD' => 'Occupied Dirty',
            'OOO' => 'Out of Order',
            'BLO' => 'Blocked',
            'NS' => 'No Show',
            'SO' => 'Slept Out',
            'HU' => 'House Use',
            'DND' => 'Do Not Disturb',
            'VR' => 'Vacant Ready',
            'HSUC' => 'House Use Clean',
            'HSD' => 'House Use Dirty',
        ][$status] ?? 'Vacant Ready';
    }
}