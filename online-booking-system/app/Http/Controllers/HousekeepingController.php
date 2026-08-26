<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Staff;
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
        $tasks = HousekeepingTask::with(['room', 'assignedStaff', 'reservation'])
            ->where('status', 'completed')->latest('finished_at')->get();

        return view('housekeeping.cleaning-history', compact('tasks'));
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