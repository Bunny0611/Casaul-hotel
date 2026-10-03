<?php

namespace App\Http\Controllers;

use App\Models\DiningMenu;
use App\Models\Event;
use App\Models\Facility;
use App\Models\GuestRequest;
use App\Models\Message;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomReservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function message(Request $request)
    {
        $message = trim((string) $request->input('message', ''));
        $action = (string) $request->input('action', '');

        if ($message === '') {
            return response()->json([
                'reply' => 'Please type your question so I can help you.',
            ], 422);
        }

        if ($action === 'contact_front_desk'
            || $this->normalizeFaqText($message) === $this->normalizeFaqText('Contact Front Desk')) {
            if ($this->normalizeFaqText($message) === $this->normalizeFaqText('Contact Front Desk')) {
                $request->session()->put('chatbot_mode', 'contact_front_desk');
            } else {
                $request->session()->forget('chatbot_mode');
            }

            return response()->json($this->contactFrontDesk($message));
        }

        if ($action !== '') {
            $request->session()->forget('chatbot_mode');
        }

        if ($action === '' && $request->session()->pull('chatbot_mode') === 'contact_front_desk') {
            return response()->json($this->contactFrontDesk($message));
        }

        if ($action === 'request_housekeeping') {
            return response()->json($this->requestHousekeeping($message, (string) $request->input('request_type', $message)));
        }

        $faqReply = $this->faqReply($message);

        return response()->json(array_filter([
            'reply' => $faqReply['reply'] ?? $this->generateReply($message),
            'quick_replies' => $faqReply['quick_replies'] ?? null,
        ], static fn ($value) => $value !== null));
    }

    public function guestMessages()
    {
        $guest = Auth::guard('guest')->user();

        abort_unless($guest, 403);

        return response()->json([
            'messages' => $this->messagesForGuest($guest->email)->map(fn (Message $message) => [
                'id' => $message->id,
                'message' => $message->message,
                'reply' => $message->admin_reply,
                'is_replied' => (bool) $message->is_replied,
                'sent_at' => $message->created_at?->toISOString(),
                'replied_at' => $message->replied_at?->toISOString(),
                'replies' => $this->repliesForMessage($message),
            ])->values(),
        ]);
    }

    protected function contactFrontDesk(string $message): array
    {
        $guest = Auth::guard('guest')->user();

        if (! $guest) {
            return [
                'reply' => 'Please sign in as a guest before contacting the front desk.',
                'auth_required' => true,
            ];
        }

        if ($this->normalizeFaqText($message) === $this->normalizeFaqText('Contact Front Desk')) {
            return [
                'reply' => 'Please type your message for the front desk. Your message and any reply will remain available in this chat.' . $this->formatGuestConversation($guest->email),
                'mode' => 'contact_front_desk',
            ];
        }

        $frontDeskMessage = Message::create([
            'customer_name' => $guest->name,
            'customer_email' => $guest->email,
            'message' => $message,
        ]);

        return [
            'reply' => 'Your message has been sent to the front desk. We will reply as soon as possible. Your conversation history is shown below.' . $this->formatGuestConversation($guest->email),
            'mode' => 'contact_front_desk',
            'message_id' => $frontDeskMessage->id,
        ];
    }

    protected function requestHousekeeping(string $message, string $requestType): array
    {
        $guest = Auth::guard('guest')->user();
        $requestTypes = ['Room Cleaning', 'Towels', 'Bed Linens', 'Toiletries', 'Other Request'];

        if (! $guest) {
            return [
                'reply' => 'Please sign in as a guest before requesting housekeeping.',
                'mode' => 'request_housekeeping',
            ];
        }

        if ($this->normalizeFaqText($message) === $this->normalizeFaqText('Request Housekeeping')) {
            return [
                'reply' => 'What can housekeeping help you with?',
                'quick_replies' => $requestTypes,
                'mode' => 'request_housekeeping',
            ];
        }

        $selectedType = collect($requestTypes)->first(fn ($type) => $this->normalizeFaqText($type) === $this->normalizeFaqText($requestType));
        if (! $selectedType) {
            return [
                'reply' => 'Please choose one of the housekeeping request types below.',
                'quick_replies' => $requestTypes,
                'mode' => 'request_housekeeping',
            ];
        }

        $reservation = $this->activeRoomReservationFor($guest);
        if (! $reservation) {
            return [
                'reply' => 'A current confirmed or checked-in room reservation is required before submitting a housekeeping request. Please contact the front desk if you need help.',
                'mode' => 'request_housekeeping',
            ];
        }

        GuestRequest::create([
            'guest_id' => $guest->id,
            'room_id' => $reservation->room_id,
            'request_type' => $selectedType,
            'description' => $selectedType . ' requested through the hotel chatbot.',
            'department' => 'Housekeeping',
            'priority' => 'Normal',
            'status' => 'Pending',
            'reservation_type' => RoomReservation::class,
            'reservation_key' => $reservation->id,
            'submitted_at' => now(),
        ]);

        return [
            'reply' => 'Your ' . strtolower($selectedType) . ' request has been submitted to housekeeping. You can follow its status from your guest records.',
            'quick_replies' => ['Contact Front Desk', 'Request Housekeeping'],
            'mode' => 'request_housekeeping',
        ];
    }

    protected function activeRoomReservationFor($guest): ?RoomReservation
    {
        return RoomReservation::query()
            ->where('guest_email', $guest->email)
            ->whereIn('status', ['confirmed', 'checked-in'])
            ->whereDate('check_in', '<=', today())
            ->whereDate('check_out', '>=', today())
            ->latest('check_in')
            ->first();
    }

    protected function messagesForGuest(string $email)
    {
        return Message::with('replies')->where('customer_email', $email)->latest()->get();
    }

    protected function repliesForMessage(Message $message)
    {
        if ($message->replies->isNotEmpty()) {
            return $message->replies->map(fn ($reply) => [
                'reply' => $reply->reply,
                'replied_at' => $reply->replied_at?->toISOString(),
            ])->values();
        }

        return $message->admin_reply ? collect([[
            'reply' => $message->admin_reply,
            'replied_at' => $message->replied_at?->toISOString(),
        ]]) : collect();
    }

    protected function formatGuestConversation(string $email): string
    {
        return $this->messagesForGuest($email)->reverse()->map(function (Message $message) {
            $sentAt = $message->created_at?->format('M j, Y g:i A') ?? 'Unknown time';
            $replies = $this->repliesForMessage($message);
            $reply = $replies->isNotEmpty()
                ? $replies->map(fn (array $item) => "\nFront Desk (" . ($item['replied_at'] ?? 'reply time unavailable') . "): " . $item['reply'])->implode('')
                : "\nFront Desk: Reply pending";

            return "\n\nYou (" . $sentAt . "): " . $message->message . $reply;
        })->implode('');
    }

    protected function faqReply(string $message): ?array
    {
        $normalizedMessage = $this->normalizeFaqText($message);
        $categories = config('chatbot.categories', []);
        $categoryLabels = array_column($categories, 'label');

        if ($normalizedMessage === $this->normalizeFaqText('Contact Us')) {
            return [
                'reply' => "CASAUL Hotel Tabaco\nMobile: (+63) 935 017 7564\nEmail: taba-roomsreservation@casahotels.com\nAddress: Tomas Cabiles St., Tabaco City\n\nCorporate Office\nTel. No.: (052) 203-0244 / (052) 203-0243\nEmail: inquiry@casaulhotels.com",
                'quick_replies' => $categoryLabels,
            ];
        }

        foreach ($categories as $category) {
            $categoryNames = array_merge([$category['label']], $category['aliases'] ?? []);
            $normalizedCategoryNames = array_map(fn ($name) => $this->normalizeFaqText($name), $categoryNames);

            if (in_array($normalizedMessage, $normalizedCategoryNames, true)) {
                if ($category['label'] === 'Facilities') {
                    return [
                        'reply' => $this->facilityReply(),
                        'quick_replies' => $categoryLabels,
                    ];
                }

                if ($category['label'] === 'Hotel Services') {
                    return [
                        'reply' => $this->hotelServicesReply(),
                        'quick_replies' => array_keys($category['questions']),
                    ];
                }

                return [
                    'reply' => 'Here are some common questions about ' . $category['label'] . '. Select a question below or type your own question.',
                    'quick_replies' => array_keys($category['questions']),
                ];
            }

            foreach ($category['questions'] as $question => $answer) {
                if ($normalizedMessage === $this->normalizeFaqText($question)) {
                    if ($question === 'What types of rooms are available?') {
                        $answer = $this->roomTypesReply();
                    } elseif ($question === 'What payment methods are accepted?') {
                        $answer = $this->acceptedPaymentMethodsReply();
                    } elseif ($question === 'What is the cancellation policy?') {
                        $answer = 'Cancellation Policy: ' . config('reservation.cancellation_policy');
                    } elseif ($category['label'] === 'Hotel Services') {
                        $answer = $this->hotelServiceQuestionReply($question);
                    }

                    return [
                        'reply' => $answer,
                        'quick_replies' => $categoryLabels,
                    ];
                }
            }
        }

        return null;
    }

    protected function normalizeFaqText(string $text): string
    {
        return trim((string) preg_replace('/[^a-z0-9]+/i', ' ', strtolower($text)));
    }

    protected function generateReply(string $message): string
    {
        $normalized = strtolower($message);

        if (Str::contains($normalized, ['check-in', 'check in', 'arrival', 'arrive'])) {
            return 'Check-in time is 3:00 PM.';
        }

        if (Str::contains($normalized, ['check-out', 'check out', 'departure', 'checkout'])) {
            return 'Check-out time is 12:00 PM.';
        }

        if (Str::contains($normalized, ['room type', 'room types', 'type of room', 'types of room'])) {
            return $this->roomTypesReply();
        }

        if (Str::contains($normalized, ['deluxe', 'standard'])) {
            return $this->roomTypeReply($normalized, $message);
        }

        if (Str::contains($normalized, ['room', 'rooms', 'accommodation', 'stay'])) {
            return $this->roomReply($normalized);
        }

        if (Str::contains($normalized, ['facility', 'facilities', 'amenity', 'amenities'])) {
            return $this->facilityReply();
        }

        if (Str::contains($normalized, ['dining', 'restaurant', 'meal', 'breakfast', 'food'])) {
            return $this->diningReply($normalized);
        }

        if (Str::contains($normalized, ['event', 'events', 'wedding', 'party', 'venue'])) {
            return $this->eventReply($normalized);
        }

        if (Str::contains($normalized, ['reservation', 'book', 'booking', 'reserve'])) {
            return $this->reservationReply($normalized);
        }

        if (Str::contains($normalized, ['hotel', 'contact', 'location', 'address', 'about'])) {
            return $this->hotelInfoReply($normalized);
        }

        if (Str::contains($normalized, ['hello', 'hi', 'hey'])) {
            return 'Hello! I can help with rooms, dining, facilities, events, and reservations. What would you like to know?';
        }

        return 'I can help with rooms, facilities, dining, events, reservations, check-in and check-out, and general hotel information. Please ask a question about one of those topics.';
    }

    protected function roomReply(string $normalized): string
    {
        $roomCount = Room::count();
        $lowestPrice = Room::query()->whereNotNull('price')->min('price');

        $priceText = $lowestPrice ? ' from ₱' . number_format((float) $lowestPrice, 2) : 'from our current available rates';

        if (Str::contains($normalized, ['available', 'availability'])) {
            return $this->availableRoomsReply();
        }

        if (Str::contains($normalized, ['price', 'rate', 'cost'])) {
            return 'Our rooms start at ' . $priceText . '. We offer Deluxe and Standard rooms.';
        }

        return 'Casaul Hotel offers two room types: Deluxe and Standard. There are currently ' . $roomCount . ' rooms in our inventory.';
    }

    protected function roomTypesReply(): string
    {
        return "Casaul Hotel offers two room types:\n• Deluxe\n• Standard\n\nWould you like to view the available rooms?";
    }

    protected function roomTypeReply(string $normalized, string $message): string
    {
        $candidate = strtolower(trim($message));

        $typeMap = [
            'deluxe' => 'Deluxe',
            'standard' => 'Standard',
        ];

        $requestedType = null;
        foreach ($typeMap as $keyword => $label) {
            if (Str::contains($candidate, $keyword)) {
                $requestedType = $label;
                break;
            }
        }

        if ($requestedType === null) {
            return $this->roomTypesReply();
        }

        $typesToCheck = [$requestedType];

        if ($requestedType === 'Deluxe') {
            $typesToCheck[] = 'Standard';
        }

        $typesToCheck = array_values(array_unique($typesToCheck));
        $availableByType = [];

        foreach ($typesToCheck as $type) {
            $rooms = Room::query()
                ->orderBy('room_number')
                ->get()
                ->filter(function ($room) use ($type) {
                    if (! $this->isRoomAvailableForChat($room->status ?? null)) {
                        return false;
                    }

                    $roomType = strtolower((string) ($room->room_type ?? ''));
                    $requested = strtolower($type);

                    return Str::contains($roomType, $requested)
                        || Str::contains($requested, $roomType);
                });

            if ($rooms->isNotEmpty()) {
                $availableByType[$type] = $rooms;
            }
        }

        if ($availableByType === []) {
            return 'There are currently no available ' . $requestedType . ' rooms. Please check other room types or let us know your preferred dates and we will help you find an option.';
        }

        $sections = [];
        foreach ($typesToCheck as $type) {
            if (! isset($availableByType[$type])) {
                continue;
            }

            $availableRoomList = $availableByType[$type]
                ->map(function ($room) {
                    return $this->formatRoomAvailabilityItem($room);
                })
                ->implode("\n");

            $sections[] = 'Available ' . $type . ' rooms:' . "\n" . $availableRoomList;
        }

        return implode("\n\n", $sections) . "\n\nWould you like me to help you check your preferred dates and guest count?";
    }

    protected function availableRoomsReply(): string
    {
        $availableRooms = Room::query()
            ->orderBy('room_number')
            ->get()
            ->filter(fn ($room) => $this->isRoomAvailableForChat($room->status ?? null));

        if ($availableRooms->isEmpty()) {
            return 'There are currently no available rooms in our system. Please check other dates or contact our front desk for assistance.';
        }

        $availableRoomList = $availableRooms
            ->map(function ($room) {
                return $this->formatRoomAvailabilityItem($room);
            })
            ->implode("\n");

        return 'Currently available rooms:' . "\n" . $availableRoomList . "\n\nLet me know your preferred dates and guest count, and I can help you choose the best option.";
    }

    protected function isRoomAvailableForChat($status): bool
    {
        $normalized = strtolower(trim((string) $status));
        $compact = str_replace(['_', ' ', '-'], '', $normalized);

        $availableStatuses = [
            'available',
            'vacant',
            'vacant_ready',
            'vacantready',
            'vacant_clean',
            'vacantclean',
            'vr',
            'vc',
            'clean',
            'ready',
        ];

        return in_array($normalized, $availableStatuses, true)
            || in_array($compact, $availableStatuses, true);
    }

    protected function formatRoomAvailabilityItem($room): string
    {
        $roomNumber = $room->room_number ?? 'N/A';
        $roomType = trim((string) ($room->room_type ?? ''));
        $price = $room->price ? '₱' . number_format((float) $room->price, 2) . '/night' : 'N/A';
        $typeSuffix = $roomType !== '' ? ' — ' . $roomType : '';

        return '• Room ' . $roomNumber . $typeSuffix . "\n  Price: " . $price;
    }

    protected function facilityReply(): string
    {
        $facilityNames = $this->availableFacilityNames();

        if ($facilityNames === []) {
            return 'No facilities are currently available.';
        }

        return "Available facilities:\n• " . implode("\n• ", $facilityNames);
    }

    protected function hotelServicesReply(): string
    {
        $services = ['housekeeping requests'];

        if (DiningMenu::query()->whereRaw('LOWER(status) = ?', ['available'])->exists()) {
            $services[] = 'dining';
        }

        $services = array_merge($services, $this->availableFacilityNames());

        return 'Available hotel services: ' . implode(', ', $services) . '.';
    }

    protected function hotelServiceQuestionReply(string $question): string
    {
        if (Str::contains(strtolower($question), 'housekeeping')) {
            return 'Yes. Submit a housekeeping request here in chat.';
        }

        if (Str::contains(strtolower($question), ['wi-fi', 'wifi'])) {
            return 'Yes. Wi-Fi is available in all rooms.';
        }

        $searchTerm = Str::contains(strtolower($question), 'parking') ? 'parking' : 'wi-fi';
        $normalizedSearchTerm = preg_replace('/[^a-z0-9]+/i', '', $searchTerm);
        $facility = collect($this->availableFacilityNames())->first(function ($name) use ($normalizedSearchTerm) {
            $normalizedName = preg_replace('/[^a-z0-9]+/i', '', strtolower($name));
            return str_contains($normalizedName, $normalizedSearchTerm);
        });

        if ($facility) {
            return $facility . ' is currently available.';
        }

        if (Str::contains(strtolower($question), 'room service')) {
            return 'Please contact the front desk to confirm room service availability.';
        }

        return 'Please contact the front desk to confirm ' . $searchTerm . ' availability.';
    }

    protected function availableFacilityNames(): array
    {
        return Facility::query()
            ->whereRaw('LOWER(status) = ?', ['available'])
            ->orderBy('name')
            ->pluck('name')
            ->all();
    }

    protected function acceptedPaymentMethodsReply(): string
    {
        $paymentMethods = array_values(array_filter(
            config('reservation.payment_methods', []),
            static fn ($method) => is_string($method) && trim($method) !== ''
        ));

        return $paymentMethods
            ? "Accepted payment methods:\n• " . implode("\n• ", $paymentMethods)
            : 'Please contact the front desk to confirm accepted payment methods.';
    }

    protected function diningReply(string $normalized): string
    {
        $diningCount = DiningMenu::count();
        $menuItems = DiningMenu::query()->select('name')->orderBy('name')->pluck('name')->take(5)->all();
        $listText = $menuItems ? implode(', ', $menuItems) : 'signature dishes and refreshments';

        if (Str::contains($normalized, ['menu', 'food', 'meal'])) {
            return 'Our dining menu includes ' . $diningCount . ' item' . ($diningCount === 1 ? '' : 's') . ' such as ' . $listText . '. You can also ask about breakfast, main courses, desserts, or beverages.';
        }

        return 'Casaul Hotel offers dining options for guests, including breakfast, meals, and beverages. We currently have ' . $diningCount . ' menu item' . ($diningCount === 1 ? '' : 's') . ' available in our system.';
    }

    protected function eventReply(string $normalized): string
    {
        $eventCount = Event::count();
        $eventNames = Event::query()->select('name')->orderBy('name')->pluck('name')->take(5)->all();
        $eventList = $eventNames ? implode(', ', $eventNames) : 'our event packages';

        if (Str::contains($normalized, ['price', 'cost'])) {
            $lowestPrice = Event::query()->whereNotNull('price')->min('price');
            $priceText = $lowestPrice ? ' starting at ₱' . number_format((float) $lowestPrice, 2) : 'based on the package you choose';
            return 'Event packages are available' . $priceText . '. We currently have ' . $eventCount . ' package' . ($eventCount === 1 ? '' : 's') . ' in the system, including ' . $eventList . '.';
        }

        return 'We offer ' . $eventCount . ' event package' . ($eventCount === 1 ? '' : 's') . ' in our system, including ' . $eventList . '. These packages are suitable for celebrations and gatherings.';
    }

    protected function reservationReply(string $normalized): string
    {
        $roomReservationCount = RoomReservation::count();
        $totalReservations = Reservation::count();

        if (Str::contains($normalized, ['status', 'pending', 'confirmed'])) {
            return "To check your reservation status, follow these steps:\n1. Sign in as a guest.\n2. Click the account icon at the top right and open [My Profile](/guest/profile).\n3. Select View Records and find your reservation. Contact the front desk if you need help. We also support room, facility, event, and dining reservations.";
        }

        if (Str::contains($normalized, ['how do i book', 'book room', 'book a room', 'make reservation'])) {
            return 'You can make a reservation through [https://casaulhotel.com/reservation](/reservation). Select your preferred room, dates, and any extras such as facilities or dining before confirming.';
        }

        return 'We support room, facility, dining, and event reservations. Our system currently shows ' . $totalReservations . ' reservation record' . ($totalReservations === 1 ? '' : 's') . ' and ' . $roomReservationCount . ' room reservation' . ($roomReservationCount === 1 ? '' : 's') . ' in the database.';
    }

    protected function hotelInfoReply(string $normalized): string
    {
        $hotelName = config('app.name', 'Casaul Hotel');
        $roomCount = Room::count();
        $facilityCount = Facility::count();
        $eventCount = Event::count();
        $diningCount = DiningMenu::count();

        return $hotelName . ' offers ' . $roomCount . ' room' . ($roomCount === 1 ? '' : 's') . ', ' . $facilityCount . ' facility option' . ($facilityCount === 1 ? '' : 's') . ', ' . $eventCount . ' event package' . ($eventCount === 1 ? '' : 's') . ', and ' . $diningCount . ' dining item' . ($diningCount === 1 ? '' : 's') . '. We are here to help with reservations, room selection, dining, and guest requests.';
    }
}
