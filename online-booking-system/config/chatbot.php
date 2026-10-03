<?php

return [
    'categories' => [
        [
            'label' => 'Reservations',
            'aliases' => ['reservation information', 'booking information'],
            'questions' => [
                'How can I make a reservation?' => 'You can make a reservation through [https://casaulhotel.com/reservation](/reservation). Select your preferred room, dates, and any extras before confirming.',
                'How can I check room availability?' => 'You can ask me to show available rooms, or visit [https://casaulhotel.com/reservation](/reservation) and enter your preferred dates and number of guests.',
                'Can I modify my reservation?' => 'Yes. Please contact the hotel or front desk with your reservation details so our staff can help update your dates, room, or guest information.',
                'How can I cancel my reservation?' => "To cancel an eligible reservation, follow these steps:\n1. Sign in as a guest.\n2. Click the account icon at the top right and open [My Profile](/guest/profile).\n3. Select View Records, find your reservation, and choose Cancel if available. Otherwise, contact the front desk.",
            ],
        ],
        [
            'label' => 'Rooms',
            'aliases' => ['room information', 'accommodation'],
            'questions' => [
                'What types of rooms are available?' => '',
                'How many guests can stay in a room?' => 'Guest capacity depends on the room type. Please check the selected room details or share your guest count so we can recommend an option.',
                'Do you have rooms with a balcony?' => 'Balcony availability depends on the room type and current inventory. Please contact the hotel so we can check your preferred dates.',
            ],
        ],
        [
            'label' => 'Facilities',
            'aliases' => ['amenities', 'amenity'],
            'questions' => [],
        ],
        [
            'label' => 'Check-in / Check-out',
            'aliases' => ['check in and check out', 'checkin checkout', 'arrival and departure'],
            'questions' => [
                'What time is check-in?' => 'Check-in time is 3:00 PM.',
                'What time is check-out?' => 'Check-out time is 12:00 PM.',
                'Can I request early check-in?' => 'Please contact the front desk to confirm early check-in availability.',
                'Can I request late check-out?' => 'Please contact the front desk to confirm late check-out availability.',
            ],
        ],
        [
            'label' => 'Payment Information',
            'aliases' => ['payment', 'payments'],
            'questions' => [
                'What payment methods are accepted?' => '',
                'Do I need to pay in advance?' => 'Advance payment requirements depend on the reservation and selected rate. The required payment details will be shown during booking or confirmed by our staff.',
                'Can I pay at the hotel?' => 'Some reservations may be paid at the hotel. Please check your reservation terms or contact the front desk before arrival.',
                'Where can I check my payment status?' => "To check a payment, follow these steps:\n1. Sign in as a guest.\n2. Click the account icon and open [My Profile](/guest/profile).\n3. Select View Records and open the relevant reservation. You can also check My Receipts in the account menu or contact the front desk.",
                'Can I get a receipt?' => "To check for a digital receipt, follow these steps:\n1. Sign in as a guest.\n2. Click the account icon and choose My Receipts if it is available.\n3. If you do not see My Receipts, open [My Profile](/guest/profile) and select View Records. Contact the front desk if you need help.",
            ],
        ],
        [
            'label' => 'Dining & Menu',
            'aliases' => ['dining', 'menu'],
            'questions' => [
                'What food and drinks are available?' => 'Our dining service offers meals, breakfast options, beverages, and other menu items. Ask me about the current menu to see available items.',
                'What are the menu prices?' => 'Menu prices vary by item. Please visit the dining menu or ask our staff for the latest prices.',
                'What time is the restaurant open?' => 'Restaurant hours can vary. Please contact the hotel or check the dining page for the current schedule.',
                'Can I order food to my room?' => 'Room delivery may be available for selected dining items. Please contact the hotel to confirm current room service availability.',
            ],
        ],
        [
            'label' => 'Hotel Services',
            'aliases' => ['services'],
            'questions' => [
                'Is Wi-Fi available?' => 'Wi-Fi availability and access details can be confirmed with the front desk when you arrive.',
                'Is parking available?' => 'Please contact the hotel for current parking availability and any parking instructions or fees.',
                'Do you offer housekeeping?' => 'Yes. Housekeeping is available for guest rooms. Please contact the front desk for scheduling or special requests.',
                'Do you offer room service?' => 'Room service availability depends on the current hotel schedule. Please contact the front desk for the available options.',
            ],
        ],
        [
            'label' => 'Hotel Policies',
            'aliases' => ['policies', 'hotel policy'],
            'questions' => [
                'What is the cancellation policy?' => '',
                'Are pets allowed?' => 'Pets are permitted at the hotel.',
                'Is smoking allowed?' => 'Smoking is permitted only in the designated area at the back of the hotel.',
                'Are visitors allowed?' => 'Visitors are permitted at the hotel.',
            ],
        ],
    ],
];