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
use App\Models\ReservationDiningItem;
use App\Models\GuestRequest;
use Illuminate\Support\Facades\Auth;

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
        $diningTables = DiningTable::where('status', 'Available')->orderBy('table_no')->get();

        return view('reservation', compact('rooms', 'amenities', 'events', 'dining', 'diningSchedules', 'diningTables'));
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

        if (!empty($diningSelections) || !empty($request->input('dining_id')) || !empty($request->input('dining_area')) || !empty($request->input('dining_schedule'))) {
            $request->merge(['category' => 'dining']);
        }

        $request->merge([
            'amenity_id' => $this->normalizeIdList($request->input('amenity_id')),
            'event_place_id' => $this->normalizeIdList($request->input('event_place_id')),
            'dining_id' => empty($diningSelections) ? $this->normalizeIdList($request->input('dining_id')) : null,
        ]);

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out' => 'required|date|after:check_in',
            'check_out_time' => 'nullable|date_format:H:i',
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
            'amenity_id' => 'nullable|string',
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

        if ($submissionToken) {
            $request->session()->put('reservation_submission_' . $submissionToken, true);
        }

        unset($validated['submission_token']);

        if (empty($validated['check_out_time']) && !empty($validated['check_in_time'])) {
            $validated['check_out_time'] = $validated['check_in_time'];
        }

        $reservation = Reservation::create(array_merge($validated, ['status' => 'pending']));

        if (!empty($diningSelections)) {
            $reservation->diningItems()->createMany($diningSelections);
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

        $reservations = Reservation::with('room')
            ->where('guest_email', $guest->email)
            ->orderBy('created_at', 'desc')
            ->get();

        $activeReservation = $this->activeReservationFor($guest);
        $guestRequests = GuestRequest::with('room')
            ->where('guest_id', $guest->id)
            ->latest('submitted_at')
            ->get();

        return view('profile-records', compact('reservations', 'activeReservation', 'guestRequests'));
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
            'request_type' => ['required', 'string', 'in:'.implode(',', $requestTypes)],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'in:Normal,Urgent'],
            'preferred_time' => ['nullable', 'date_format:H:i'],
        ]);

        $reservation = $this->activeReservationFor($guest);
        if (!$reservation) {
            return back()->withErrors(['request_type' => 'You need an active reservation to submit a guest request.'])->withInput();
        }

        $guestRequest = GuestRequest::create([
            'guest_id' => $guest->id,
            'reservation_id' => $reservation->id,
            'room_id' => $reservation->room_id,
            'request_type' => $validated['request_type'],
            'description' => $validated['description'],
            'department' => in_array($validated['request_type'], $housekeepingTypes, true) ? 'Housekeeping' : 'Employee',
            'priority' => $validated['priority'],
            'preferred_time' => $validated['preferred_time'] ?? null,
            'status' => 'New',
            'submitted_at' => now(),
        ]);

        return redirect()->route('guest.records')
            ->with('request_success', 'Your request has been submitted successfully.')
            ->with('request_id', $guestRequest->id);
    }

    protected function activeReservationFor($guest): ?Reservation
    {
        return Reservation::with('room')
            ->where('guest_email', $guest->email)
            ->whereIn('status', ['confirmed', 'checked-in'])
            ->whereDate('check_in', '<=', today())
            ->whereDate('check_out', '>=', today())
            ->latest('check_in')
            ->first();
    }

    public function roomDetail($slug)
    {
        $room = collect($this->featuredRooms())->firstWhere('slug', $slug);

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
