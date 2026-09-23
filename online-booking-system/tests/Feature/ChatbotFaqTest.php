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
}