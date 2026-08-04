<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

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

        return view('housekeeping.dashboard', compact(
            'rooms',
            'totalRooms',
            'cleanRooms',
            'dirtyRooms',
            'inProgress',
            'occupiedRooms'
        ));
    }


    public function assignedRooms()
    {
        $rooms = Room::orderBy('room_number')->get();

        return view('housekeeping.assigned-rooms', compact('rooms'));
    }


    public function roomStatusUpdate()
    {
        $rooms = Room::orderBy('room_number')->get();

        return view('housekeeping.room-status-update', compact('rooms'));
    }


    public function guestRequests()
    {
        return view('housekeeping.guest-requests');
    }


    public function maintenanceReport()
    {
        return view('housekeeping.maintenance-report');
    }


    public function cleaningHistory()
    {
        return view('housekeeping.cleaning-history');
    }


    public function updateStatus(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'cleaning_status' => ['required', 'in:clean,dirty,in_progress'],
        ]);

        $room->update([
            'cleaning_status' => $validated['cleaning_status']
        ]);

        return back()->with(
            'success',
            "Room {$room->room_number} marked as " .
            str_replace('_', ' ', $validated['cleaning_status']) . '.'
        );
    }
}