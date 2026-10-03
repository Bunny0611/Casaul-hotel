<?php

namespace Tests\Feature;

use App\Models\Facility;
use Tests\TestCase;

class ChatbotFaqTest extends TestCase
{
    public function test_chatbot_returns_questions_for_an_faq_category(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'Payment Information']);

        $response->assertOk()
            ->assertJsonPath('reply', 'Here are some common questions about Payment Information. Select a question below or type your own question.')
            ->assertJsonPath('quick_replies.0', 'What payment methods are accepted?');
    }

    public function test_room_type_reply_lists_only_deluxe_and_standard(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'What types of rooms are available?']);

        $response->assertOk()
            ->assertJsonPath('reply', "Casaul Hotel offers two room types:\n• Deluxe\n• Standard\n\nWould you like to view the available rooms?");
    }

    public function test_payment_methods_match_the_reservation_page_configuration(): void
    {
        $expectedReply = "Accepted payment methods:\n• " . implode("\n• ", config('reservation.payment_methods'));
        $response = $this->postJson('/chatbot/message', ['message' => 'What payment methods are accepted?']);

        $response->assertOk()->assertJsonPath('reply', $expectedReply);
    }

    public function test_cancellation_policy_matches_the_reservation_page_configuration(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'What is the cancellation policy?']);

        $response->assertOk()
            ->assertJsonPath('reply', 'Cancellation Policy: ' . config('reservation.cancellation_policy'));
    }

    public function test_hotel_policy_replies_are_short_and_accurate(): void
    {
        $expectedReplies = [
            'Are pets allowed?' => 'Pets are permitted at the hotel.',
            'Is smoking allowed?' => 'Smoking is permitted only in the designated area at the back of the hotel.',
            'Are visitors allowed?' => 'Visitors are permitted at the hotel.',
        ];

        foreach ($expectedReplies as $question => $reply) {
            $this->postJson('/chatbot/message', ['message' => $question])
                ->assertOk()
                ->assertJsonPath('reply', $reply);
        }
    }

    public function test_facilities_reply_lists_available_database_facilities_only(): void
    {
        Facility::query()->create(['name' => 'Chatbot Test Facility', 'status' => 'available']);
        Facility::query()->create(['name' => 'Unavailable Test Facility', 'status' => 'maintenance']);

        $response = $this->postJson('/chatbot/message', ['message' => 'Facilities']);
        $reply = $response->json('reply');

        $response->assertOk();
        $this->assertStringContainsString('Chatbot Test Facility', $reply);
        $this->assertStringNotContainsString('Unavailable Test Facility', $reply);
    }

    public function test_contact_us_displays_the_website_footer_contact_details(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'Contact Us']);

        $response->assertOk()
            ->assertJsonPath('reply', "CASAUL Hotel Tabaco\nMobile: (+63) 935 017 7564\nEmail: taba-roomsreservation@casahotels.com\nAddress: Tomas Cabiles St., Tabaco City\n\nCorporate Office\nTel. No.: (052) 203-0244 / (052) 203-0243\nEmail: inquiry@casaulhotels.com");
    }

    public function test_wifi_is_available_in_all_rooms(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'Is Wi-Fi available?']);

        $response->assertOk()
            ->assertJsonPath('reply', 'Yes. Wi-Fi is available in all rooms.');
    }

    public function test_housekeeping_prompt_preserves_a_back_navigation_state(): void
    {
        $response = $this->postJson('/chatbot/message', [
            'message' => 'Request Housekeeping',
            'action' => 'request_housekeeping',
        ]);

        $response->assertOk()->assertJsonPath('mode', 'request_housekeeping');
    }

    public function test_chatbot_answers_an_faq_question(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'What time is check-in?']);

        $response->assertOk()
            ->assertJsonPath('reply', 'Check-in time is 3:00 PM.')
            ->assertJsonPath('quick_replies.0', 'Reservations');
    }

    public function test_reservation_faq_links_to_the_reservation_page(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'How can I make a reservation?']);

        $response->assertOk()
            ->assertJsonPath('reply', 'You can make a reservation through [https://casaulhotel.com/reservation](/reservation). Select your preferred room, dates, and any extras before confirming.');
    }

    public function test_room_availability_faq_links_to_the_reservation_page(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'How can I check room availability?']);

        $response->assertOk()
            ->assertJsonPath('reply', 'You can ask me to show available rooms, or visit [https://casaulhotel.com/reservation](/reservation) and enter your preferred dates and number of guests.');
    }

    public function test_free_text_booking_reply_links_to_the_reservation_page(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'How do I book online?']);

        $response->assertOk()
            ->assertJsonPath('reply', 'You can make a reservation through [https://casaulhotel.com/reservation](/reservation). Select your preferred room, dates, and any extras such as facilities or dining before confirming.');
    }

    public function test_cancellation_faq_links_to_guest_profile_with_steps(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'How can I cancel my reservation?']);

        $response->assertOk()
            ->assertJsonPath('reply', "To cancel an eligible reservation, follow these steps:\n1. Sign in as a guest.\n2. Click the account icon at the top right and open [My Profile](/guest/profile).\n3. Select View Records, find your reservation, and choose Cancel if available. Otherwise, contact the front desk.");
    }

    public function test_payment_status_faq_links_to_guest_profile_with_steps(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'Where can I check my payment status?']);

        $response->assertOk()
            ->assertJsonPath('reply', "To check a payment, follow these steps:\n1. Sign in as a guest.\n2. Click the account icon and open [My Profile](/guest/profile).\n3. Select View Records and open the relevant reservation. You can also check My Receipts in the account menu or contact the front desk.");
    }

    public function test_receipt_faq_links_to_guest_profile_with_steps(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'Can I get a receipt?']);

        $response->assertOk()
            ->assertJsonPath('reply', "To check for a digital receipt, follow these steps:\n1. Sign in as a guest.\n2. Click the account icon and choose My Receipts if it is available.\n3. If you do not see My Receipts, open [My Profile](/guest/profile) and select View Records. Contact the front desk if you need help.");
    }

    public function test_free_text_reservation_status_links_to_guest_profile_with_steps(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'What is my reservation status?']);

        $response->assertOk()
            ->assertJsonPath('reply', "To check your reservation status, follow these steps:\n1. Sign in as a guest.\n2. Click the account icon at the top right and open [My Profile](/guest/profile).\n3. Select View Records and find your reservation. Contact the front desk if you need help. We also support room, facility, event, and dining reservations.");
    }
}