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

        // === Monthly Revenue (Last 6 Months) ===
        $monthlyRevenue = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $revenue = Reservation::where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
            
            $monthlyLabels[] = $month->format('M');
            $monthlyRevenue[] = (float) $revenue;
        }

        // === Average Daily Revenue (Last 30 Days) ===
        $thirtyDaysAgo = now()->subDays(30);
        $last30DaysRevenue = Reservation::where('status', 'completed')
            ->where('created_at', '>=', $thirtyDaysAgo)
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
            'monthlyLabels',
            'monthlyRevenue',
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
            'cancelledReservations'
        ));
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
}
