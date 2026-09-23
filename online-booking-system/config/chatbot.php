<?php

return [
    'categories' => [
        [
            'label' => 'Reservations',
            'aliases' => ['reservation information', 'booking information'],
            'questions' => [
                'How can I make a reservation?' => 'You can make a reservation through the hotel reservation page. Select your preferred room, dates, and any extras before confirming.',
                'How can I check room availability?' => 'You can ask me to show available rooms, or visit the reservation page and enter your preferred dates and number of guests.',
                'Can I modify my reservation?' => 'Yes. Please contact the hotel or front desk with your reservation details so our staff can help update your dates, room, or guest information.',
                'How can I cancel my reservation?' => 'You can cancel an eligible reservation from your guest profile, or contact the hotel with your reservation details for assistance.',
            ],
        ],
        [
            'label' => 'Rooms',
            'aliases' => ['room information', 'accommodation'],
            'questions' => [
                'What types of rooms are available?' => 'We offer room types such as Standard, Deluxe, Executive, and Suite options, subject to current availability.',
                'What amenities are included?' => 'Room amenities vary by room type. Please check the room details on the accommodation page or ask our front desk about a specific room.',
                'How many guests can stay in a room?' => 'Guest capacity depends on the room type. Please check the selected room details or share your guest count so we can recommend an option.',
                'Do you have rooms with a balcony?' => 'Balcony availability depends on the room type and current inventory. Please contact the hotel so we can check your preferred dates.',
            ],
        ],
        [
            'label' => 'Check-in / Check-out',
            'aliases' => ['check in and check out', 'checkin checkout', 'arrival and departure'],
            'questions' => [
                'What time is check-in?' => 'Our standard check-in time is 2:00 PM. If you need an earlier check-in, please contact the hotel or check availability with our staff.',
                'What time is check-out?' => 'Our standard check-out time is 12:00 PM. Late check-out requests are subject to room availability and front desk approval.',
                'Can I request early check-in?' => 'Yes. Early check-in can be requested in advance, but it depends on room availability on the day you arrive.',
                'Can I request late check-out?' => 'Yes. Please ask the front desk about late check-out. Approval depends on availability and may be subject to an additional charge.',
            ],
        ],
        [
            'label' => 'Payment Information',
            'aliases' => ['payment', 'payments'],
            'questions' => [
                'What payment methods are accepted?' => 'Please contact the hotel for the latest accepted payment methods and any applicable payment requirements.',
                'Do I need to pay in advance?' => 'Advance payment requirements depend on the reservation and selected rate. The required payment details will be shown during booking or confirmed by our staff.',
                'Can I pay at the hotel?' => 'Some reservations may be paid at the hotel. Please check your reservation terms or contact the front desk before arrival.',
                'Where can I check my payment status?' => 'You can review payment details in your guest profile or ask the front desk to confirm the status of your reservation.',
                'Can I get a receipt?' => 'Yes. Please request a receipt from the front desk, or check your guest profile if a digital receipt is available for your reservation.',
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
            'aliases' => ['services', 'hotel amenities'],
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
                'What is the cancellation policy?' => 'Cancellation terms depend on your reservation rate and booking details. Please check your reservation confirmation or contact the hotel before cancelling.',
                'Are pets allowed?' => 'Pet policies may vary by room and reservation. Please contact the hotel before booking so our staff can confirm the current policy.',
                'Is smoking allowed?' => 'Please contact the hotel for the current smoking policy and designated smoking areas.',
                'Are visitors allowed?' => 'Visitors must follow hotel registration and safety policies. Please check with the front desk before inviting visitors to your room.',
            ],
        ],
    ],
];