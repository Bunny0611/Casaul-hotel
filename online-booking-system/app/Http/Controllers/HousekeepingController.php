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
}

