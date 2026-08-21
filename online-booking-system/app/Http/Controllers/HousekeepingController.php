<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\MaintenanceReport;

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
        $reports = MaintenanceReport::latest('date_reported')->get();

        return view('housekeeping.maintenance-report', compact('reports'));
    }

    public function storeMaintenanceReport(Request $request)
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
        return view('housekeeping.cleaning-history');
    }

    public function updateStatus(Request $request, $id)
    {
        $room = Room::find($id) ??
            Room::where('room_number', $id)->firstOrFail();

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