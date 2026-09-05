<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Message;
use App\Models\MaintenanceReport;
use App\Models\Staff;
use App\Models\InventoryItem;
use App\Models\Amenity;
use App\Models\EventPlace;
use App\Models\DiningTable;
use App\Models\DiningSchedule;
use App\Models\DiningMenu;
use App\Models\ReservationDiningItem;
use App\Models\GuestRequest;

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
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();
        $todayArrivals = Reservation::whereDate('check_in', today())->count();
        $todayDepartures = Reservation::whereDate('check_out', today())->count();
        $pendingRequests = Reservation::where('status', 'pending')->count();
        $occupancyRate = $totalRooms > 0 ? min(100, round(($occupiedRooms / $totalRooms) * 100)) : 0;

        $recentActivity = Reservation::with('room')
            ->latest()
            ->take(5)
            ->get()
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

    public function rooms()
    {
        $rooms = Room::orderBy('room_number')->paginate(5);
        $amenities = Amenity::orderBy('name')->paginate(5, ['*'], 'amenities_page')->appends(['tab' => 'amenities']);
        $eventPlaces = EventPlace::orderBy('name')->paginate(5, ['*'], 'event_places_page')->appends(['tab' => 'event-place']);
        $dining = DiningMenu::orderBy('name')->paginate(5, ['*'], 'dining_page')->appends(['tab' => 'dining']);
        $diningTables = DiningTable::orderBy('table_no')->get();
        $diningSchedules = DiningSchedule::orderBy('available_from')->get();
        $activeTab = request()->query('tab', 'rooms');

        if (!in_array($activeTab, ['rooms', 'amenities', 'event-place', 'dining'], true)) {
            $activeTab = 'rooms';
        }

        return view('admin.rooms', compact('rooms', 'amenities', 'eventPlaces', 'dining', 'diningTables', 'diningSchedules', 'activeTab'));
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
        $validated = $request->validate([
            'category' => ['required', 'in:amenities,event_place,dining'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'pricing_basis' => ['required_if:category,amenities,event_place', 'nullable', 'string', 'in:Per Stay,Per Person,Per Vehicle,Per Hour,Per Day,Fixed Price,Per Event'],
            'scheduling_requirement' => ['required_if:category,amenities', 'nullable', 'string', 'in:No Additional Schedule,Date Required,Date & Time Required'],
            'event_type' => ['required_if:category,event_place', 'nullable', 'string', 'in:Birthday,Wedding'],
            'status' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_to' => ['nullable', 'date_format:H:i'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);
        $validated['status'] = strtolower($validated['status']);

        $catalogModel = match ($validated['category']) {
            'amenities' => Amenity::class,
            'event_place' => EventPlace::class,
            default => DiningMenu::class,
        };
        if ($catalogModel::where('name', $validated['name'])->exists()) {
            return back()->withErrors(['name' => 'An item with this name already exists.'])->withInput();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleCatalogImageUpload($request);
        }

        match ($validated['category']) {
            'amenities' => Amenity::create([
                'name' => $validated['name'], 'description' => $validated['description'] ?? null,
                'price' => $validated['price'], 'status' => $validated['status'], 'image' => $validated['image'] ?? null,
                'pricing_basis' => $validated['pricing_basis'] ?? 'Per Stay', 'capacity' => $validated['capacity'] ?? null,
                'scheduling_requirement' => $validated['scheduling_requirement'] ?? 'No Additional Schedule',
            ]),
            'event_place' => EventPlace::create([
                'event_type' => $validated['event_type'], 'name' => $validated['name'], 'description' => $validated['description'] ?? null,
                'price' => $validated['price'], 'capacity' => $validated['capacity'] ?? null,
                'pricing_basis' => $validated['pricing_basis'] ?? 'Per Event', 'location' => $validated['location'] ?? null,
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
        $item = $category === 'amenities' ? Amenity::findOrFail($id) : ($category === 'event_place' ? EventPlace::findOrFail($id) : DiningMenu::findOrFail($id));
        $validated = $request->validate([
            'category' => ['required', 'in:amenities,event_place,dining'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'pricing_basis' => ['required_if:category,amenities,event_place', 'nullable', 'string', 'in:Per Stay,Per Person,Per Vehicle,Per Hour,Per Day,Fixed Price,Per Event'],
            'scheduling_requirement' => ['required_if:category,amenities', 'nullable', 'string', 'in:No Additional Schedule,Date Required,Date & Time Required'],
            'event_type' => ['required_if:category,event_place', 'nullable', 'string', 'in:Birthday,Wedding'],
            'status' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_to' => ['nullable', 'date_format:H:i'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);
        $validated['status'] = strtolower($validated['status']);

        $model = $category === 'amenities' ? Amenity::class : ($category === 'event_place' ? EventPlace::class : DiningMenu::class);
        if ($model::where('name', $validated['name'])->where('id', '!=', $item->id)->exists()) {
            return back()->withErrors(['name' => 'An item with this name already exists.'])->withInput();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleCatalogImageUpload($request, $item->image);
        }

        $item->update($category === 'amenities'
            ? ['name' => $validated['name'], 'description' => $validated['description'] ?? null, 'price' => $validated['price'], 'pricing_basis' => $validated['pricing_basis'] ?? 'Per Stay', 'capacity' => $validated['capacity'] ?? null, 'scheduling_requirement' => $validated['scheduling_requirement'] ?? 'No Additional Schedule', 'status' => $validated['status'], 'image' => $validated['image'] ?? $item->image]
            : ($category === 'event_place'
                ? ['event_type' => $validated['event_type'], 'name' => $validated['name'], 'description' => $validated['description'] ?? null, 'price' => $validated['price'], 'pricing_basis' => $validated['pricing_basis'] ?? 'Per Event', 'capacity' => $validated['capacity'] ?? null, 'location' => $validated['location'] ?? null, 'status' => $validated['status'], 'image' => $validated['image'] ?? $item->image]
                : $validated));

        return redirect()->route('admin.rooms')->with('success', 'Inventory item updated successfully.');
    }

    public function updateInventoryStatus(Request $request, $id)
    {
        $category = $request->input('category', 'dining');
        $item = $category === 'amenities' ? Amenity::findOrFail($id) : ($category === 'event_place' ? EventPlace::findOrFail($id) : DiningMenu::findOrFail($id));
        $validated = $request->validate(['status' => ['required', 'string', 'max:50']]);
        $item->update(['status' => strtolower($validated['status'])]);

        return redirect()->route('admin.rooms')->with('success', 'Inventory status updated successfully.');
    }

    public function destroyInventoryItem($id)
    {
        $category = request()->input('category', 'dining');
        $item = $category === 'amenities' ? Amenity::findOrFail($id) : ($category === 'event_place' ? EventPlace::findOrFail($id) : DiningMenu::findOrFail($id));
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

        if (!count($inventoryIds) || !in_array($category, ['amenities', 'event_place', 'dining'], true)) {
            return redirect()->route('admin.rooms')->with('success', 'No inventory items selected for deletion.');
        }

        $model = $category === 'amenities' ? Amenity::class : ($category === 'event_place' ? EventPlace::class : DiningMenu::class);
        $deleted = $model::whereIn('id', $inventoryIds)->delete();

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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
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
            'price' => 'required|numeric|min:0',
            'floor' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'status' => 'required|in:available,occupied,reserved,maintenance',
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
        $allReservations = Reservation::with('room', 'amenity', 'eventPlace', 'diningMenu')->latest()->get();
        
        // Separate reservations by category with fallback checks (like employee view)
        // This handles both old data (using room_id, amenity_id fields) and new data (using category field)
        $roomReservations = $allReservations->filter(function ($reservation) {
            return $reservation->room_id || $reservation->category === 'room' || $reservation->category === 'rooms';
        })->values();
        
        $amenityReservations = $allReservations->filter(function ($reservation) {
            return $reservation->amenity_id || $reservation->category === 'amenity' || $reservation->category === 'amenities';
        })->values();
        
        $eventPlaceReservations = $allReservations->filter(function ($reservation) {
            return $reservation->event_place_id || $reservation->category === 'event_place';
        })->values();
        
        $diningReservations = $allReservations->filter(function ($reservation) {
            return $reservation->category === 'dining'
                || !empty($reservation->dining_id)
                || !empty($reservation->dining_area)
                || !empty($reservation->dining_schedule)
                || $reservation->diningItems()->exists();
        })->values();
        
        $rooms = Room::orderBy('room_number')->get();
        $inventoryItems = InventoryItem::orderBy('name')->get();
        $amenities = Amenity::orderBy('name')->get();
        $eventPlaces = EventPlace::orderBy('name')->get();
        $diningMenus = DiningMenu::orderBy('name')->get();
        $diningSchedules = DiningSchedule::orderBy('available_from')->get();
        $diningTables = DiningTable::orderBy('table_no')->get();

        // For backward compatibility, keep the old variable name
        $reservations = $roomReservations;

        return request()->routeIs('employee.reservation')
            ? view('employee.reservation', compact('reservations', 'roomReservations', 'amenityReservations', 'eventPlaceReservations', 'diningReservations', 'rooms', 'inventoryItems', 'amenities', 'eventPlaces', 'diningMenus', 'diningSchedules', 'diningTables'))
            : view('admin.reservations', compact('reservations', 'roomReservations', 'amenityReservations', 'eventPlaceReservations', 'diningReservations', 'rooms', 'inventoryItems', 'amenities', 'eventPlaces', 'diningMenus', 'diningSchedules'));
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
            'category' => ['required', 'in:rooms,amenities,event_place,dining'],
            'room_id' => ['nullable', 'required_if:category,rooms', 'exists:rooms,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'event_type' => ['nullable', 'required_if:category,event_place', 'string', 'max:100'],
            'number_of_guests' => ['nullable', 'required_if:category,event_place', 'integer', 'min:1'],
            'dining_area' => ['nullable', 'required_if:category,dining', 'string', 'max:100'],
            'dining_schedule' => ['nullable', 'required_if:category,dining', 'in:Breakfast,Lunch,Dinner'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'check_in' => ['required', 'date'],
            'check_in_time' => ['nullable', 'required_if:category,amenities', 'date_format:H:i'],
            'check_out' => ['required', 'date', 'after_or_equal:check_in'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:Cash / Pay at Hotel,GCash,Maya,Credit / Debit Card,Bank Transfer'],
            'payment_details' => ['nullable', 'string', 'max:2000'],
            'amount_paid' => ['nullable', 'numeric', 'min:0', 'lte:total_amount'],
            'amenity_id' => ['nullable', 'required_if:category,amenities', 'exists:amenities,id'],
            'event_place_id' => ['nullable', 'required_if:category,event_place', 'exists:event_places,id'],
            'dining_id' => ['nullable', 'string'],
            'duration_hours' => ['nullable', 'required_if:category,amenities', 'integer', 'min:1', 'max:24'],
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

        if ($validated['category'] === 'amenities') {
            $amenity = Amenity::findOrFail($validated['amenity_id']);
            $endTime = Carbon::createFromFormat('Y-m-d H:i', $validated['check_in'] . ' ' . $validated['check_in_time'])
                ->addHours((int) $validated['duration_hours']);
            $validated['check_out'] = $endTime->toDateString();
            $validated['check_out_time'] = $endTime->format('H:i');
            $validated['amenity_start_time'] = $validated['check_in_time'] ?? null;
            $validated['amenity_end_time'] = $endTime->format('H:i');
            $validated['total_amount'] = (float) $amenity->price * (int) $validated['duration_hours'];
            unset($validated['duration_hours']);
        }

        if (($validated['category'] ?? null) === 'event_place') {
            $validated['event_start_time'] = $validated['check_in_time'] ?? null;
            $validated['event_end_time'] = $validated['check_out_time'] ?? null;
        }

        if (($validated['category'] ?? null) === 'dining') {
            $validated['dining_time'] = $validated['dining_schedule'] ?? $validated['check_in_time'] ?? null;
        }

        if (($validated['category'] ?? null) === 'rooms') {
            $validated['room_check_in_time'] = $validated['check_in_time'] ?? null;
            $validated['room_check_out_time'] = $validated['check_out_time'] ?? null;
        }

        $reservation = Reservation::create($validated);

        if (!empty($diningSelections)) {
            $reservation->diningItems()->createMany($diningSelections);
        }

        return $isEmployeeReservation
            ? redirect()->route('employee.reservation')->with('success', 'Reservation created successfully!')
            : redirect()->route('admin.reservations')->with('success', 'Reservation created successfully!');
    }

    public function updateReservationStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,checked-in,cancelled,completed'],
        ]);

        DB::transaction(function () use ($id, $validated) {
            $reservation = Reservation::with('room')->lockForUpdate()->findOrFail($id);
            if ($validated['status'] === 'completed') {
                $paid = (float) $reservation->payments()->sum('amount');
                if (round((float) $reservation->total_amount - $paid, 2) > 0) {
                    abort(422, 'The reservation must be paid in full before checkout.');
                }
            }
            $reservation->update(['status' => $validated['status']]);

            if (!$reservation->room) {
                return;
            }

            if ($validated['status'] === 'checked-in') {
                $reservation->room->update([
                    'status' => 'occupied',
                    'cleaning_status' => 'clean',
                ]);
            } elseif ($validated['status'] === 'completed') {
                $reservation->room->update([
                    'status' => 'available',
                    'cleaning_status' => 'dirty',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Reservation status updated successfully!');
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
            $reservation = Reservation::lockForUpdate()->findOrFail($id);
            $paid = (float) $reservation->payments()->sum('amount');
            $balance = round((float) $reservation->total_amount - $paid, 2);
            if ((float) $validated['amount'] > $balance) {
                abort(422, 'Payment amount cannot exceed the balance due.');
            }

            $payment = $reservation->payments()->create([
                ...$validated,
                'recorded_by' => $request->user()->id,
            ]);
            $newPaid = round($paid + (float) $payment->amount, 2);
            $newBalance = max(round((float) $reservation->total_amount - $newPaid, 2), 0);
            $reservation->update([
                'amount_paid' => $newPaid,
            ]);

            return [
                'total' => (float) $reservation->total_amount,
                'paid' => $newPaid,
                'balance' => $newBalance,
                'status' => $newBalance === 0.0 ? 'Paid' : 'Partially Paid',
            ];
        });

        return response()->json($result);
    }

    public function updateReservation(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
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
            'category' => ['required', 'in:rooms,amenities,event_place,dining'],
            'room_id' => ['nullable', 'required_if:category,rooms', 'exists:rooms,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'event_type' => ['nullable', 'required_if:category,event_place', 'string', 'max:100'],
            'number_of_guests' => ['nullable', 'required_if:category,event_place', 'integer', 'min:1'],
            'dining_area' => ['nullable', 'required_if:category,dining', 'string', 'max:100'],
            'dining_schedule' => ['nullable', 'required_if:category,dining', 'in:Breakfast,Lunch,Dinner'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'check_in' => ['required', 'date'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out' => ['required', 'date', 'after_or_equal:check_in'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:Cash / Pay at Hotel,GCash,Maya,Credit / Debit Card,Bank Transfer'],
            'payment_details' => ['nullable', 'string', 'max:2000'],
            'amount_paid' => ['nullable', 'numeric', 'min:0', 'lte:total_amount'],
            'special_requests' => ['nullable', 'string'],
            'dining_id' => ['nullable', 'string'],
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

        $reservation->update($validated);

        if (!empty($diningSelections)) {
            $reservation->diningItems()->delete();
            $reservation->diningItems()->createMany($diningSelections);
        }

        return $request->routeIs('employee.reservations.update')
            ? redirect()->route('employee.reservation')->with('success', 'Reservation updated successfully!')
            : redirect()->route('admin.reservations')->with('success', 'Reservation updated successfully!');
    }

    public function destroyReservation(Request $request, $id)
    {
        Reservation::findOrFail($id)->delete();

        return $request->routeIs('employee.reservations.destroy')
            ? redirect()->route('employee.reservation')->with('success', 'Reservation deleted successfully!')
            : redirect()->route('admin.reservations')->with('success', 'Reservation deleted successfully!');
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
        $requestQuery = GuestRequest::with(['guest', 'reservation.room', 'room', 'assignedEmployee'])
            ->where('department', 'Employee')
            ->latest('submitted_at');
        $requests = $requestQuery->paginate(5)->withQueryString();
        $allRequests = (clone $requestQuery)->get();
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

        $monthlyRevenue = Reservation::where('status', 'completed')
            ->when($from, fn ($query) => $query->whereDate('updated_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('updated_at', '<=', $to))
            ->orderBy('updated_at')
            ->get(['updated_at', 'total_amount'])
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
