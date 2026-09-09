<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\InventoryItem;
use App\Models\Amenity;
use App\Models\EventPlace;
use App\Models\DiningMenu;
use App\Models\DiningSchedule;
use App\Models\DiningTable;
use App\Models\Message;
use App\Models\Reservation;
use App\Models\RoomReservation;
use App\Models\EventReservation;
use App\Models\AmenityReservation;
use App\Models\DiningReservation;
use App\Models\GuestRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    protected function featuredRooms(): array
    {
        return [
            [
                'slug' => 'deluxe-room',
                'name' => 'Deluxe Room',
                'price' => '₱3,500',
                'tagline' => 'Elegant comfort for a restful getaway.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'Our Deluxe Room pairs a warm, modern aesthetic with airy interiors, plush bedding, and a convenient layout designed for both relaxation and productivity.',
                'features' => ['King or twin beds', 'Private bath', 'High-speed Wi‑Fi', 'Room service'],
            ],
            [
                'slug' => 'executive-room',
                'name' => 'Executive Room',
                'price' => '₱6,500',
                'tagline' => 'Sophisticated luxury for work and leisure.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'The Executive Room is crafted for guests who want a more elevated experience, with generous space, refined finishes, and a tranquil atmosphere throughout the stay.',
                'features' => ['Executive lounge access', 'Large workspace', 'Premium amenities', 'City view'],
            ],
            [
                'slug' => 'presidential-room',
                'name' => 'Presidential Room',
                'price' => '₱12,000',
                'tagline' => 'A grand stay with a sense of occasion.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'Designed for memorable stays, the Presidential Room offers a luxurious ambiance, refined details, and spacious comfort that effortlessly balances elegance and practicality.',
                'features' => ['VIP service', 'Luxury furnishings', 'Premium toiletries', 'Private seating area'],
            ],
            [
                'slug' => 'standard-room',
                'name' => 'Standard Room',
                'price' => '₱2,800',
                'tagline' => 'Simple comfort with a polished finish.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'A well-appointed Standard Room brings together comfort and clarity, making it ideal for guests seeking a fresh, restful base in the heart of the city.',
                'features' => ['Complimentary breakfast', 'Air-conditioned', 'Smart TV', 'Daily housekeeping'],
            ],
        ];
    }

    public function index()
    {
        $rooms = $this->featuredRooms();
        return view('index', compact('rooms'));
    }

    public function reservation()
    {
        $rooms = Room::where('status', 'available')->get();

        $amenities = Amenity::whereIn('status', ['available', 'limited'])
            ->orderBy('name')
            ->get();

        $events = EventPlace::whereIn('status', ['available', 'limited'])
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

        return view('reservation', compact('rooms', 'amenities', 'events', 'dining', 'diningSchedules', 'diningTables', 'diningReservations'));
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
            'amenity_id' => $this->normalizeIdList($request->input('amenity_id')),
            'event_place_id' => $this->normalizeIdList($request->input('event_place_id')),
            'dining_id' => empty($diningSelections) ? $this->normalizeIdList($request->input('dining_id')) : null,
            'category' => $request->input('category', 'rooms'),
        ]);

        // Determine reservation category based on what's selected (priority order matters)
        $eventPlaceId = $request->input('event_place_id');
        $amenityId = $request->input('amenity_id');
        $hasDining = !empty($diningSelections) || !empty($request->input('dining_id')) || !empty($request->input('dining_area')) || !empty($request->input('dining_schedule'));

        if (!empty($eventPlaceId)) {
            $request->merge(['category' => 'event_place']);
        } elseif (!empty($amenityId)) {
            $request->merge(['category' => 'amenities']);
        } elseif ($hasDining) {
            $request->merge(['category' => 'dining']);
        } else {
            // Default to room if no amenities, events, or dining selected
            $request->merge(['category' => 'rooms']);
        }

        $validated = $request->validate([
            'category' => ['required', 'in:rooms,amenities,event_place,dining'],
            'room_id' => ['nullable', 'required_if:category,rooms', 'exists:rooms,id'],
            'check_in' => 'required|date|after_or_equal:today',
            'check_in_time' => 'nullable|date_format:H:i',
            'event_start_time' => 'nullable|date_format:H:i',
            'check_out' => ['required', 'date', 'after_or_equal:check_in'],
            'check_out_time' => 'nullable|date_format:H:i',
            'event_end_time' => 'nullable|date_format:H:i',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => ['nullable', 'in:Cash / Pay at Hotel,GCash,Maya,Credit / Debit Card,Bank Transfer'],
            'payment_details' => ['nullable', 'string', 'max:2000'],
            'amount_paid' => ['nullable', 'numeric', 'min:0', 'lte:total_amount'],
            'special_requests' => 'nullable|string',
            'dining_id' => 'nullable|string',
            'dining_area' => 'nullable|string|max:100',
            'dining_schedule' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:1',
            'duration_hours' => 'nullable|integer|min:1',
            'amenity_id' => 'nullable|string',
            'amenity_quantity' => 'nullable|integer|min:1',
            'event_place_id' => 'nullable|string',
            'event_type' => 'nullable|string|max:100',
            'number_of_guests' => 'nullable|integer|min:1',
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

        if (!empty($validated['amenity_id'])) {
            $amenityIds = collect(explode(',', $validated['amenity_id']))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '')
                ->all();

            $invalidAmenityIds = collect($amenityIds)->filter(fn ($id) => !Amenity::whereKey($id)->exists())->values()->all();
            abort_if(!empty($invalidAmenityIds), 422, 'One or more selected amenities are invalid.');

            $validated['amenity_id'] = implode(',', $amenityIds);
            $selectedAmenityQuantity = (int) ($validated['quantity'] ?? 1);
            foreach ($amenityIds as $amenityId) {
                $amenity = Amenity::find($amenityId);
                abort_if($amenity?->capacity && $selectedAmenityQuantity > $amenity->capacity, 422, 'The selected amenity quantity exceeds its capacity.');
            }
        }

        if (!empty($validated['event_place_id'])) {
            $eventPlaceIds = collect(explode(',', $validated['event_place_id']))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '')
                ->all();

            $invalidEventIds = collect($eventPlaceIds)->filter(fn ($id) => !EventPlace::whereKey($id)->exists())->values()->all();
            abort_if(!empty($invalidEventIds), 422, 'One or more selected event packages are invalid.');

            $validated['event_place_id'] = implode(',', $eventPlaceIds);
            foreach ($eventPlaceIds as $eventPlaceId) {
                $eventPlace = EventPlace::find($eventPlaceId);
                abort_if($eventPlace?->capacity && !empty($validated['number_of_guests']) && $validated['number_of_guests'] > $eventPlace->capacity, 422, 'The selected guest count exceeds the package capacity.');
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
            $validated['guest_phone'] = $guest->contact_no;
        }

        $submissionToken = $validated['submission_token'] ?? null;
        if ($submissionToken && $request->session()->has('reservation_submission_' . $submissionToken)) {
            return redirect()->route('reservation');
        }

        if (empty($validated['check_out_time']) && !empty($validated['check_in_time'])) {
            $validated['check_out_time'] = $validated['check_in_time'];
        }

        $validated['status'] = 'pending';
        $validated['number_of_guests'] = max(1, (int) ($validated['number_of_guests'] ?? 1));
        $submittedTotalAmount = (float) $validated['total_amount'];
        $category = $validated['category'];

        if ($category === 'rooms') {
            $validated['room_check_in_time'] = $validated['check_in_time'] ?? null;
            $validated['room_check_out_time'] = $validated['check_out_time'] ?? null;
            $reservation = DB::transaction(function () use ($validated) {
                Room::query()->whereKey($validated['room_id'])->lockForUpdate()->firstOrFail();

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
        } elseif ($category === 'event_place') {
            $validated['event_start_time'] = $validated['event_start_time'] ?? $validated['check_in_time'] ?? null;
            $validated['event_end_time'] = $validated['event_end_time'] ?? $validated['check_out_time'] ?? null;
            $reservation = EventReservation::create(collect($validated)->only([
                'event_place_id', 'guest_name', 'guest_email', 'guest_phone',
                'event_type', 'check_in', 'event_start_time', 'check_out',
                'event_end_time', 'number_of_guests', 'status', 'total_amount',
                'payment_method', 'payment_details', 'amount_paid', 'special_requests',
            ])->all());
        } elseif ($category === 'amenities') {
            $amenity = Amenity::findOrFail($validated['amenity_id']);
            $amenityQuantity = max(1, (int) ($validated['amenity_quantity'] ?? 1));
            $pricingBasis = trim(strtolower((string) $amenity->pricing_basis));
            if (in_array($pricingBasis, ['per vehicle', 'per stay + per vehicle'], true) && $amenity->capacity && $amenityQuantity > $amenity->capacity) {
                throw ValidationException::withMessages([
                    'amenity_quantity' => 'The selected number of vehicles exceeds this amenity\'s available capacity.',
                ]);
            }
            $stayDays = max(1, Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out'])));
            $durationHours = max(1, (int) ($validated['duration_hours'] ?? 1));
            $amenityStartTime = $validated['check_in_time'] ?? '00:00';
            $endTime = Carbon::createFromFormat('Y-m-d H:i', $validated['check_in'] . ' ' . $amenityStartTime)
                ->addHours($durationHours);
            $validated['check_out'] = $endTime->toDateString();
            $validated['amenity_start_time'] = $amenityStartTime;
            $validated['amenity_end_time'] = $endTime->format('H:i');
            $validated['amenity_quantity'] = $amenityQuantity;
            $validated['total_amount'] = match ($pricingBasis) {
                'per stay + per vehicle' => (float) $amenity->price * ($stayDays + $amenityQuantity),
                'per vehicle' => (float) $amenity->price * $amenityQuantity,
                default => (float) $amenity->price,
            };
            $reservation = AmenityReservation::create(collect($validated)->only([
                'amenity_id', 'amenity_quantity', 'guest_name', 'guest_email', 'guest_phone', 'check_in',
                'amenity_start_time', 'check_out', 'amenity_end_time',
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
                    'number_of_guests' => $validated['number_of_guests'],
                    'status' => 'pending',
                    'total_amount' => max(0, $submittedTotalAmount - (float) $validated['total_amount']),
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

        if (!empty($diningSelections)) {
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
                'total_amount' => max(0, $submittedTotalAmount),
                'payment_method' => $validated['payment_method'],
                'payment_details' => $validated['payment_details'],
                'amount_paid' => 0,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);
            $diningReservation->diningItems()->createMany($diningSelections);
        }

        if ($category !== 'rooms' && $category !== 'amenities' && !empty($validated['room_id'])) {
            RoomReservation::create([
                'room_id' => $validated['room_id'],
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'],
                'check_in' => $validated['check_in'],
                'room_check_in_time' => $validated['check_in_time'] ?? null,
                'check_out' => $validated['check_out'],
                'room_check_out_time' => $validated['check_out_time'] ?? null,
                'number_of_guests' => $validated['number_of_guests'],
                'status' => 'pending',
                'total_amount' => max(0, $submittedTotalAmount),
                'payment_method' => $validated['payment_method'],
                'payment_details' => $validated['payment_details'],
                'amount_paid' => 0,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);
        }

        if ($category !== 'amenities' && !empty($validated['amenity_id'])) {
            $amenityId = (int) collect(explode(',', (string) $validated['amenity_id']))->filter()->first();
            $amenity = Amenity::find($amenityId);
            if ($amenity) {
                $amenityQuantity = max(1, (int) ($validated['amenity_quantity'] ?? $validated['quantity'] ?? 1));
                $amenityStartTime = $validated['check_in_time'] ?? '00:00';
                $amenityEndTime = Carbon::createFromFormat('Y-m-d H:i', $validated['check_in'] . ' ' . $amenityStartTime)
                    ->addHours(max(1, (int) ($validated['duration_hours'] ?? 1)));
                AmenityReservation::create([
                    'amenity_id' => $amenity->id,
                    'amenity_quantity' => $amenityQuantity,
                    'guest_name' => $validated['guest_name'],
                    'guest_email' => $validated['guest_email'],
                    'guest_phone' => $validated['guest_phone'],
                    'check_in' => $validated['check_in'],
                    'amenity_start_time' => $amenityStartTime,
                    'check_out' => $amenityEndTime->toDateString(),
                    'amenity_end_time' => $amenityEndTime->format('H:i'),
                    'number_of_guests' => $validated['number_of_guests'],
                    'status' => 'pending',
                    'total_amount' => max(0, $submittedTotalAmount),
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
    
    public function accommodation()
    {
        $rooms = Room::where('status', 'available')->get();
        return view('accommodation', compact('rooms'));
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
            EventReservation::with(['eventPlace', 'diningItems.diningMenu', 'payments'])
                ->where('guest_email', $guest->email)->get()
                ->each(fn ($reservation) => $reservation->category = 'event_place'),
            AmenityReservation::with(['amenity', 'payments'])
                ->where('guest_email', $guest->email)->get()
                ->each(fn ($reservation) => $reservation->category = 'amenities'),
            DiningReservation::with(['diningItems.diningMenu', 'payments'])
                ->where('guest_email', $guest->email)->get()
                ->each(fn ($reservation) => $reservation->category = 'dining'),
        ])->flatten(1)->map(function ($source) {
            $reservation = new Reservation();
            $reservation->forceFill($source->getAttributes());
            $reservation->setAttribute('category', $source->category);
            $reservation->setRelation('room', $source->relationLoaded('room') ? $source->getRelation('room') : null);
            $reservation->setRelation('amenities', $source->relationLoaded('amenity') && $source->amenity ? collect([$source->amenity]) : collect());
            $reservation->setRelation('eventPlaces', $source->relationLoaded('eventPlace') && $source->eventPlace ? collect([$source->eventPlace]) : collect());
            $reservation->setRelation('diningItems', $source->relationLoaded('diningItems') ? $source->getRelation('diningItems') : collect());
            $reservation->setRelation('payments', $source->relationLoaded('payments') ? $source->getRelation('payments') : collect());

            return $reservation;
        });

        $reservations = $reservations->concat($categoryReservations)
            ->sortByDesc('created_at')
            ->values();

        $reservations->each(function (Reservation $reservation) {
            $amenityIds = array_values(array_filter(array_map('trim', explode(',', (string) $reservation->amenity_id))));
            $eventPlaceIds = array_values(array_filter(array_map('trim', explode(',', (string) $reservation->event_place_id))));

            $reservation->setRelation('amenities', Amenity::whereIn('id', $amenityIds)->get());
            $reservation->setRelation('eventPlaces', EventPlace::whereIn('id', $eventPlaceIds)->get());
        });

        $activeReservation = $this->activeReservationFor($guest);
        $guestRequests = GuestRequest::with('room')
            ->where('guest_id', $guest->id)
            ->latest('submitted_at')
            ->get();

        return view('profile-records', compact('reservations', 'activeReservation', 'guestRequests'));
    }

    public function receipts()
    {
        $guest = Auth::guard('guest')->user();
        abort_unless($guest, 403);

        $receipts = Reservation::with('room')
            ->where('guest_email', $guest->email)
            ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile-receipts', compact('receipts'));
    }

    public function deleteReservation(Request $request, $reservation)
    {
        $guest = Auth::guard('guest')->user();
        abort_unless($guest, 403);

        $reservation = match ($request->input('category')) {
            'rooms' => RoomReservation::find($reservation),
            'event_place' => EventReservation::find($reservation),
            'amenities' => AmenityReservation::find($reservation),
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

    public function cancelReservation(Request $request, $reservation)
    {
        $guest = Auth::guard('guest')->user();
        abort_unless($guest, 403);

        $reservation = match ($request->input('category')) {
            'rooms' => RoomReservation::find($reservation),
            'event_place' => EventReservation::find($reservation),
            'amenities' => AmenityReservation::find($reservation),
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

        $reservation->update(['status' => 'cancelled']);

        if ($reservation->status === 'checked-in') {
            $reservation->loadMissing('room');
            $reservation->room?->update([
                'status' => 'available',
                'cleaning_status' => 'dirty',
            ]);
        }

        return redirect()->route('guest.records')->with('success', 'Your reservation has been cancelled successfully.');
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

            $validItems[] = [
                'request_type' => $type,
                'quantity' => $quantity,
                'department' => in_array($type, $housekeepingTypes, true) ? 'Housekeeping' : 'Employee',
            ];
        }

        if (empty($validItems)) {
            return back()->withErrors(['request_type' => 'Please select at least one valid request type.'])->withInput();
        }

        $createdIds = [];
        foreach ($validItems as $item) {
            $guestRequest = GuestRequest::create([
                'guest_id' => $guest->id,
                'reservation_id' => $reservation->getAttribute('request_reservation_id'),
                'room_id' => $reservation->room_id,
                'request_type' => $item['request_type'],
                'description' => $validated['description'],
                'department' => $item['department'],
                'priority' => $validated['priority'],
                'preferred_time' => $validated['preferred_time'] ?? null,
                'status' => 'New',
                'quantity' => $item['quantity'],
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
                    'description' => $databaseRoom->description ?? 'Enjoy a comfortable room with thoughtful amenities and a welcoming atmosphere.',
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
