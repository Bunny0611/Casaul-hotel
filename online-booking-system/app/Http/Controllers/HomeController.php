<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\InventoryItem;
use App\Models\Facility;
use App\Models\Event;
use App\Models\DiningMenu;
use App\Models\DiningReservationItem;
use App\Models\EventReservationDiningItem;
use App\Models\ReservationDiningItem;
use App\Models\DiningSchedule;
use App\Models\DiningTable;
use App\Models\Message;
use App\Models\Reservation;
use App\Models\RoomReservation;
use App\Models\EventReservation;
use App\Models\FacilityReservation;
use App\Models\DiningReservation;
use App\Models\GuestRequest;
use App\Support\ReservationPricing;
use App\Support\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    protected function featuredRooms(): array
    {
        $rooms = Room::query()
            ->where('status', 'available')
            ->orderBy('room_number')
            ->limit(5)
            ->get();

        if ($rooms->isNotEmpty()) {
            return $rooms
                ->map(function (Room $room) {
                    return [
                        'slug' => Str::slug($room->room_type ?? 'room'),
                        'name' => $room->room_type ?? 'Room',
                        'price' => '₱' . number_format((float) $room->price, 2),
                        'tagline' => 'Comfortable accommodation for a restful stay.',
                        'image' => $room->image ? 'images/' . $room->image : 'image/Royal-Suite-room.jpg',
                        'description' => $room->description ?? 'Enjoy a comfortable room with thoughtful facilities and a welcoming atmosphere.',
                        'features' => ['2 Guests', '1 Bed', 'Wi‑Fi', 'Air conditioning'],
                    ];
                })
                ->values()
                ->all();
        }

        return [
            [
                'slug' => 'deluxe-room',
                'name' => 'Deluxe Room',
                'price' => '₱3,500.00',
                'tagline' => 'Elegant comfort for a restful getaway.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'Our Deluxe Room pairs a warm, modern aesthetic with airy interiors, plush bedding, and a convenient layout designed for both relaxation and productivity.',
                'features' => ['King or twin beds', 'Private bath', 'High-speed Wi‑Fi', 'Room service'],
            ],
            [
                'slug' => 'executive-room',
                'name' => 'Executive Room',
                'price' => '₱6,500.00',
                'tagline' => 'Sophisticated luxury for work and leisure.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'The Executive Room is crafted for guests who want a more elevated experience, with generous space, refined finishes, and a tranquil atmosphere throughout the stay.',
                'features' => ['Executive lounge access', 'Large workspace', 'Premium facilities', 'City view'],
            ],
            [
                'slug' => 'presidential-room',
                'name' => 'Presidential Room',
                'price' => '₱12,000.00',
                'tagline' => 'A grand stay with a sense of occasion.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'Designed for memorable stays, the Presidential Room offers a luxurious ambiance, refined details, and spacious comfort that effortlessly balances elegance and practicality.',
                'features' => ['VIP service', 'Luxury furnishings', 'Premium toiletries', 'Private seating area'],
            ],
            [
                'slug' => 'standard-room',
                'name' => 'Standard Room',
                'price' => '₱2,800.00',
                'tagline' => 'Simple comfort with a polished finish.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'A well-appointed Standard Room brings together comfort and clarity, making it ideal for guests seeking a fresh, restful base in the heart of the city.',
                'features' => ['Complimentary breakfast', 'Air-conditioned', 'Smart TV', 'Daily housekeeping'],
            ],
            [
                'slug' => 'garden-suite',
                'name' => 'Garden Suite',
                'price' => '₱7,200.00',
                'tagline' => 'A serene hideaway with fresh air and modern comfort.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'The Garden Suite brings together elegance and tranquility, with generous living space and warm natural textures throughout the room.',
                'features' => ['Private veranda', 'Garden view', 'Balcony seating', 'Room service'],
            ],
        ];
    }

    public function index()
    {
        $rooms = $this->featuredRooms();
        $bestSellingDining = $this->bestSellingDining();

        return view('index', compact('rooms', 'bestSellingDining'));
    }

    private function bestSellingDining()
    {
        $menus = DiningMenu::query()
            ->whereIn('status', ['available', 'limited'])
            ->get()
            ->keyBy('id');

        $sales = collect();

        $sales = $sales->merge(
            DiningReservationItem::query()
                ->whereHas('diningReservation', fn ($query) => $query->whereNotIn('status', ['cancelled']))
                ->selectRaw('dining_id, SUM(quantity) as total_quantity')
                ->groupBy('dining_id')
                ->get()
        );

        $sales = $sales->merge(
            ReservationDiningItem::query()
                ->whereHas('reservation', fn ($query) => $query->whereNotIn('status', ['cancelled']))
                ->selectRaw('dining_id, SUM(quantity) as total_quantity')
                ->groupBy('dining_id')
                ->get()
        );

        $sales = $sales->merge(
            EventReservationDiningItem::query()
                ->whereHas('eventReservation', fn ($query) => $query->whereNotIn('status', ['cancelled']))
                ->selectRaw('dining_id, SUM(quantity) as total_quantity')
                ->groupBy('dining_id')
                ->get()
        );

        $salesByMenu = $sales
            ->groupBy('dining_id')
            ->map(fn ($items) => $items->sum('total_quantity'));

        return collect($this->diningCategoryOptions())
            ->mapWithKeys(function ($category) use ($menus, $salesByMenu) {
                $menu = $menus
                    ->filter(fn ($item) => $this->normalizeDiningCategory($item->category ?? 'Breakfast') === $category)
                    ->sortByDesc(fn ($item) => $salesByMenu->get($item->id, 0))
                    ->first();

                return [$category => $menu];
            })
            ->filter();
    }

    public function reservation()
    {
        $rooms = Room::query()
            ->orderByRaw('CAST(room_number AS UNSIGNED) ASC')
            ->orderBy('room_number')
            ->get();

        $facilities = Facility::whereIn('status', ['available', 'limited'])
            ->orderBy('name')
            ->get();

        $events = Event::whereIn('status', ['available', 'limited'])
            ->orderBy('name')
            ->get();

        $dining = DiningMenu::with('diningSchedule')
            ->whereIn('status', ['available', 'limited'])
            ->where(function ($query) {
                $query->whereNull('dining_schedule_id')
                    ->orWhereHas('diningSchedule', fn ($schedule) => $schedule->where('status', 'Active'));
            })
            ->orderBy('name')
            ->get();

        $diningByCategory = collect($this->diningCategoryOptions())->mapWithKeys(function ($category) use ($dining) {
            $items = $dining
                ->filter(fn ($meal) => $this->normalizeDiningCategory($meal->category ?? 'Breakfast') === $category)
                ->values();

            return [$category => $items];
        });

        $diningSchedules = DiningSchedule::where('status', 'Active')->orderBy('available_from')->get();
        $diningTables = DiningTable::whereIn('status', ['Available', 'Reserved'])->orderBy('table_no')->get();
        $diningReservations = DiningReservation::whereNotIn('status', ['cancelled', 'completed'])
            ->get(['dining_area', 'dining_schedule', 'check_in'])
            ->filter(function ($reservation) use ($diningSchedules) {
                $reservationDate = Carbon::parse($reservation->check_in)->startOfDay();
                $today = Carbon::today();

                if ($reservationDate->lt($today)) {
                    return false;
                }

                if (!$reservationDate->isToday()) {
                    return true;
                }

                $reservationSchedules = collect(explode(',', (string) $reservation->dining_schedule))
                    ->map(fn ($period) => trim($period))
                    ->filter();

                return $reservationSchedules->contains(function ($period) use ($diningSchedules) {
                    $schedule = $diningSchedules->firstWhere('period', $period);
                    return $schedule && Carbon::now()->lt(Carbon::parse($schedule->available_to));
                });
            })
            ->map(fn ($reservation) => [
                'dining_area' => $reservation->dining_area,
                'dining_schedule' => $reservation->dining_schedule,
                'check_in' => $reservation->check_in->format('Y-m-d'),
            ])
            ->values();

        return view('reservation', compact('rooms', 'facilities', 'events', 'dining', 'diningByCategory', 'diningSchedules', 'diningTables', 'diningReservations'));
    }

    public function roomAvailability(Request $request)
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        $rooms = Room::query()
            ->orderByRaw('CAST(room_number AS UNSIGNED) ASC')
            ->orderBy('room_number')
            ->get()
            ->map(function (Room $room) use ($validated) {
                $conflict = RoomAvailability::conflict($room, $validated['check_in'], $validated['check_out']);

                return [
                    'id' => $room->id,
                    'available' => $conflict === null,
                    'conflict' => $conflict ? [
                        'guest_name' => $conflict->guest_name,
                        'check_in' => $conflict->check_in?->format('Y-m-d'),
                        'check_out' => $conflict->check_out?->format('Y-m-d'),
                    ] : null,
                ];
            });

        return response()->json(['rooms' => $rooms]);
    }

    public function dining()
    {
        $dining = DiningMenu::with('diningSchedule')
            ->whereIn('status', ['available', 'limited'])
            ->where(function ($query) {
                $query->whereNull('dining_schedule_id')
                    ->orWhereHas('diningSchedule', fn ($schedule) => $schedule->where('status', 'Active'));
            })
            ->orderBy('name')
            ->get();

        $menuByCategory = collect($this->diningCategoryOptions())->mapWithKeys(function ($category) use ($dining) {
            $items = $dining->filter(fn ($meal) => $this->normalizeDiningCategory($meal->category ?? 'Breakfast') === $category)->values();
            return [$category => $items];
        });

        return view('dining', ['dining' => $dining, 'menuByCategory' => $menuByCategory, 'diningCategories' => $this->diningCategoryOptions()]);
    }

    public function diningMenuItems(Request $request)
    {
        $selectedCategory = $this->normalizeDiningCategory($request->query('category', 'Breakfast'));

        $items = DiningMenu::with('diningSchedule')
            ->whereIn('status', ['available', 'limited'])
            ->where(function ($query) {
                $query->whereNull('dining_schedule_id')
                    ->orWhereHas('diningSchedule', fn ($schedule) => $schedule->where('status', 'Active'));
            })
            ->orderBy('name')
            ->get()
            ->filter(fn ($meal) => $this->normalizeDiningCategory($meal->category ?? 'Breakfast') === $selectedCategory)
            ->values();

        return response()->json([
            'category' => $selectedCategory,
            'items' => $items->map(function ($meal) {
                $category = $this->normalizeDiningCategory($meal->category ?? 'Breakfast');
                $availableFrom = $meal->available_from ?: $meal->diningSchedule?->available_from;
                $availableTo = $meal->available_to ?: $meal->diningSchedule?->available_to;

                return [
                    'id' => $meal->id,
                    'name' => $meal->name,
                    'description' => $meal->description ?: 'A delicious option crafted for your stay.',
                    'category' => $category,
                    'price' => (float) $meal->price,
                    'image' => $meal->image && Storage::disk('public')->exists($meal->image)
                        ? asset('storage/' . $meal->image)
                        : asset('image/Royal-Suite-room.jpg'),
                    'available_from' => $availableFrom ? Carbon::parse($availableFrom)->format('H:i') : null,
                    'available_to' => $availableTo ? Carbon::parse($availableTo)->format('H:i') : null,
                    'schedule' => $meal->diningSchedule?->period,
                ];
            })->values(),
        ]);
    }

    private function diningCategoryOptions(): array
    {
        return ['Breakfast', 'Appetizer', 'Main Course', 'Soup', 'Salad', 'Dessert', 'Beverage'];
    }

    private function normalizeDiningCategory(?string $category): string
    {
        $value = strtolower(trim((string) ($category ?? '')));

        $map = [
            'breakfast' => 'Breakfast',
            'appetizer' => 'Appetizer',
            'appetisers' => 'Appetizer',
            'appetizers' => 'Appetizer',
            'main course' => 'Main Course',
            'main-course' => 'Main Course',
            'maincourse' => 'Main Course',
            'lunch' => 'Main Course',
            'dinner' => 'Main Course',
            'afternoon snacks' => 'Appetizer',
            'afternoon-snacks' => 'Appetizer',
            'snack' => 'Appetizer',
            'snacks' => 'Appetizer',
            'soup' => 'Soup',
            'salad' => 'Salad',
            'dessert' => 'Dessert',
            'beverage' => 'Beverage',
            'beverages' => 'Beverage',
            'drinks' => 'Beverage',
            'drink' => 'Beverage',
        ];

        return $map[$value] ?? ($value !== '' ? ucfirst($value) : 'Breakfast');
    }

    private function normalizeIdList($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            $ids = $value;
        } else {
            $ids = explode(',', (string) $value);
        }

        $normalized = collect($ids)
            ->map(fn ($id) => trim((string) $id))
            ->filter(fn ($id) => $id !== '' && $id !== 'null')
            ->unique()
            ->values()
            ->all();

        return $normalized ? implode(',', $normalized) : null;
    }

    private function normalizeDiningSelections(Request $request): array
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

        $request->merge([
            'facility_id' => $this->normalizeIdList($request->input('facility_id')),
            'event_id' => $this->normalizeIdList($request->input('event_id')),
            'dining_id' => empty($diningSelections) ? $this->normalizeIdList($request->input('dining_id')) : null,
            'category' => $request->input('category', 'rooms'),
        ]);

        // Determine reservation category based on what's selected (priority order matters)
        $eventId = $request->input('event_id');
        $facilityId = $request->input('facility_id');
        $hasDining = !empty($diningSelections) || !empty($request->input('dining_id')) || !empty($request->input('dining_area')) || !empty($request->input('dining_schedule'));

        if (!empty($eventId)) {
            $request->merge(['category' => 'event']);
        } elseif (!empty($facilityId)) {
            $request->merge(['category' => 'facilities']);
        } elseif ($hasDining) {
            $request->merge(['category' => 'dining']);
        } else {
            // Default to room if no facilities, events, or dining selected
            $request->merge(['category' => 'rooms']);
        }

        $validated = $request->validate([
            'category' => ['required', 'in:rooms,facilities,event,dining'],
            'room_id' => ['nullable', 'required_if:category,rooms', 'exists:rooms,id'],
            'check_in' => 'required|date|after_or_equal:today',
            'check_in_time' => 'nullable|date_format:H:i',
            'event_start_time' => ['exclude_unless:category,event', 'required', 'date_format:H:i'],
            'check_out' => ['required', 'date', 'after_or_equal:check_in'],
            'check_out_time' => 'nullable|date_format:H:i',
            'event_end_time' => ['exclude_unless:category,event', 'required', 'date_format:H:i'],
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => ['nullable', 'in:Cash / Pay at Hotel,GCash,Maya,Credit / Debit Card,Bank Transfer'],
            'payment_details' => ['nullable', 'string', 'max:2000'],
            'gcash_payment_proof' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'maya_payment_proof' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'bank_payment_proof' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'amount_paid' => ['nullable', 'numeric', 'min:0', 'lte:total_amount'],
            'special_requests' => 'nullable|string',
            'dining_id' => 'nullable|string',
            'dining_area' => 'nullable|string|max:100',
            'dining_schedule' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'duration_hours' => 'nullable|integer|min:1|max:24',
            'facility_id' => 'nullable|string',
            'facility_quantity' => 'nullable|integer|min:1',
            'event_id' => 'nullable|string',
            'event_type' => 'nullable|string|max:100',
            'number_of_guests' => 'nullable|integer|min:1',
            'room_number_of_guests' => 'nullable|integer|min:1',
            'adult_guests' => 'nullable|integer|min:0',
            'kid_guests' => 'nullable|integer|min:0',
            'submission_token' => 'nullable|string|max:100',
        ]);

        if (empty($validated['payment_method'])) {
            $validated['payment_method'] = 'Cash / Pay at Hotel';
        }

        if ($request->has('payment_details') && trim((string) $request->input('payment_details')) !== '') {
            $validated['payment_details'] = $request->input('payment_details');
        } else {
            $validated['payment_details'] = null;
        }

        $paymentDetails = (string) ($validated['payment_details'] ?? '');
        $paymentValidationMessage = match ($validated['payment_method']) {
            'GCash' => preg_match('/Number:\s*09\d{9}(?:\s*•|$)/', $paymentDetails)
                ? null
                : 'GCash mobile number must be exactly 11 digits and start with 09.',
            'Maya' => preg_match('/Number:\s*09\d{9}(?:\s*•|$)/', $paymentDetails)
                ? null
                : 'Maya mobile number must be exactly 11 digits and start with 09.',
            'Credit / Debit Card' => preg_match('/Expiration:\s*(0[1-9]|1[0-2])\/\d{2}(?:\s*•|$)/', $paymentDetails)
                ? null
                : 'Expiration date must use MM/YY.',
            'Bank Transfer' => preg_match('/Reference:\s*([^•]*)/', $paymentDetails, $referenceMatch)
                && mb_strlen(trim($referenceMatch[1])) <= 50
                ? null
                : 'Bank reference number must be 50 characters or fewer.',
            default => null,
        };

        if ($paymentValidationMessage) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'payment_details' => $paymentValidationMessage,
            ]);
        }

        if (!empty($validated['amount_paid'])) {
            $validated['amount_paid'] = (float) $validated['amount_paid'];
        } elseif (!empty($validated['payment_details'])) {
            preg_match('/(?:Amount\s*[:]|Amount\s*\|\s*)\s*([₱P]?)\s*([0-9]+(?:,[0-9]{3})*(?:\.\d{1,2})?|[0-9]+(?:\.\d{1,2})?)/i', (string) $validated['payment_details'], $matches);
            if (!empty($matches[2])) {
                $validated['amount_paid'] = (float) str_replace(',', '', $matches[2]);
            }
        }

        if ($validated['payment_method'] === 'Cash / Pay at Hotel') {
            $validated['amount_paid'] = 0;
        }

        if (($validated['category'] ?? null) === 'event') {
            $eventDate = Carbon::parse($validated['check_in'])->startOfDay();
            $minimumEventDate = Carbon::today()->addDay()->startOfDay();

            if ($eventDate->lt($minimumEventDate)) {
                throw ValidationException::withMessages([
                    'check_in' => 'Event reservations must be booked at least 1 day in advance. Same-day bookings are not allowed.',
                ]);
            }
        }

        $paymentProofFile = match ($validated['payment_method']) {
            'GCash' => $request->file('gcash_payment_proof'),
            'Maya' => $request->file('maya_payment_proof'),
            'Bank Transfer' => $request->file('bank_payment_proof'),
            default => null,
        };

        if ($paymentProofFile) {
            $paymentProofPath = $paymentProofFile->store('payment-proofs', 'public');
            $validated['payment_details'] = preg_replace('/\s*•\s*Proof:\s*[^•]*/i', '', (string) ($validated['payment_details'] ?? ''));
            $validated['payment_details'] = trim((string) $validated['payment_details']) . ' • Proof: ' . \Illuminate\Support\Facades\Storage::disk('public')->url($paymentProofPath);
        }

        if (!empty($validated['facility_id'])) {
            $facilityIds = collect(explode(',', $validated['facility_id']))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '')
                ->all();

            $invalidFacilityIds = collect($facilityIds)->filter(fn ($id) => !Facility::whereKey($id)->exists())->values()->all();
            abort_if(!empty($invalidFacilityIds), 422, 'One or more selected facilities are invalid.');

            $validated['facility_id'] = implode(',', $facilityIds);
            $selectedFacilityQuantity = (int) ($validated['facility_quantity'] ?? $validated['quantity'] ?? 1);
            foreach ($facilityIds as $facilityId) {
                $facility = Facility::find($facilityId);
                abort_if($facility?->capacity && $selectedFacilityQuantity > $facility->capacity, 422, 'The selected facility quantity exceeds its capacity.');
            }
        }

        if (!empty($validated['event_id'])) {
            $eventIds = collect(explode(',', $validated['event_id']))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '')
                ->all();

            $invalidEventIds = collect($eventIds)->filter(fn ($id) => !Event::whereKey($id)->exists())->values()->all();
            abort_if(!empty($invalidEventIds), 422, 'One or more selected event packages are invalid.');

            $validated['event_id'] = implode(',', $eventIds);
            foreach ($eventIds as $eventId) {
                $event = Event::find($eventId);
                abort_if($event?->capacity && !empty($validated['number_of_guests']) && $validated['number_of_guests'] > $event->capacity, 422, 'The selected guest count exceeds the package capacity.');

                if ($event) {
                    $start = Carbon::createFromFormat('H:i', $validated['event_start_time']);
                    $end = Carbon::createFromFormat('H:i', $validated['event_end_time']);
                    $configuredDuration = max(1, (int) ($event->duration_hours ?: 4));
                    $pricingBasis = strtolower(trim((string) $event->pricing_basis));

                    if ($pricingBasis === 'per person') {
                        $end = $start->copy()->addHours($configuredDuration);
                        $validated['event_end_time'] = $end->format('H:i');
                    }

                    abort_if($end->lessThanOrEqualTo($start), 422, 'The event end time must be after the start time.');
                    abort_if($event->available_from && $start->format('H:i') < Carbon::parse($event->available_from)->format('H:i'), 422, 'The event starts before its available time.');
                    abort_if($event->available_to && $end->format('H:i') > Carbon::parse($event->available_to)->format('H:i'), 422, 'The event ends after its available time.');

                    $submittedDuration = max(1, $start->diffInHours($end));
                    abort_if($pricingBasis === 'per hour' && $submittedDuration > $configuredDuration, 422, 'This event allows a maximum duration of '.$configuredDuration.' hours.');
                    $validated['duration_hours'] = $submittedDuration;
                }
            }
        }

        if (!empty($validated['dining_id'])) {
            $diningIds = collect(explode(',', $validated['dining_id']))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '')
                ->all();

            $invalidDiningIds = collect($diningIds)->filter(fn ($id) => !DiningMenu::whereKey($id)->exists())->values()->all();
            abort_if(!empty($invalidDiningIds), 422, 'One or more selected dining items are invalid.');
            $validated['dining_id'] = implode(',', $diningIds);
        }

        if ($guest = Auth::guard('guest')->user()) {
            $validated['guest_name'] = $guest->name;
            $validated['guest_email'] = $guest->email;
            $validated['guest_phone'] = filled($guest->contact_no)
                ? $guest->contact_no
                : ($validated['guest_phone'] ?? '');
        }

        // Some existing guest accounts do not have a saved contact number.
        // Keep the non-null reservation columns compatible with those accounts.
        $validated['guest_phone'] = (string) ($validated['guest_phone'] ?? '');

        $submissionToken = $validated['submission_token'] ?? null;
        if ($submissionToken && $request->session()->has('reservation_submission_' . $submissionToken)) {
            return redirect()->route('reservation');
        }

        if (empty($validated['check_out_time']) && !empty($validated['check_in_time'])) {
            $validated['check_out_time'] = $validated['check_in_time'];
        }

        $validated['status'] = 'pending';
        $validated['number_of_guests'] = max(1, (int) ($validated['number_of_guests'] ?? 1));
        $category = $validated['category'];

        $facilityIds = collect(explode(',', (string) ($validated['facility_id'] ?? '')))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();
        $eventIds = collect(explode(',', (string) ($validated['event_id'] ?? '')))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();
        $facilities = Facility::whereIn('id', $facilityIds)->get();
        $events = Event::whereIn('id', $eventIds)->get();
        $room = !empty($validated['room_id']) ? Room::findOrFail($validated['room_id']) : null;
        $roomGuestCount = (int) ($validated['room_number_of_guests'] ?? $validated['number_of_guests']);
        $roomTotal = $room
            ? ReservationPricing::room(
                $room,
                $validated['check_in'],
                $validated['check_out'],
                $roomGuestCount,
                isset($validated['adult_guests']) ? (int) $validated['adult_guests'] : null,
                isset($validated['kid_guests']) ? (int) $validated['kid_guests'] : null
            )
            : 0;
        $facilityTotal = ReservationPricing::facilities(
            $facilities,
            (int) ($validated['facility_quantity'] ?? $validated['quantity'] ?? 1),
            $validated['check_in'],
            $validated['check_out']
        );
        $eventDurationHours = 1;
        if (!empty($validated['event_start_time']) && !empty($validated['event_end_time'])) {
            $eventDurationHours = max(1, Carbon::parse($validated['event_start_time'])->diffInHours(Carbon::parse($validated['event_end_time'])));
        }
        $eventTotal = ReservationPricing::events($events, $validated['number_of_guests'], $eventDurationHours);
        $diningTotal = ReservationPricing::dining($diningSelections);
        $categoryTotal = match ($category) {
            'rooms' => $roomTotal,
            'facilities' => $facilityTotal,
            'event' => $eventTotal,
            'dining' => $diningTotal,
        };
        $validated['total_amount'] = $categoryTotal;

        if ($facilityIds->isNotEmpty()) {
            $validated['facility_id'] = $facilityIds->first();
        }
        if ($eventIds->isNotEmpty()) {
            $validated['event_id'] = $eventIds->first();
        }

        if ($category === 'rooms') {
            $validated['room_check_in_time'] = $validated['check_in_time'] ?? null;
            $validated['room_check_out_time'] = $validated['check_out_time'] ?? null;
            $reservation = DB::transaction(function () use ($validated) {
                $room = Room::query()->whereKey($validated['room_id'])->lockForUpdate()->firstOrFail();

                $hasConflict = RoomReservation::query()
                    ->where('room_id', $validated['room_id'])
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->whereDate('check_in', '<', $validated['check_out'])
                    ->whereDate('check_out', '>', $validated['check_in'])
                    ->exists();

                if (!$hasConflict) {
                    $hasConflict = Reservation::query()
                        ->where('room_id', $validated['room_id'])
                        ->whereNotIn('status', ['cancelled', 'completed'])
                        ->whereDate('check_in', '<', $validated['check_out'])
                        ->whereDate('check_out', '>', $validated['check_in'])
                        ->exists();
                }

                if ($hasConflict) {
                    throw ValidationException::withMessages([
                        'room_id' => 'Sorry, this room is no longer available for your selected dates. Please choose another room.',
                    ]);
                }

                return RoomReservation::create(collect($validated)->only([
                    'room_id', 'guest_name', 'guest_email', 'guest_phone', 'check_in',
                    'room_check_in_time', 'check_out', 'room_check_out_time',
                    'number_of_guests', 'status', 'total_amount', 'payment_method',
                    'payment_details', 'amount_paid', 'special_requests',
                ])->all());
            });
        } elseif ($category === 'event') {
            $validated['event_start_time'] = $validated['event_start_time'] ?? $validated['check_in_time'] ?? null;
            $validated['event_end_time'] = $validated['event_end_time'] ?? $validated['check_out_time'] ?? null;
            $reservation = EventReservation::create(collect($validated)->only([
                'event_id', 'guest_name', 'guest_email', 'guest_phone',
                'event_type', 'check_in', 'event_start_time', 'check_out',
                'event_end_time', 'duration_hours', 'number_of_guests', 'status', 'total_amount',
                'payment_method', 'payment_details', 'amount_paid', 'special_requests',
            ])->all());
        } elseif ($category === 'facilities') {
            $facility = $facilities->firstOrFail();
            $facilityQuantity = max(1, (int) ($validated['facility_quantity'] ?? 1));
            $pricingBasis = trim(strtolower((string) $facility->pricing_basis));
            if (in_array($pricingBasis, ['per vehicle', 'per stay + per vehicle'], true) && $facility->capacity && $facilityQuantity > $facility->capacity) {
                throw ValidationException::withMessages([
                    'facility_quantity' => 'The selected number of vehicles exceeds this facility\'s available capacity.',
                ]);
            }
            $durationHours = max(1, (int) ($validated['duration_hours'] ?? 1));
            $facilityStartTime = $validated['check_in_time'] ?? '00:00';
            $endTime = Carbon::createFromFormat('Y-m-d H:i', $validated['check_in'] . ' ' . $facilityStartTime)
                ->addHours($durationHours);
            $validated['check_out'] = $endTime->toDateString();
            $validated['facility_start_time'] = $facilityStartTime;
            $validated['facility_end_time'] = $endTime->format('H:i');
            $validated['facility_quantity'] = $facilityQuantity;
            $validated['total_amount'] = $facilityTotal;
            $reservation = FacilityReservation::create(collect($validated)->only([
                'facility_id', 'facility_quantity', 'guest_name', 'guest_email', 'guest_phone', 'check_in',
                'facility_start_time', 'check_out', 'facility_end_time',
                'number_of_guests', 'status', 'total_amount', 'payment_method',
                'payment_details', 'amount_paid', 'special_requests',
            ])->all());

            if (!empty($validated['room_id'])) {
                RoomReservation::create([
                    'room_id' => $validated['room_id'],
                    'guest_name' => $validated['guest_name'],
                    'guest_email' => $validated['guest_email'],
                    'guest_phone' => $validated['guest_phone'],
                    'check_in' => $validated['check_in'],
                    'room_check_in_time' => $validated['check_in_time'] ?? null,
                    'check_out' => $validated['check_out'],
                    'room_check_out_time' => $validated['check_out_time'] ?? null,
                    'number_of_guests' => $roomGuestCount,
                    'status' => 'pending',
                    'total_amount' => $roomTotal,
                    'payment_method' => $validated['payment_method'],
                    'payment_details' => $validated['payment_details'],
                    'amount_paid' => 0,
                    'special_requests' => $validated['special_requests'] ?? null,
                ]);
            }
        } else {
            $tableNumber = (string) ($validated['dining_area'] ?? '');
            $hasDiningConflict = DiningReservation::query()
                ->activeForTableAndSchedule($tableNumber, $validated['check_in'], $validated['dining_schedule'])
                ->exists();

            if ($hasDiningConflict) {
                throw ValidationException::withMessages([
                    'dining_area' => 'This table is already reserved for the selected dining schedule. Please choose another table or schedule.',
                ]);
            }

            $reservation = DiningReservation::create(collect($validated)->only([
                'guest_name', 'guest_email', 'guest_phone', 'dining_area',
                'dining_schedule', 'check_in', 'check_out', 'quantity',
                'dining_id', 'status', 'total_amount', 'payment_method',
                'payment_details', 'amount_paid', 'special_requests',
            ])->all());
        }

        if (!empty($diningSelections) && method_exists($reservation, 'diningItems')) {
            $reservation->diningItems()->createMany($diningSelections);
        }

        // A guest booking can contain several products. Keep each selection visible
        // in the employee/admin portals by recording it in its own reservation table.
        if ($category !== 'dining' && !empty($diningSelections)) {
            $diningReservation = DiningReservation::create([
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'],
                'dining_area' => $validated['dining_area'] ?? ($diningSelections[0]['dining_area'] ?? 'N/A'),
                'dining_schedule' => $validated['dining_schedule'] ?? ($diningSelections[0]['dining_schedule'] ?? 'N/A'),
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'quantity' => $validated['quantity'] ?? 1,
                'dining_id' => $validated['dining_id'] ?? null,
                'status' => 'pending',
                'total_amount' => $diningTotal,
                'payment_method' => $validated['payment_method'],
                'payment_details' => $validated['payment_details'],
                'amount_paid' => 0,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);
            $diningReservation->diningItems()->createMany($diningSelections);
        }

        if ($category !== 'rooms' && $category !== 'facilities' && !empty($validated['room_id'])) {
            RoomReservation::create([
                'room_id' => $validated['room_id'],
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'],
                'check_in' => $validated['check_in'],
                'room_check_in_time' => $validated['check_in_time'] ?? null,
                'check_out' => $validated['check_out'],
                'room_check_out_time' => $validated['check_out_time'] ?? null,
                'number_of_guests' => $roomGuestCount,
                'status' => 'pending',
                'total_amount' => $roomTotal,
                'payment_method' => $validated['payment_method'],
                'payment_details' => $validated['payment_details'],
                'amount_paid' => 0,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);
        }

        if ($category !== 'facilities' && !empty($validated['facility_id'])) {
            $facilityId = (int) collect(explode(',', (string) $validated['facility_id']))->filter()->first();
            $facility = Facility::find($facilityId);
            if ($facility) {
                $facilityQuantity = max(1, (int) ($validated['facility_quantity'] ?? $validated['quantity'] ?? 1));
                $facilityStartTime = $validated['check_in_time'] ?? '00:00';
                $facilityEndTime = Carbon::createFromFormat('Y-m-d H:i', $validated['check_in'] . ' ' . $facilityStartTime)
                    ->addHours(max(1, (int) ($validated['duration_hours'] ?? 1)));
                FacilityReservation::create([
                    'facility_id' => $facility->id,
                    'facility_quantity' => $facilityQuantity,
                    'guest_name' => $validated['guest_name'],
                    'guest_email' => $validated['guest_email'],
                    'guest_phone' => $validated['guest_phone'],
                    'check_in' => $validated['check_in'],
                    'facility_start_time' => $facilityStartTime,
                    'check_out' => $facilityEndTime->toDateString(),
                    'facility_end_time' => $facilityEndTime->format('H:i'),
                    'number_of_guests' => $validated['number_of_guests'],
                    'status' => 'pending',
                    'total_amount' => $facilityTotal,
                    'payment_method' => $validated['payment_method'],
                    'payment_details' => $validated['payment_details'],
                    'amount_paid' => 0,
                    'special_requests' => $validated['special_requests'] ?? null,
                ]);
            }
        }

        if ($submissionToken) {
            $request->session()->put('reservation_submission_' . $submissionToken, true);
        }

        return redirect()->route('reservation')->with('success', 'Your reservation request has been submitted. We will contact you soon.');
    }
    
    public function accommodation(Request $request)
    {
        $hasSearched = $request->boolean('search');
        $searchError = null;
        $searchCriteria = [];
        $roomsQuery = Room::query()->where('status', 'available');

        if ($hasSearched) {
            $validator = Validator::make($request->query(), [
                'check_in' => ['required', 'date', 'after_or_equal:today'],
                'check_out' => ['required', 'date', 'after:check_in'],
                'guests' => ['required', 'integer', 'min:1'],
                'room_type' => ['required', 'in:Deluxe Room,Standard Room'],
            ]);

            if ($validator->fails()) {
                $searchError = $validator->errors()->first();
                $rooms = collect();
            } else {
                $searchCriteria = $validator->validated();
                $rooms = $roomsQuery
                    ->where('room_type', $searchCriteria['room_type'])
                    ->where('capacity', '>=', $searchCriteria['guests'])
                    ->availableForDates($searchCriteria['check_in'], $searchCriteria['check_out'])
                    ->orderByRaw('CAST(room_number AS UNSIGNED) ASC')
                    ->orderBy('room_number')
                    ->get();
            }
        } else {
            $rooms = $roomsQuery
                ->orderByRaw('CAST(room_number AS UNSIGNED) ASC')
                ->orderBy('room_number')
                ->limit(5)
                ->get();
        }

        $checkInDate = $searchCriteria['check_in'] ?? now()->addDays(2)->toDateString();
        $checkOutDate = $searchCriteria['check_out'] ?? now()->addDays(4)->toDateString();
        $guestCount = (int) ($searchCriteria['guests'] ?? 2);
        $selectedRoomType = $searchCriteria['room_type'] ?? 'Deluxe Room';

        return view('accommodation', compact(
            'rooms',
            'hasSearched',
            'searchError',
            'checkInDate',
            'checkOutDate',
            'guestCount',
            'selectedRoomType'
        ));
    }

    public function profile()
    {
        abort_unless(Auth::guard('guest')->check(), 403);

        return view('profile');
    }

    public function records()
    {
        $guest = Auth::guard('guest')->user();
        abort_unless($guest, 403);

        $reservations = Reservation::with(['room', 'diningItems.diningMenu', 'payments'])
            ->where('guest_email', $guest->email)
            ->orderBy('created_at', 'desc')
            ->get();

        $categoryReservations = collect([
            RoomReservation::with(['room', 'payments'])
                ->where('guest_email', $guest->email)->get()
                ->each(fn ($reservation) => $reservation->category = 'rooms'),
            EventReservation::with(['event', 'diningItems.diningMenu', 'payments'])
                ->where('guest_email', $guest->email)->get()
                ->each(fn ($reservation) => $reservation->category = 'event'),
            FacilityReservation::with(['facility', 'payments'])
                ->where('guest_email', $guest->email)->get()
                ->each(fn ($reservation) => $reservation->category = 'facilities'),
            DiningReservation::with(['diningItems.diningMenu', 'payments'])
                ->where('guest_email', $guest->email)->get()
                ->each(fn ($reservation) => $reservation->category = 'dining'),
        ])->flatten(1)->map(function ($source) {
            $reservation = new Reservation();
            $reservation->forceFill($source->getAttributes());
            $reservation->setAttribute('category', $source->category);
            $reservation->setRelation('room', $source->relationLoaded('room') ? $source->getRelation('room') : null);
            $reservation->setRelation('facilities', $source->relationLoaded('facility') && $source->facility ? collect([$source->facility]) : collect());
            $reservation->setRelation('events', $source->relationLoaded('event') && $source->event ? collect([$source->event]) : collect());
            $reservation->setRelation('diningItems', $source->relationLoaded('diningItems') ? $source->getRelation('diningItems') : collect());
            $reservation->setRelation('payments', $source->relationLoaded('payments') ? $source->getRelation('payments') : collect());

            return $reservation;
        });

        $reservations = $reservations->concat($categoryReservations)
            ->sortByDesc('created_at')
            ->values();

        $reservations->each(function (Reservation $reservation) {
            $facilityIds = array_values(array_filter(array_map('trim', explode(',', (string) $reservation->facility_id))));
            $eventIds = array_values(array_filter(array_map('trim', explode(',', (string) $reservation->event_id))));

            $reservation->setRelation('facilities', Facility::whereIn('id', $facilityIds)->get());
            $reservation->setRelation('events', Event::whereIn('id', $eventIds)->get());
        });

        $activeReservation = $this->activeReservationFor($guest);
        $guestRequests = GuestRequest::with('room')
            ->where('guest_id', $guest->id)
            ->latest('submitted_at')
            ->get();

        $guestRequests = $guestRequests->groupBy(function (GuestRequest $guestRequest) {
            $room = $guestRequest->room_id ?? $guestRequest->room?->room_number ?? 'room';
            $submittedAt = $guestRequest->submitted_at?->toDateTimeString() ?? 'submitted-at';

            return $room . '|' . $submittedAt;
        })->map(function ($group) {
            $first = $group->first();
            $statuses = $group->pluck('status')->unique()->values();
            $priorities = $group->pluck('priority')->unique()->values();
            $statusOrder = ['New' => 1, 'In Progress' => 2, 'Delivered' => 3, 'Completed' => 4];
            $groupStatus = $statuses->sortBy(fn ($status) => $statusOrder[$status] ?? 0)->first();

            $first->setAttribute('group_count', $group->count());
            $first->setAttribute('group_request_id', 'REQ-' . str_pad($first->id, 4, '0', STR_PAD_LEFT));
            $first->setAttribute('group_status', $groupStatus ?: 'New');
            $first->setAttribute('group_priority', $priorities->count() === 1 ? $priorities->first() : 'Mixed');
            $first->setAttribute('group_items', $group->map(function (GuestRequest $item) {
                return [
                    'request_type' => $item->request_type,
                    'status' => $item->status,
                    'priority' => $item->priority,
                    'description' => $item->description,
                    'quantity' => (int) ($item->quantity ?? 1),
                    'unit_price' => (float) ($item->unit_price ?? 0),
                    'subtotal' => (float) ($item->subtotal ?? ((float) ($item->unit_price ?? 0) * (int) ($item->quantity ?? 1))),
                    'submitted_at' => $item->submitted_at?->format('M d, Y g:i A'),
                ];
            })->values()->all());

            return $first;
        })->values();

        return view('profile-records', compact('reservations', 'activeReservation', 'guestRequests'));
    }

    public function receipts()
    {
        $guest = Auth::guard('guest')->user();
        abort_unless($guest, 403);

        $receipts = collect([
            Reservation::with(['room', 'diningItems.diningMenu', 'payments'])
                ->where('guest_email', $guest->email)
                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                ->get(),
            RoomReservation::with(['room', 'payments'])
                ->where('guest_email', $guest->email)
                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                ->get()
                ->each(fn ($reservation) => $reservation->category = 'rooms'),
            EventReservation::with(['event', 'diningItems.diningMenu', 'payments'])
                ->where('guest_email', $guest->email)
                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                ->get()
                ->each(fn ($reservation) => $reservation->category = 'event'),
            FacilityReservation::with(['facility', 'payments'])
                ->where('guest_email', $guest->email)
                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                ->get()
                ->each(fn ($reservation) => $reservation->category = 'facilities'),
            DiningReservation::with(['diningItems.diningMenu', 'payments'])
                ->where('guest_email', $guest->email)
                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                ->get()
                ->each(fn ($reservation) => $reservation->category = 'dining'),
        ])->flatten(1)->map(function ($source) {
            $reservation = new Reservation();
            $reservation->forceFill($source->getAttributes());
            $reservation->setAttribute('category', $source->category ?? 'rooms');
            $reservation->setRelation('room', $source->relationLoaded('room') && $source->getRelation('room') ? $source->getRelation('room') : null);
            $reservation->setRelation('facilities', $source->relationLoaded('facility') && $source->facility ? collect([$source->facility]) : collect());
            $reservation->setRelation('events', $source->relationLoaded('event') && $source->event ? collect([$source->event]) : collect());
            $reservation->setRelation('diningItems', $source->relationLoaded('diningItems') ? $source->getRelation('diningItems') : collect());
            $reservation->setRelation('payments', $source->relationLoaded('payments') ? $source->getRelation('payments') : collect());

            return $reservation;
        })->sortByDesc('created_at')->values()->map(function (Reservation $reservation) {
            $matchingRoomReservations = RoomReservation::with('room')
                ->where('guest_email', $reservation->guest_email)
                ->whereDate('check_in', $reservation->check_in)
                ->whereDate('check_out', $reservation->check_out)
                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                ->get();
            $matchingFacilityReservations = FacilityReservation::with('facility')
                ->where('guest_email', $reservation->guest_email)
                ->whereDate('check_in', $reservation->check_in)
                ->whereDate('check_out', $reservation->check_out)
                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                ->get();
            $matchingEventReservations = EventReservation::with('event')
                ->where('guest_email', $reservation->guest_email)
                ->whereDate('check_in', $reservation->check_in)
                ->whereDate('check_out', $reservation->check_out)
                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                ->get();
            $matchingDiningReservations = DiningReservation::with('diningItems.diningMenu')
                ->where('guest_email', $reservation->guest_email)
                ->whereDate('check_in', $reservation->check_in)
                ->whereDate('check_out', $reservation->check_out)
                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                ->get();

            if ($matchingRoomReservations->isEmpty()
                && $matchingFacilityReservations->isEmpty()
                && $matchingEventReservations->isEmpty()
                && $matchingDiningReservations->isEmpty()) {
                return $reservation;
            }

            if (!$reservation->room && $matchingRoomReservations->first()?->room) {
                $reservation->setRelation('room', $matchingRoomReservations->first()->room);
            }

            $reservation->facility_id = $matchingFacilityReservations->pluck('facility_id')->filter()->implode(',');
            $reservation->event_id = $matchingEventReservations->pluck('event_id')->filter()->implode(',');
            $reservation->setRelation(
                'diningItems',
                $matchingDiningReservations->flatMap(fn ($diningReservation) => $diningReservation->diningItems)->values()
            );
            $reservation->total_amount = $matchingRoomReservations
                ->concat($matchingFacilityReservations)
                ->concat($matchingEventReservations)
                ->concat($matchingDiningReservations)
                ->sum(fn ($relatedReservation) => (float) $relatedReservation->total_amount);

            return $reservation;
        })->unique(function (Reservation $reservation) {
            return implode('|', [
                $reservation->guest_email,
                optional($reservation->check_in)->format('Y-m-d'),
                optional($reservation->check_out)->format('Y-m-d'),
            ]);
        })->values();

        return view('profile-receipts', compact('receipts'));
    }

    public function deleteReservation(Request $request, $reservation)
    {
        $guest = Auth::guard('guest')->user();
        abort_unless($guest, 403);

        $reservation = match ($request->input('category')) {
            'rooms' => RoomReservation::find($reservation),
            'event' => EventReservation::find($reservation),
            'facilities' => FacilityReservation::find($reservation),
            'dining' => DiningReservation::find($reservation),
            default => Reservation::find($reservation),
        };

        abort_if(!$reservation, 404, 'Reservation not found.');

        if ($reservation->guest_email !== $guest->email) {
            abort(403, 'You can only delete your own reservations.');
        }

        if (!in_array($reservation->status, ['cancelled', 'confirmed', 'checked-in', 'completed'], true)) {
            return redirect()->route('guest.records')->withErrors([
                'reservation' => 'Only cancelled, confirmed, checked-in, or checked-out reservations can be deleted.',
            ]);
        }

        if ($reservation->status === 'checked-in') {
            $reservation->loadMissing('room');
            $reservation->room?->update([
                'status' => 'available',
                'cleaning_status' => 'dirty',
            ]);
        }

        if (method_exists($reservation, 'diningItems')) {
            $reservation->diningItems()->delete();
        }
        $reservation->delete();

        return redirect()->route('guest.records')->with('success', 'Your reservation has been deleted.');
    }

    public function deleteGuestRequest(Request $request, GuestRequest $guestRequest)
    {
        $guest = Auth::guard('guest')->user();
        abort_unless($guest, 403);

        abort_unless($guestRequest->guest_id === $guest->id, 403, 'You can only delete your own requests.');

        $guestRequest->delete();

        return redirect()->route('guest.records')->with('success', 'Your request has been deleted.');
    }

    public function cancelReservation(Request $request, $reservation)
    {
        $guest = Auth::guard('guest')->user();
        abort_unless($guest, 403);

        $reservation = match ($request->input('category')) {
            'rooms' => RoomReservation::find($reservation),
            'event' => EventReservation::find($reservation),
            'facilities' => FacilityReservation::find($reservation),
            'dining' => DiningReservation::find($reservation),
            default => Reservation::find($reservation),
        };

        abort_if(!$reservation, 404, 'Reservation not found.');

        if ($reservation->guest_email !== $guest->email) {
            abort(403, 'You can only cancel your own reservations.');
        }

        if (in_array($reservation->status, ['cancelled', 'completed'], true)) {
            return redirect()->route('guest.records')->withErrors(['reservation' => 'This reservation cannot be cancelled.']);
        }

        DB::transaction(function () use ($reservation) {
            $wasCheckedIn = $reservation->status === 'checked-in';
            $totalPaid = $this->overallReservationPaid($reservation);
            $refundAmount = round(min(max($totalPaid, 0), max((float) ($reservation->total_amount ?? 0), 0)), 2);

            if ($refundAmount > 0) {
                $reservation->refunds()->create([
                    'guest_name' => $reservation->guest_name,
                    'original_total' => round((float) $reservation->total_amount, 2),
                    'final_total' => 0,
                    'total_paid' => round($totalPaid, 2),
                    'refund_amount' => $refundAmount,
                    'reason' => $wasCheckedIn ? 'Early Check-out' : 'Cancellation',
                    'refund_date' => now()->toDateString(),
                    'status' => 'Pending',
                ]);
            }

            $reservation->update(['status' => 'cancelled']);

            if ($wasCheckedIn) {
                $reservation->loadMissing('room');
                $reservation->room?->update([
                    'status' => 'available',
                    'cleaning_status' => 'dirty',
                ]);
            }
        });

        return redirect()->route('guest.records')->with('success', 'Your reservation has been cancelled successfully.');
    }

    private function overallReservationPaid($reservation): float
    {
        $relatedRows = collect([
            ...RoomReservation::with('payments')->get(),
            ...FacilityReservation::with('payments')->get(),
            ...EventReservation::with('payments')->get(),
            ...DiningReservation::with('payments')->get(),
            ...Reservation::with('payments')->get(),
        ])->filter(function ($row) use ($reservation) {
            return $row->guest_email === $reservation->guest_email
                && optional($row->check_in)->toDateString() === optional($reservation->check_in)->toDateString()
                && !in_array($row->status, ['cancelled', 'completed'], true);
        });

        $total = (float) $relatedRows->sum(fn ($row) => (float) ($row->total_amount ?? 0));
        $paid = max(
            (float) $relatedRows->max(fn ($row) => (float) ($row->amount_paid ?? 0)),
            (float) $relatedRows->sum(fn ($row) => (float) $row->payments->sum('amount'))
        );

        return round(min(max($paid, 0), max($total, 0)), 2);
    }

    public function storeGuestRequest(Request $request)
    {
        $guest = Auth::guard('guest')->user();
        abort_unless($guest, 403);

        $housekeepingTypes = [
            'Extra Towels', 'Extra Pillows', 'Extra Blanket', 'Toiletries',
            'Room Cleaning', 'Change Bedsheets', 'Other Housekeeping Request',
        ];
        $requestTypes = array_merge($housekeepingTypes, [
            'Broken Aircon', 'Broken TV', 'Broken Light', 'Plumbing/Water Problem',
            'Late Checkout', 'Early Check-in', 'Dining/Food Request',
            'Transportation Request', 'Other Request',
        ]);
        $billableRequestPrices = [
            'Extra Towels' => 80.00,
            'Extra Pillows' => 50.00,
            'Extra Blanket' => 120.00,
            'Toiletries' => 80.00,
            'Room Cleaning' => 200.00,
            'Change Bedsheets' => 150.00,
            'Other Housekeeping Request' => 100.00,
            'Dining/Food Request' => 250.00,
        ];

        $validated = $request->validate([
            'request_items' => ['required', 'json'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'in:Normal,Urgent'],
            'preferred_time' => ['nullable', 'date_format:H:i'],
        ]);

        $reservation = $this->activeReservationFor($guest);
        if (!$reservation) {
            return back()->withErrors(['request_type' => 'You need an active reservation to submit a guest request.'])->withInput();
        }

        $requestItems = json_decode($validated['request_items'], true);
        $requestItems = is_array($requestItems) ? $requestItems : [];

        $validItems = [];
        foreach ($requestItems as $item) {
            $type = trim((string) ($item['type'] ?? ''));
            $quantity = (int) ($item['quantity'] ?? 1);

            if ($type === '' || !in_array($type, $requestTypes, true) || $quantity < 1) {
                continue;
            }

            $unitPrice = (float) ($billableRequestPrices[$type] ?? 0.00);
            $subtotal = $unitPrice * $quantity;

            $validItems[] = [
                'request_type' => $type,
                'quantity' => $quantity,
                'department' => in_array($type, $housekeepingTypes, true) ? 'Housekeeping' : 'Employee',
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'is_billable' => $unitPrice > 0,
            ];
        }

        if (empty($validItems)) {
            return back()->withErrors(['request_type' => 'Please select at least one valid request type.'])->withInput();
        }

        $createdIds = [];
        foreach ($validItems as $item) {
            $legacyReservationId = $reservation->getAttribute('request_reservation_id');
            $reservationType = $reservation->getAttribute('reservation_type')
                ?? ($reservation instanceof \App\Models\RoomReservation ? 'App\\Models\\RoomReservation' : get_class($reservation));
            $reservationKey = $reservation->getAttribute('reservation_key') ?? $reservation->id;
            $requestReservationId = $reservationType === 'App\\Models\\RoomReservation' ? null : $legacyReservationId;

            $guestRequest = GuestRequest::create([
                'guest_id' => $guest->id,
                'reservation_id' => $requestReservationId,
                'room_id' => $reservation->room_id,
                'request_type' => $item['request_type'],
                'description' => $validated['description'],
                'department' => $item['department'],
                'priority' => $validated['priority'],
                'preferred_time' => $validated['preferred_time'] ?? null,
                'status' => 'New',
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
                'is_billable' => $item['is_billable'],
                'billing_status' => $item['is_billable'] ? 'pending' : 'not_required',
                'reservation_type' => $reservationType,
                'reservation_key' => $reservationKey,
                'submitted_at' => now(),
            ]);

            $createdIds[] = $guestRequest->id;
        }

        $firstRequestId = $createdIds[0] ?? null;

        return redirect()->route('guest.records')
            ->with('request_success', 'Your request has been submitted successfully.')
            ->with('request_id', $firstRequestId);
    }

    protected function activeReservationFor($guest): ?Reservation
    {
        $legacyReservation = Reservation::with('room')
            ->where('guest_email', $guest->email)
            ->whereIn('status', ['confirmed', 'checked-in'])
            ->whereDate('check_in', '<=', today())
            ->whereDate('check_out', '>=', today())
            ->latest('check_in')
            ->first();

        if ($legacyReservation) {
            $legacyReservation->setAttribute('request_reservation_id', $legacyReservation->id);
            return $legacyReservation;
        }

        $roomReservation = RoomReservation::with('room')
            ->where('guest_email', $guest->email)
            ->whereIn('status', ['confirmed', 'checked-in'])
            ->whereDate('check_in', '<=', today())
            ->whereDate('check_out', '>=', today())
            ->latest('check_in')
            ->first();

        if (!$roomReservation) {
            return null;
        }

        $activeReservation = new Reservation();
        $activeReservation->forceFill($roomReservation->getAttributes());
        $activeReservation->setAttribute('request_reservation_id', null);
        $activeReservation->setAttribute('reservation_type', 'App\\Models\\RoomReservation');
        $activeReservation->setAttribute('reservation_key', $roomReservation->id);
        $activeReservation->setRelation('room', $roomReservation->room);

        return $activeReservation;
    }

    public function roomDetail($slug)
    {
        $room = collect($this->featuredRooms())->firstWhere('slug', $slug);

        if (!$room) {
            $databaseRoom = Room::query()->get()->first(fn ($candidate) => Str::slug($candidate->room_type) === $slug);

            if ($databaseRoom) {
                $room = [
                    'name' => $databaseRoom->room_type,
                    'price' => '₱' . number_format((float) $databaseRoom->price, 2),
                    'tagline' => 'Comfortable accommodation for a restful stay.',
                    'image' => $databaseRoom->image ? 'images/' . $databaseRoom->image : 'image/Royal-Suite-room.jpg',
                    'description' => $databaseRoom->description ?? 'Enjoy a comfortable room with thoughtful facilities and a welcoming atmosphere.',
                    'features' => ['Comfortable bedding', 'Private bath', 'High-speed Wi-Fi', 'Air conditioning'],
                ];
            }
        }

        if (!$room) {
            abort(404);
        }

        return view('room-detail', compact('room'));
    }
    
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);
        
        Message::create([
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'message' => $validated['message'],
        ]);
        
        return redirect()->back()->with('success', 'Message sent successfully! We will get back to you soon.');
    }
}
