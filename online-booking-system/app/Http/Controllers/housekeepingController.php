 <?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class HousekeepingController extends Controller
{
    /**
     * Show the housekeeping dashboard with room cleaning statuses.
     */
    public function dashboard()
    {
        $rooms = Room::orderBy('room_number')->get();

        // Stats
        $totalRooms = $rooms->count();
        $cleanRooms = $rooms->where('cleaning_status', 'clean')->count();
        $dirtyRooms = $rooms->where('cleaning_status', 'dirty')->count();
        $inProgress = $rooms->where('cleaning_status', 'in_progress')->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();

        return view('housekeeping.dashboard', compact(
            'rooms',
            'totalRooms',
            'cleanRooms',
            'dirtyRooms',
            'inProgress',
            'occupiedRooms'
        ));
    }

    /**
     * Update the cleaning status of a room.
     */
    public function updateStatus(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'cleaning_status' => ['required', 'in:clean,dirty,in_progress'],
        ]);

        $room->update(['cleaning_status' => $validated['cleaning_status']]);

        return back()->with('success', "Room {$room->room_number} marked as " . str_replace('_', ' ', $validated['cleaning_status']) . '.');
    }

    /**
     * Show assigned rooms for housekeeping.
     */
    public function assignedRooms()
    {
        return view('housekeeping.assigned-rooms');
    }

    /**
     * Show the room status update page.
     */
    public function roomStatusUpdate()
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('housekeeping.room-status-update', compact('rooms'));
    }

    /**
     * Show guest requests for housekeeping.
     */
    public function guestRequests()
    {
        return view('housekeeping.guest-requests');
    }

    /**
     * Show the maintenance report page.
     */
    public function maintenanceReport()
    {
        return view('housekeeping.maintenance-report');
    }

    /**
     * Show the cleaning history page.
     */
    public function cleaningHistory()
    {
        return view('housekeeping.cleaning-history');
    }
}

