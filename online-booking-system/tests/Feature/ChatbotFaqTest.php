<?php

namespace Tests\Feature;

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

    public function test_chatbot_answers_an_faq_question(): void
    {
        $response = $this->postJson('/chatbot/message', ['message' => 'What time is check-in?']);

        $response->assertOk()
            ->assertJsonPath('reply', 'Our standard check-in time is 2:00 PM. If you need an earlier check-in, please contact the hotel or check availability with our staff.')
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