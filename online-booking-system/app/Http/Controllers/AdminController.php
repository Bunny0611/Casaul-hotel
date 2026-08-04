<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Message;

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
        $rooms = Room::all();
        return view('admin.rooms', compact('rooms'));
    }

    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms',
            'room_type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'floor' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

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

    public function reports()
    {
        $reservations = Reservation::with('room')->latest()->get();
        $completedReservations = $reservations->where('status', 'completed');

        $totalRevenue = (float) $completedReservations->sum('total_amount');
        $averagePayment = $completedReservations->count() > 0 ? (float) $completedReservations->avg('total_amount') : 0;
        $highestPayment = $completedReservations->count() > 0 ? (float) $completedReservations->max('total_amount') : 0;
        $lowestPayment = $completedReservations->count() > 0 ? (float) $completedReservations->min('total_amount') : 0;

        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();

        $pendingReservations = $reservations->where('status', 'pending')->count();
        $confirmedReservations = $reservations->where('status', 'confirmed')->count();
        $completedCount = $completedReservations->count();
        $cancelledReservations = $reservations->where('status', 'cancelled')->count();

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

        return view('admin.reports', compact(
            'totalRevenue',
            'averagePayment',
            'highestPayment',
            'lowestPayment',
            'reservations',
            'availableRooms',
            'occupiedRooms',
            'maintenanceRooms',
            'pendingReservations',
            'confirmedReservations',
            'completedCount',
            'cancelledReservations',
            'monthlyLabels',
            'monthlyRevenue'
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
